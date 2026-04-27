<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get sorting parameters
        $sortBy = request('sort_by'); // No default, let it be null if not provided
        $order = request('order', 'desc'); // Default: desc
        
        // Build query with relationships
        $query = Asset::with('category');
        
        // Handle dynamic category sorting from dropdown first
        if ($sortBy && str_starts_with($sortBy, 'category_')) {
            $categoryId = str_replace('category_', '', $sortBy);
            $query->where('assets.category_id', $categoryId)
                  ->orderBy('assets.created_at', 'desc');
        } else {
            // Apply regular sorting if not category-specific
            switch ($sortBy) {
                case 'category':
                    // Handle specific category filtering
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
                    // Extract numeric part for proper numeric sorting
                    $query->orderByRaw('CAST(SUBSTRING(code, 5) AS UNSIGNED) ' . $order);
                    break;
                case 'name':
                    $query->orderBy('name', $order);
                    break;
                case 'created_at':
                    $query->orderBy('created_at', $order);
                    break;
                default:
                    // Only use latest if no sort_by parameter at all
                    if (!$sortBy) {
                        $query->latest();
                    }
                    break;
            }
        }
        
        $assets = $query->paginate(15);
        
        // Append sort parameters to pagination links
        $assets->appends(request()->only(['sort_by', 'order', 'category_id']));
        $categories = Category::withCount('assets')->orderBy('name')->get();
        
        // Calculate highest code numbers per category for instant code generation
        $categoryHighestCodes = [];
        foreach ($categories as $category) {
            $lastAsset = Asset::where('category_id', $category->id)
                ->orderByRaw('CAST(SUBSTRING(code, 5) AS INTEGER) DESC')
                ->first();
            
            if ($lastAsset) {
                // Extract number from code (handle both single and range codes)
                $codeParts = explode('-', $lastAsset->code);
                $highestNumber = (int)substr($codeParts[0], 4);
            } else {
                $highestNumber = 0;
            }
            
            $categoryHighestCodes[$category->id] = $highestNumber;
        }
        
        return view('assets.master-asset', compact('assets', 'categories', 'categoryHighestCodes'));
    }

    /**
     * Get next available code for a category
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
            $categoryPrefix = strtoupper(substr($category->name, 0, 4));
            
            // Get the highest existing asset number for this category
            $lastAsset = Asset::where('category_id', $categoryId)
                ->orderByRaw('CAST(SUBSTRING(code, 5) AS INTEGER) DESC')
                ->first();
            
            $lastNumber = 0;
            if ($lastAsset) {
                // Extract number from code (handle both single and range codes)
                $codeParts = explode('-', $lastAsset->code);
                $lastNumber = (int)substr($codeParts[0], 4);
            }
            
            $startNumber = $lastNumber + 1;
            
            // Generate single code for preview (individual rows will be created)
            $assetCode = $categoryPrefix . $startNumber;
            
            return response()->json([
                'success' => true,
                'code' => $assetCode,
                'prefix' => $categoryPrefix,
                'start_number' => $startNumber,
                'end_number' => $startNumber + $stock - 1,
                'message' => "Will create {$stock} individual items: {$assetCode}, {$categoryPrefix}" . ($startNumber + 1) . ($stock > 2 ? ", ..." : "")
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'stock' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $category = Category::findOrFail($request->category_id);
            $categoryPrefix = strtoupper(substr($category->name, 0, 4));
            
            // Get the highest existing asset number for this category
            $lastAsset = Asset::where('category_id', $request->category_id)
                ->orderByRaw('CAST(SUBSTRING(code, 5) AS INTEGER) DESC')
                ->first();
            
            $lastNumber = 0;
            if ($lastAsset) {
                // Extract number from code (handle both single and range codes)
                $codeParts = explode('-', $lastAsset->code);
                $lastNumber = (int)substr($codeParts[0], 4);
            }
            
            $stock = (int) $request->stock;
            $startNumber = $lastNumber + 1;
            
            // Create individual rows for each stock item
            $createdAssets = [];
            for ($i = 0; $i < $stock; $i++) {
                $currentNumber = $startNumber + $i;
                $assetCode = $categoryPrefix . $currentNumber;
                
                $assetData = $request->all();
                $assetData['code'] = $assetCode;
                $assetData['stock'] = 1; // Each individual item has stock of 1
                $assetData['condition'] = 'baik'; // Auto-set condition
                $assetData['status'] = 'tersedia'; // Auto-set status
                
                $asset = Asset::create($assetData);
                $createdAssets[] = $asset;
            }
            
            $message = $stock > 1 
                ? "Berhasil menambahkan {$stock} item individual"
                : 'Berhasil menambahkan';
            
            session()->flash('success', $message);
            
            return response()->json([
                'success' => true,
                'message' => "Successfully created {$stock} asset(s)",
                'data' => $createdAssets,
                'count' => $stock
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create asset: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        $asset->load('category');
        return response()->json([
            'success' => true,
            'data' => $asset
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:assets,code,' . $asset->id,
            'description' => 'nullable|string|max:1000',
            'stock' => 'required|integer|min:0',
            'condition' => 'required|string|max:20',
            'status' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $asset->update($request->all());
            
            session()->flash('success', 'Berhasil mengubah');
            
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
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            // Find asset manually to handle missing assets gracefully
            $asset = Asset::find($id);
            
            if (!$asset) {
                session()->flash('success', 'Berhasil menghapus');
                return response()->json([
                    'success' => true,
                    'message' => 'Asset already deleted or not found'
                ]);
            }
            
            $asset->delete();
            
            session()->flash('success', 'Berhasil menghapus');
            
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
     * Bulk delete assets.
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
            
            // Delete assets
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
}
