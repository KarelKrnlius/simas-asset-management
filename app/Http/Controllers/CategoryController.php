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
            $category->update($request->all());
            
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
            // Check if category has assets
            if ($category->assets()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category. It has associated assets.'
                ], 422);
            }

            $category->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category: ' . $e->getMessage()
            ], 500);
        }
    }
}
