<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryService {

    public function GetAllCategories() {
        return Category::withCount('insights')->get();
    }

    public function GetCategoriesById($id) {
        return Category::withCount('insights')->findOrFail($id);
    }

    public function GetCategoriesBySlug($slug) {
        return Category::withCount('insights')->where('slug', $slug)->firstOrFail();
    }

    public function createCategories(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:categories,slug',
            'description' => 'nullable|string',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name'], '-');
        }

        $category = Category::create($validated);

        return $category;
    }

    public function updateCategories(Request $request, $id) {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', Rule::unique('categories')->ignore($id)],
            'description' => 'nullable|string',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name'], '-');
        }

        $category->update($validated);

        return $category;
    }

    public function deleteCategories($id) {
        $category = Category::findOrFail($id);
        $category->delete();

        return $category;
    }
}
