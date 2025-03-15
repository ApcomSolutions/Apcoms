<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryService {

    /**
     * Get all categories with their subcategories
     *
     * @param bool $parentOnly Get only parent categories (with children loaded)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function GetAllCategories($parentOnly = false) {
        $query = Category::withCount('insights');

        if ($parentOnly) {
            $query->whereNull('parent_id')->with(['children' => function($query) {
                $query->withCount('insights')->orderBy('order');
            }]);
        }

        return $query->orderBy('order')->get();
    }

    /**
     * Get a category by ID with its subcategories
     *
     * @param int $id Category ID
     * @return Category
     */
    public function GetCategoriesById($id) {
        return Category::withCount('insights')
            ->with(['children' => function($query) {
                $query->withCount('insights')->orderBy('order');
            }])
            ->findOrFail($id);
    }

    /**
     * Get a category by slug with its subcategories
     *
     * @param string $slug Category slug
     * @return Category
     */
    public function GetCategoriesBySlug($slug) {
        return Category::withCount('insights')
            ->with(['children' => function($query) {
                $query->withCount('insights')->orderBy('order');
            }])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * Create a new category
     *
     * @param Request $request
     * @return Category
     */
    public function createCategories(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
            'order' => 'integer'
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name'], '-');
        }

        // Set default values for new fields
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        if (!isset($validated['order'])) {
            // Get the highest order within the same parent + 1
            $maxOrder = Category::where('parent_id', $validated['parent_id'] ?? null)
                ->max('order') ?? 0;
            $validated['order'] = $maxOrder + 1;
        }

        $category = Category::create($validated);

        return $category;
    }

    /**
     * Update an existing category
     *
     * @param Request $request
     * @param int $id
     * @return Category
     */
    public function updateCategories(Request $request, $id) {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', Rule::unique('categories')->ignore($id)],
            'description' => 'nullable|string',
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                function ($attribute, $value, $fail) use ($id) {
                    // Prevent category from being its own parent
                    if ($value == $id) {
                        $fail('A category cannot be its own parent.');
                    }

                    // Prevent circular references
                    if ($value) {
                        $parent = Category::find($value);
                        if ($parent && $parent->parent_id == $id) {
                            $fail('Circular reference detected. Category cannot be a parent of its parent.');
                        }
                    }
                },
            ],
            'is_active' => 'boolean',
            'order' => 'integer'
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name'], '-');
        }

        // Handle default values
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = $category->is_active;
        }

        $category->update($validated);

        return $category;
    }

    /**
     * Delete a category
     *
     * @param int $id
     * @return Category
     */
    public function deleteCategories($id) {
        $category = Category::findOrFail($id);

        // Handle child categories before deletion
        if ($category->children()->count() > 0) {
            // Option 1: Set children's parent_id to null (make them top-level)
            $category->children()->update(['parent_id' => null]);

            // Option 2: Delete all children (uncomment if desired)
            // $category->children()->delete();
        }

        $category->delete();

        return $category;
    }

    /**
     * Update the order of categories
     *
     * @param Request $request
     * @return array
     */
    public function updateCategoriesOrder(Request $request) {
        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.order' => 'required|integer'
        ]);

        foreach ($validated['categories'] as $item) {
            Category::where('id', $item['id'])
                ->update(['order' => $item['order']]);
        }

        return ['success' => true, 'message' => 'Category order updated successfully'];
    }
}
