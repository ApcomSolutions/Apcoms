<?php

namespace App\Services;

use App\Models\NewsCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NewsCategoryService
{
    /**
     * Get all news categories
     * @return Collection
     */
    public function getAllCategories()
    {
        return NewsCategory::withCount('news')->get();
    }

    /**
     * Get a specific category by ID
     * @param int $id
     * @return NewsCategory
     */
    public function getCategoryById($id)
    {
        return NewsCategory::withCount('news')->findOrFail($id);
    }

    /**
     * Get a specific category by slug
     * @param string $slug
     * @return NewsCategory
     */
    public function getCategoryBySlug($slug)
    {
        return NewsCategory::withCount('news')->where('slug', $slug)->firstOrFail();
    }

    /**
     * Create a new category
     * @param Request $request
     * @return NewsCategory
     */
    public function createCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:news_categories,slug',
            'description' => 'nullable|string',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name'], '-');
        }

        // Ensure slug is unique
        $count = NewsCategory::where('slug', $validated['slug'])->count();
        if ($count > 0) {
            $validated['slug'] = $validated['slug'] . '-' . uniqid();
        }

        return NewsCategory::create($validated);
    }

    /**
     * Update an existing category
     * @param Request $request
     * @param int $id
     * @return NewsCategory
     */
    public function updateCategory(Request $request, $id)
    {
        $category = NewsCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', Rule::unique('news_categories')->ignore($id)],
            'description' => 'nullable|string',
        ]);

        // Auto-generate slug if not provided or if name has changed and slug wasn't manually set
        if (empty($validated['slug']) || ($category->name != $validated['name'] && !$request->has('slug'))) {
            $validated['slug'] = Str::slug($validated['name'], '-');

            // Ensure slug is unique
            $count = NewsCategory::where('slug', $validated['slug'])->where('id', '!=', $id)->count();
            if ($count > 0) {
                $validated['slug'] = $validated['slug'] . '-' . uniqid();
            }
        }

        $category->update($validated);

        return $category->fresh();
    }

    /**
     * Delete a category
     * @param int $id
     * @return NewsCategory
     */
    public function deleteCategory($id)
    {
        $category = NewsCategory::findOrFail($id);

        // Store a copy of category data before deletion
        $categoryData = $category->toArray();

        $category->delete();

        return (object) $categoryData;
    }

    /**
     * Get categories with custom format
     * @return array
     */
    public function getCategoriesForDropdown()
    {
        return NewsCategory::withCount('news')
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'news_count' => $category->news_count,
                    'label' => $category->name . ' (' . $category->news_count . ')',
                    'value' => $category->id
                ];
            });
    }

    /**
     * Get popular categories (with the most news articles)
     * @param int $limit
     * @return Collection
     */
    public function getPopularCategories($limit = 5)
    {
        return NewsCategory::withCount('news')
            ->having('news_count', '>', 0)
            ->orderBy('news_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get all categories with their news articles
     * @return Collection
     */
    public function getCategoriesWithNews()
    {
        return NewsCategory::with(['news' => function ($query) {
            $query->orderBy('publish_date', 'desc');
        }])
            ->withCount('news')
            ->having('news_count', '>', 0)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get a category with its news articles
     * @param string $slug
     * @param int $newsLimit
     * @return object
     */
    public function getCategoryWithNews($slug, $newsLimit = 10)
    {
        $category = NewsCategory::with(['news' => function ($query) use ($newsLimit) {
            $query->orderBy('publish_date', 'desc')->limit($newsLimit);
        }])
            ->withCount('news')
            ->where('slug', $slug)
            ->firstOrFail();

        return $category;
    }

    /**
     * Update multiple categories' order
     * @param array $orderedIds
     * @return bool
     */
    public function updateCategoriesOrder(array $orderedIds)
    {
        $order = 0;

        foreach ($orderedIds as $id) {
            $category = NewsCategory::find($id);
            if ($category) {
                $category->update(['order' => $order]);
                $order++;
            }
        }

        return true;
    }

    /**
     * Search categories by name or description
     * @param string $query
     * @return Collection
     */
    public function searchCategories($query)
    {
        return NewsCategory::withCount('news')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orderBy('name')
            ->get();
    }
}
