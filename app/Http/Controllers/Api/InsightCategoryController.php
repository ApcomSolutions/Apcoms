<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class InsightCategoryController extends Controller {

    protected $categoryService;

    public function __construct(CategoryService $categoryService){
        $this->categoryService = $categoryService;
    }

    /**
     * Get all categories
     */
    public function showAllCategory(Request $request){
        $parentOnly = $request->has('parent_only') ? filter_var($request->parent_only, FILTER_VALIDATE_BOOLEAN) : false;
        $categories = $this->categoryService->GetAllCategories($parentOnly);
        return response()->json($categories, 200);
    }

    /**
     * Get category by ID
     */
    public function showCategoryById($id){
        $category = $this->categoryService->GetCategoriesById($id);
        return response()->json($category, 200);
    }

    /**
     * Get category by slug
     */
    public function showCategoryBySlug($slug){
        $category = $this->categoryService->GetCategoriesBySlug($slug);
        return response()->json($category, 200);
    }

    /**
     * Create new category
     */
    public function store(Request $request)
    {
        $category = $this->categoryService->createCategories($request);

        return response()->json([
            'message' => 'Category successfully created',
            'data' => $category
        ], 201);
    }

    /**
     * Update existing category
     */
    public function update(Request $request, $id){
        $category = $this->categoryService->updateCategories($request, $id);

        return response()->json([
            'message' => 'Category successfully updated',
            'data' => $category
        ], 200);
    }

    /**
     * Delete category
     */
    public function destroy($id){
        $category = $this->categoryService->deleteCategories($id);

        return response()->json([
            'message' => 'Category successfully deleted',
            'data' => $category
        ], 200);
    }

    /**
     * Update the order of categories
     */
    public function updateCategoriesOrder(Request $request){
        $result = $this->categoryService->updateCategoriesOrder($request);

        return response()->json($result, 200);
    }

    /**
     * Get subcategories for a specific parent
     */
    public function getSubcategories($parentId){
        $parent = $this->categoryService->GetCategoriesById($parentId);

        return response()->json([
            'parent' => [
                'id' => $parent->id,
                'name' => $parent->name,
                'slug' => $parent->slug
            ],
            'subcategories' => $parent->children
        ], 200);
    }
}
