<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NewsCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewsCategoryController extends Controller
{
    protected $categoryService;

    public function __construct(NewsCategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of all categories
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showAllCategories()
    {
        $categories = $this->categoryService->getAllCategories();
        return response()->json($categories, 200);
    }

    /**
     * Display the specified category by ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showCategoryById($id)
    {
        $category = $this->categoryService->getCategoryById($id);
        return response()->json($category, 200);
    }

    /**
     * Display the specified category by slug
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function showCategoryBySlug($slug)
    {
        $category = $this->categoryService->getCategoryBySlug($slug);
        return response()->json($category, 200);
    }

    /**
     * Store a newly created category
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $category = $this->categoryService->createCategory($request);

            return response()->json([
                'message' => 'Category successfully created',
                'data' => $category
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', ['errors' => $e->errors()]);
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating category:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'message' => 'Error creating category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified category
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $category = $this->categoryService->updateCategory($request, $id);

            return response()->json([
                'message' => 'Category successfully updated',
                'data' => $category
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', ['errors' => $e->errors()]);
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating category:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'message' => 'Error updating category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified category
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Check if category has news
            $category = $this->categoryService->getCategoryById($id);
            if ($category->news_count > 0) {
                return response()->json([
                    'message' => 'Cannot delete a category that has associated news articles'
                ], 422);
            }

            $category = $this->categoryService->deleteCategory($id);

            return response()->json([
                'message' => 'Category successfully deleted',
                'data' => $category
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting category:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'message' => 'Error deleting category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get categories formatted for dropdown
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategoriesForDropdown()
    {
        $categories = $this->categoryService->getCategoriesForDropdown();
        return response()->json($categories, 200);
    }

    /**
     * Get popular categories
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPopularCategories(Request $request)
    {
        $limit = $request->input('limit', 5);
        $categories = $this->categoryService->getPopularCategories($limit);
        return response()->json($categories, 200);
    }

    /**
     * Get all categories with their news
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategoriesWithNews()
    {
        $categories = $this->categoryService->getCategoriesWithNews();
        return response()->json($categories, 200);
    }

    /**
     * Get a specific category with its news
     *
     * @param string $slug
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategoryWithNews($slug, Request $request)
    {
        $newsLimit = $request->input('news_limit', 10);
        $category = $this->categoryService->getCategoryWithNews($slug, $newsLimit);
        return response()->json($category, 200);
    }

    /**
     * Update categories order
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCategoriesOrder(Request $request)
    {
        $validated = $request->validate([
            'ordered_ids' => 'required|array',
            'ordered_ids.*' => 'integer|exists:news_categories,id'
        ]);

        $success = $this->categoryService->updateCategoriesOrder($validated['ordered_ids']);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Categories order updated successfully' : 'Failed to update categories order'
        ], $success ? 200 : 400);
    }

    /**
     * Search categories
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchCategories(Request $request)
    {
        $query = $request->input('query', '');
        if (empty($query)) {
            return $this->showAllCategories();
        }

        $categories = $this->categoryService->searchCategories($query);
        return response()->json([
            'query' => $query,
            'total' => count($categories),
            'data' => $categories
        ], 200);
    }
}
