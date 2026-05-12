<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get sorting parameters
        $sortBy = request('sort_by', 'code');
        $order = request('order', 'asc');
        $search = request('search');

        // Build query with relationships
        $query = Asset::with('category');

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(code) LIKE ?', ['%' . strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
                  ->orWhereHas('category', function($catQ) use ($search) {
                      $catQ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
                  })
                  ->orWhereRaw('LOWER(condition) LIKE ?', ['%' . strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(status) LIKE ?', ['%' . strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($search) . '%']);
            });
        }

        // Handle category sorting
        if ($sortBy && str_starts_with($sortBy, 'category_')) {
            $categoryId = str_replace('category_', '', $sortBy);
            $query->where('assets.category_id', $categoryId)
                  ->orderBy('assets.created_at', 'desc');
        } else {
            switch ($sortBy) {
                case 'latest':
                    $query->orderBy('created_at', 'desc');
                    break;

                case 'category':
                    if (request('category_id')) {
                        $query->where('assets.category_id', request('category_id'))
                              ->orderBy('assets.created_at', 'desc');
                    } else {
                        $query->join('categories', 'assets.category_id', '=', 'categories.id')
                              ->orderBy('categories.name', $order)
                              ->select('assets.*');
                    }
                    break;

                case 'code':
                    $query->orderByRaw("NULLIF(REGEXP_REPLACE(SUBSTRING(code, 5), '[^0-9].*', ''), '')::int {$order}");
                    break;

                case 'name':
                    $query->orderBy('name', $order);
                    break;

                case 'created_at':
                    $query->orderBy('created_at', $order);
                    break;

                default:
                    if (!$sortBy) {
                        $query->latest();
                    }
                    break;
            }
        }

        $assets = $query->paginate(15);

        $assets->appends(request()->only(['search', 'sort_by', 'order', 'category_id']));
        $categories = Category::withCount('assets')->orderBy('name')->get();

        $categoryHighestCodes = [];

        foreach ($categories as $category) {
            $lastAsset = Asset::where('category_id', $category->id)
                ->whereRaw("SUBSTRING(code, 5) ~ '^[0-9]+'")
                ->orderByRaw("NULLIF(REGEXP_REPLACE(SUBSTRING(code, 5), '[^0-9].*', ''), '')::int DESC")
                ->first();

            if ($lastAsset) {
                $codeParts = explode('-', $lastAsset->code);
                $highestNumber = (int)preg_replace('/[^0-9]/', '', $codeParts[0]);
            } else {
                $highestNumber = 0;
            }

            $categoryHighestCodes[$category->id] = $highestNumber;
        }

        return view('assets.master-asset', compact('assets', 'categories', 'categoryHighestCodes'));
    }

    /**
     * Get next available code
     */
    public function getNextCode(Request $request)
    {
        $categoryId = $request->query('category_id');
        $stock = (int) $request->query('stock') ?: 1;

        if (!$categoryId) {
            return response()->json([
                'success' => false,
                'message' => 'Category ID is required'
            ], 400);
        }

        try {
            $category = Category::findOrFail($categoryId);

            if (!$category->category_code) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori belum memiliki category_code'
                ], 400);
            }

            $categoryCode = $category->category_code;

            $lastAsset = Asset::orderBy('id', 'desc')->first();
            $lastNumber = 0;

            if ($lastAsset && strlen($lastAsset->code) >= 6) {
                $lastNumber = (int) substr($lastAsset->code, -6);
            }

            $nextNumber = $lastNumber + 1;
            $sequence = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            $assetCode = 'BRIN-' . $categoryCode . '-' . $sequence;

            return response()->json([
                'success' => true,
                'code' => $assetCode,
                'category_code' => $categoryCode,
                'sequence' => $nextNumber,
                'last_number' => $lastNumber,
                'message' => "Next asset code: {$assetCode}"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store asset
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'stock' => 'required|integer|min:0',
            'condition' => 'required|string|max:20',
            'status' => 'required|in:tersedia,dipinjam,diperbaiki',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $photoPath = null;

            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $photoName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();

                $photoPath = $photo->storeAs('assets', $photoName, 'rustfs');

                if (!$photoPath) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload photo to RustFS'
                    ], 500);
                }
            }

            $category = Category::findOrFail($request->category_id);

            if (!$category->category_code) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori belum memiliki category_code'
                ], 400);
            }

            $categoryCode = $category->category_code;
            $stock = (int) $request->stock;

            $createdAssets = [];

            for ($i = 0; $i < $stock; $i++) {

                $lastAsset = Asset::orderBy('id', 'desc')->first();
                $lastNumber = 0;

                if ($lastAsset && strlen($lastAsset->code) >= 6) {
                    $lastNumber = (int) substr($lastAsset->code, -6);
                }

                $nextNumber = $lastNumber + 1;
                $sequence = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

                $assetCode = 'BRIN-' . $categoryCode . '-' . $sequence;

                $assetData = $request->except(['_token', '_method', 'photo']);

                $assetData['code'] = $assetCode;
                $assetData['stock'] = 1;
                $assetData['condition'] = 'baik';
                $assetData['status'] = 'tersedia';
                $assetData['photo'] = $photoPath;

                $asset = Asset::create($assetData);
                $createdAssets[] = $asset;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully created {$stock} asset(s)",
                'data' => $createdAssets,
                'count' => $stock
            ]);

        } catch (\Exception $e) {

            if (isset($photoPath) && $photoPath) {
                Storage::disk('rustfs')->delete($photoPath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to create asset: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show asset
     */
    public function show(Asset $asset)
    {
        $asset->load('category');

        $photoUrl = null;

        if ($asset->photo) {
            $photoUrl = \App\Helpers\AssetHelper::getPhotoUrl($asset->photo);
        }

        return response()->json([
            'success' => true,
            'data' => $asset,
            'photo_url' => $photoUrl
        ]);
    }

    /**
     * Update asset
     */
    public function update(Request $request, Asset $asset)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:assets,code,' . $asset->id,
            'description' => 'nullable|string|max:500',
            'stock' => 'required|integer|min:0',
            'condition' => 'required|string|max:20',
            'status' => 'required|in:tersedia,dipinjam,perlu_perbaikan,tidak_tersedia',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $updateData = $request->except(['_token', '_method', 'photo', 'current_photo']);

            if ($request->hasFile('photo')) {

                if ($asset->photo) {
                    try {
                        if (Storage::disk('rustfs')->exists($asset->photo)) {
                            Storage::disk('rustfs')->delete($asset->photo);
                        }
                    } catch (\Exception $e) {
                    }
                }

                $photo = $request->file('photo');
                $photoName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();

                $photoPath = $photo->storeAs('assets', $photoName, 'rustfs');

                if ($photoPath) {
                    $updateData['photo'] = $photoPath;
                }
            }

            $asset->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Asset updated successfully',
                'data' => $asset
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to update asset: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete asset
     */
    public function destroy(Request $request, $id)
    {
        try {

            $asset = Asset::find($id);

            if (!$asset) {
                return response()->json([
                    'success' => true,
                    'message' => 'Asset already deleted or not found'
                ]);
            }

            $asset->delete();

            return response()->json([
                'success' => true,
                'message' => 'Asset deleted successfully'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete asset: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete assets
     */
    public function bulkDelete(Request $request)
    {
        try {

            $assetIds = json_decode($request->asset_ids, true);

            if (!is_array($assetIds) || empty($assetIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No assets selected for deletion'
                ], 400);
            }

            $deletedCount = Asset::whereIn('id', $assetIds)->delete();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$deletedCount} aset",
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete assets: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export asset CSV
     */
    public function export()
    {
        $assets = Asset::with('category')->get();

        return response()->streamDownload(function () use ($assets) {

            $file = fopen('php://output', 'w');

            // Header CSV
            fputcsv($file, [
                'Kode',
                'Nama Asset',
                'Kategori',
                'Stok',
                'Kondisi',
                'Status',
                'Deskripsi'
            ]);

            // Isi data
            foreach ($assets as $asset) {

                fputcsv($file, [
                    $asset->code,
                    $asset->name,
                    $asset->category->name ?? '-',
                    $asset->stock,
                    $asset->condition,
                    $asset->status,
                    $asset->description
                ]);
            }

            fclose($file);

        }, 'data-asset.csv');
    }
}