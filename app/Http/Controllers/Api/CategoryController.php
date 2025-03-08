<?php

namespace App\Http\Controllers\Api;



use App\Services\CategoryService;
use Illuminate\Http\Request;


class CategoryController{

    protected $categoryService;

    public function __construct(CategoryService $categoryService){
        $this->categoryService = $categoryService;
    }

    public function showAllCategory(){
        $categories = $this->categoryService->GetAllCategories();
       return response()->json($categories, 200);
    }

    public function showCategoryById($id){
        $categories = $this->categoryService->GetCategoriesById($id);
        return response()->json($categories, 200);
    }

    public function store(Request $request)
    {
        $categories = $this->categoryService->createCategories($request);

        return response()->json([
            'message' => 'Insight berhasil dibuat',
            'data' => $categories
        ], 201);
    }

    public function update(Request $request, $id){
        $categories = $this->categoryService->GetCategoriesById($id);
        $categories = $this->categoryService->updateCategories($request, $categories);
        return response()->json([
            'message' => 'Insight berhasil diubah',
            'data' => $categories
        ], 200);
    }

    public function destroy($id){
        $categories = $this->categoryService->GetCategoriesById($id);
        $categories = $this->categoryService->DeleteCategories($categories);
        return response()->json([
            'message' => 'Insight berhasil dihapus',
            'data' => $categories
        ], 200);
    }
}
