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
            // Auto-generate category_code
            $maxCode = Category::whereNotNull('category_code')
                ->where('category_code', '!=', '')
                ->get(['category_code'])
                ->filter(function ($category) {
                    return is_numeric($category->category_code);
                })
                ->max(function ($category) {
                    return (int)$category->category_code;
                });

            if ($maxCode) {
                $nextCode = $maxCode + 1;
            } else {
                $nextCode = 1;
            }

            $categoryCode = str_pad($nextCode, 2, '0', STR_PAD_LEFT);

            $category = Category::create([
                'name' => $request->name,
                'description' => $request->description,
                'category_code' => $categoryCode,
            ]);

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
            
            // Category update should NOT change asset codes
            // Asset codes should remain in BRIN-XX-YYYYYY format
            $message = 'Kategori berhasil diperbarui.';
            
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
