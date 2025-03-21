<?php

namespace App\Services;

use App\Models\Insight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InsightService
{
    public function getAllInsights() {
        return Insight::with('category')
            ->withCount('trackings as view_count')
            ->get()
            ->map(function ($insight) {
                return [
                    'id' => $insight->id,
                    'judul' => $insight->judul,
                    'slug' => $insight->slug,
                    'isi' => $insight->isi,
                    'image_url' => $insight->image_url,
                    'penulis' => $insight->penulis,
                    'TanggalTerbit' => $insight->TanggalTerbit,
                    'category_id' => $insight->category_id,
                    'category_name' => $insight->category ? $insight->category->name : null,
                    'view_count' => $insight->view_count,
                ];
            });
    }

    public function getInsightById($id) {
        $insight = Insight::with('category')
            ->withCount('trackings as view_count') // Add view count
            ->findOrFail($id);

        return [
            'id' => $insight->id,
            'judul' => $insight->judul,
            'slug' => $insight->slug,
            'isi' => $insight->isi,
            'image_url' => $insight->image_url,
            'penulis' => $insight->penulis,
            'TanggalTerbit' => $insight->TanggalTerbit,
            'category_id' => $insight->category_id,
            'category_name' => $insight->category ? $insight->category->name : null,
            'view_count' => $insight->view_count, // Include view count
        ];
    }

    public function getInsightBySlug($slug) {
        $insight = Insight::with('category')
            ->withCount('trackings as view_count') // Add view count
            ->where('slug', $slug)
            ->firstOrFail();

        return [
            'id' => $insight->id,
            'judul' => $insight->judul,
            'slug' => $insight->slug,
            'isi' => $insight->isi,
            'image_url' => $insight->image_url,
            'penulis' => $insight->penulis,
            'TanggalTerbit' => $insight->TanggalTerbit,
            'category_id' => $insight->category_id,
            'category_name' => $insight->category ? $insight->category->name : null,
            'view_count' => $insight->view_count, // Include view count
        ];
    }

    public function createInsight(Request $request) {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'slug' => 'required|string|unique:insights,slug',
            'isi' => 'required|string',
            'penulis' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'TanggalTerbit' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $insightData = collect($validated)->except('image')->toArray();

        // Upload image jika ada
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('insights', 'public');
            $insightData['image_url'] = '/storage/' . $path;
        }

        $insight = Insight::create($insightData);
        return $this->getInsightById($insight->id);
    }

    public function updateInsight(Request $request, $slug) {
        $insight = Insight::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'slug' => 'required|string|unique:insights,slug,'.$insight->id,
            'isi' => 'required|string',
            'penulis' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'TanggalTerbit' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
            'delete_image' => 'nullable|boolean',
        ]);

        $insightData = collect($validated)->except(['image', 'delete_image'])->toArray();

        // Handle image deletion if requested
        if ($request->boolean('delete_image') && $insight->image_url) {
            // Delete the image from storage
            $oldPath = str_replace('/storage/', '', $insight->image_url);
            Storage::disk('public')->delete($oldPath);

            // Set image_url to null in the database
            $insightData['image_url'] = null;
        }
        // Upload gambar baru jika ada
        elseif ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($insight->image_url) {
                $oldPath = str_replace('/storage/', '', $insight->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('insights', 'public');
            $insightData['image_url'] = '/storage/' . $path;
        }

        $insight->update($insightData);
        return $this->getInsightById($insight->id);
    }

    public function deleteInsight($slug) {
        $insight = Insight::where('slug', $slug)->firstOrFail();

        // Hapus gambar jika ada
        if ($insight->image_url) {
            $path = str_replace('/storage/', '', $insight->image_url);
            Storage::disk('public')->delete($path);
        }

        $insight->delete();
        return ['message' => 'Insight berhasil dihapus'];
    }

    /**
     * Search for insights based on a query string
     * @param string $query
     * @return array
     */
    public function searchInsights($query)
    {
        return Insight::with('category')
            ->withCount('trackings as view_count') // Add view count
            ->where('judul', 'like', "%{$query}%")
            ->orWhere('isi', 'like', "%{$query}%")
            ->orWhere('penulis', 'like', "%{$query}%")
            ->get()
            ->map(function ($insight) {
                return [
                    'id' => $insight->id,
                    'judul' => $insight->judul,
                    'slug' => $insight->slug,
                    'isi' => $insight->isi,
                    'image_url' => $insight->image_url,
                    'penulis' => $insight->penulis,
                    'TanggalTerbit' => $insight->TanggalTerbit,
                    'category_id' => $insight->category_id,
                    'category_name' => $insight->category ? $insight->category->name : null,
                    'view_count' => $insight->view_count, // Include view count
                ];
            });
    }
}
