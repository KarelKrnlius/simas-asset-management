<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('assets')->latest()->paginate(10);
        return view('categories.index', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Custom validation for space-insensitive unique check
        $existingCategories = Category::pluck('name')->toArray();
        $inputName = preg_replace('/\s+/', '', strtolower($request->name));
        
        foreach ($existingCategories as $existingName) {
            if (preg_replace('/\s+/', '', strtolower($existingName)) === $inputName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori sudah ada!'
                ], 422);
            }
        }
        
        // Regular validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $category = Category::create($request->all());
            
            session()->flash('success', 'Berhasil');
            
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load('assets');
        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $oldName = $category->name;
            $category->update($request->all());
            
            // If category name changed, update only the prefix of asset codes
            if ($oldName !== $request->name) {
                $newPrefix = strtoupper(substr($request->name, 0, 4));
                
                // Get all assets in this category
                $assets = $category->assets()->get();
                $updatedCount = 0;
                
                foreach ($assets as $asset) {
                    // Extract the number from existing code
                    $existingCode = $asset->code;
                    $number = preg_replace('/[^0-9]/', '', $existingCode);
                    
                    // Create new code with same number but new prefix
                    $newCode = $newPrefix . $number;
                    
                    // Only update if the code is different
                    if ($asset->code !== $newCode) {
                        $asset->update(['code' => $newCode]);
                        $updatedCount++;
                    }
                }
                
                $message = $updatedCount > 0 
                    ? "Kategori berhasil diperbarui. {$updatedCount} kode aset telah diperbarui."
                    : 'Kategori berhasil diperbarui. Tidak ada perubahan kode aset.';
                
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $category,
                    'codes_updated' => $updatedCount
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            // Get asset count before deletion
            $assetCount = $category->assets()->count();
            
            // Delete all assets in this category first
            $category->assets()->delete();
            
            // Then delete the category
            $category->delete();
            
            $message = $assetCount > 0 
                ? "Kategori dan {$assetCount} aset berhasil dihapus"
                : 'Kategori berhasil dihapus';
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_assets' => $assetCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category: ' . $e->getMessage()
            ], 500);
        }
    }
}
