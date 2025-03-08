<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryService {

    public function GetAllCategories() {
        return Category::all();
    }

    public function GetCategoriesById($id) {
        return Category::findOrFail($id);
    }

    public function createCategories(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:categories,slug',
            'description' => 'required|string',
        ]);

        return Category::create($validated);
    }

    public function updateCategories(Request $request, $id) {
        $categories = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', Rule::unique('categories')->ignore($id)],
            'description' => 'required|string',
        ]);

        $categories->update($validated);

        return $categories;
    }

    public function deleteCategories($id) {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Category Berhasil Dihapus']);
    }
}
