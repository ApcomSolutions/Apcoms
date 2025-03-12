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

    public function showAllCategory(){
        $categories = $this->categoryService->GetAllCategories();
        return response()->json($categories, 200);
    }

    public function showCategoryById($id){
        $category = $this->categoryService->GetCategoriesById($id);
        return response()->json($category, 200);
    }

    public function showCategoryBySlug($slug){
        $category = $this->categoryService->GetCategoriesBySlug($slug);
        return response()->json($category, 200);
    }

    public function store(Request $request)
    {
        $category = $this->categoryService->createCategories($request);

        return response()->json([
            'message' => 'Category successfully created',
            'data' => $category
        ], 201);
    }

    public function update(Request $request, $id){
        $category = $this->categoryService->updateCategories($request, $id);

        return response()->json([
            'message' => 'Category successfully updated',
            'data' => $category
        ], 200);
    }

    public function destroy($id){
        $category = $this->categoryService->deleteCategories($id);

        return response()->json([
            'message' => 'Category successfully deleted',
            'data' => $category
        ], 200);
    }
}
