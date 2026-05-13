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
        $sortBy = request('sort_by', 'code'); // Default: sort by code
        $order = request('order', 'asc'); // Default: asc
        $search = request('search'); // Search parameter
        
        // Build query with relationships
        $query = Asset::with('category');
        
        // Apply search filter (case-insensitive) - search in code, name, category, condition, status, description
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
        
        // Handle dynamic category sorting from dropdown first
        if ($sortBy && str_starts_with($sortBy, 'category_')) {
            $categoryId = str_replace('category_', '', $sortBy);
            $query->where('assets.category_id', $categoryId)
                  ->orderBy('assets.created_at', 'desc');
        } else {
            // Apply regular sorting if not category-specific
            switch ($sortBy) {
                case 'latest':
                    $query->orderBy('created_at', 'desc');
                    break;
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
                    // Sort berdasarkan angka terakhir di kode (BRIN-01-000001, BRIN-01-000002, dst)
                    $query->orderByRaw("(SUBSTRING(code FROM '\\d{6}$'))::INTEGER {$order}");
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
        
        // Append search and sort parameters to pagination links
        $assets->appends(request()->only(['search', 'sort_by', 'order', 'category_id']));
        $categories = Category::withCount('assets')->orderBy('name')->get();
        
        // Calculate highest code numbers per category for instant code generation
        $categoryHighestCodes = [];
        foreach ($categories as $category) {
            $lastAsset = Asset::where('category_id', $category->id)
                ->whereRaw("SUBSTRING(code, 5) ~ '^[0-9]+'")
                ->orderByRaw("NULLIF(REGEXP_REPLACE(SUBSTRING(code, 5), '[^0-9].*', ''), '')::int DESC")
                ->first();
            
            if ($lastAsset) {
                // Extract number from code (handle both single and range codes)
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
            
            if (!$category->category_code) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori belum memiliki category_code'
                ], 400);
            }
            
            $categoryCode = $category->category_code;
            
            // Reliable Sequence Logic: Get last asset and extract sequence
            $lastAsset = Asset::orderBy('id', 'desc')->first();
            $lastNumber = 0;
            
            if ($lastAsset && strlen($lastAsset->code) >= 6) {
                // Extract last 6 digits from BRIN-XX-YYYYYY format
                $lastNumber = (int) substr($lastAsset->code, -6);
            }
            
            $nextNumber = $lastNumber + 1;
            $sequence = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            
            // Generate code in BRIN-XX-YYYYYY format with reliable sequence
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'condition' => 'required|string|max:20',
            'status' => 'required|in:tersedia,dipinjam,diperbaiki',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Foto WAJIB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Handle photo upload - SIMPAN KE RUSTFS
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $photoName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                
                // Upload ke RustFS (object storage)
                $photoPath = $photo->storeAs('assets', $photoName, 'rustfs');
                
                // Jika upload gagal, return error
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
            
            // Set stock to 1 automatically
            $stock = 1;
            
            // Create single asset with stock = 1
            // Atomic Reliable Sequence Logic: Get last asset right before creation
            $lastAsset = Asset::orderBy('id', 'desc')->first();
            $lastNumber = 0;
            
            if ($lastAsset && strlen($lastAsset->code) >= 6) {
                // Extract last 6 digits from BRIN-XX-YYYYYY format
                $lastNumber = (int) substr($lastAsset->code, -6);
            }
            
            $nextNumber = $lastNumber + 1;
            $sequence = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            
            $assetCode = 'BRIN-' . $categoryCode . '-' . $sequence;
            
            $assetData = $request->except(['_token', '_method', 'photo']);
            $assetData['code'] = $assetCode;
            $assetData['stock'] = 1; // Auto-set stock to 1
            $assetData['condition'] = 'baik'; // Auto-set condition
            $assetData['status'] = 'tersedia'; // Auto-set status
            $assetData['photo'] = $photoPath; // Add photo path
            
            $asset = Asset::create($assetData);
            
            $message = 'Berhasil menambahkan asset baru';
            
            session()->flash('success', $message);
            
            return response()->json([
                'success' => true,
                'message' => 'Asset berhasil ditambahkan',
                'data' => $asset
            ]);
        } catch (\Exception $e) {
            // Jika ada error, hapus foto yang sudah diupload
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
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        $asset->load('category');
        
        // Get photo URL (support both local and RustFS)
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
     * Update the specified resource in storage.
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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validasi foto opsional
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
            
            // Handle photo upload - SIMPAN KE RUSTFS
            if ($request->hasFile('photo')) {
                // Hapus foto lama jika ada DAN tersimpan di RustFS
                if ($asset->photo) {
                    try {
                        if (Storage::disk('rustfs')->exists($asset->photo)) {
                            Storage::disk('rustfs')->delete($asset->photo);
                        }
                    } catch (\Exception $e) {
                        // Ignore error jika foto lama tidak bisa dihapus
                    }
                }
                
                // Upload foto baru ke RustFS
                $photo = $request->file('photo');
                $photoName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $photoPath = $photo->storeAs('assets', $photoName, 'rustfs');
                
                if ($photoPath) {
                    $updateData['photo'] = $photoPath;
                }
            }
            
            $asset->update($updateData);
            
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
     * Export assets to Excel
     */
    public function export()
    {
        try {
            // Get all assets with category
            $assets = Asset::with('category')->orderBy('code')->get();
            
            // Create Excel content with proper formatting
            $filename = 'master_asset_' . date('Y-m-d_His') . '.xls';
            
            $headers = [
                'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];
            
            $callback = function() use ($assets) {
                // Start HTML table with styling
                echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
                echo '<head>';
                echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
                echo '<style>';
                echo 'table { border-collapse: collapse; width: 100%; }';
                echo 'th { background-color: #E11D48; color: white; font-weight: bold; padding: 12px; text-align: left; border: 1px solid #ddd; }';
                echo 'td { padding: 10px; border: 1px solid #ddd; }';
                echo 'tr:nth-child(even) { background-color: #f9fafb; }';
                echo 'tr:hover { background-color: #f1f5f9; }';
                echo '</style>';
                echo '</head>';
                echo '<body>';
                
                echo '<h2 style="color: #E11D48; font-family: Arial, sans-serif;">Master Asset - SIMAS</h2>';
                echo '<p style="font-family: Arial, sans-serif; color: #64748b;">Exported on: ' . date('d F Y H:i:s') . '</p>';
                
                echo '<table border="1">';
                
                // Header row
                echo '<thead><tr>';
                echo '<th>No</th>';
                echo '<th>Kode Asset</th>';
                echo '<th>Nama Asset</th>';
                echo '<th>Kategori</th>';
                echo '<th>Deskripsi</th>';
                echo '<th>Stok Tersedia</th>';
                echo '<th>Stok Keluar</th>';
                echo '<th>Kondisi</th>';
                echo '<th>Status Asset</th>';
                echo '<th>Link Barcode QR</th>';
                echo '</tr></thead>';
                
                // Data rows
                echo '<tbody>';
                $no = 1;
                foreach ($assets as $asset) {
                    // Calculate stok keluar (assuming initial stock was always 1 per asset)
                    $stokKeluar = ($asset->stock == 0) ? 1 : 0;
                    
                    // Generate QR code URL with ngrok domain
                    $qrUrl = 'https://magnifier-sinner-unsettled.ngrok-free.dev/asset/' . $asset->code;
                    
                    echo '<tr>';
                    echo '<td>' . $no++ . '</td>';
                    echo '<td>' . htmlspecialchars($asset->code) . '</td>';
                    echo '<td>' . htmlspecialchars($asset->name) . '</td>';
                    echo '<td>' . htmlspecialchars($asset->category->name ?? '-') . '</td>';
                    echo '<td>' . htmlspecialchars($asset->description ?? '-') . '</td>';
                    echo '<td style="text-align: center;">' . $asset->stock . '</td>';
                    echo '<td style="text-align: center;">' . $stokKeluar . '</td>';
                    echo '<td>' . ucfirst($asset->condition) . '</td>';
                    echo '<td>' . str_replace('_', ' ', ucfirst($asset->status)) . '</td>';
                    echo '<td><a href="' . $qrUrl . '" target="_blank">' . $qrUrl . '</a></td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                
                echo '</table>';
                echo '</body>';
                echo '</html>';
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal export data: ' . $e->getMessage());
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
            
            // Check if any assets are currently being borrowed
            $assetsInUse = \DB::table('loan_details')
                ->join('loans', 'loan_details.loan_id', '=', 'loans.id')
                ->whereIn('loan_details.asset_id', $assetIds)
                ->where('loans.status', 'dipinjam')
                ->whereNull('loans.deleted_at')
                ->count();
            
            if ($assetsInUse > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus asset yang sedang dipinjam. Silakan kembalikan asset terlebih dahulu.'
                ], 400);
            }
            
            // Delete loan_details records for returned loans
            \DB::table('loan_details')
                ->whereIn('asset_id', $assetIds)
                ->delete();
            
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
