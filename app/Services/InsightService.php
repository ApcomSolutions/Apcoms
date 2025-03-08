<?php

namespace App\Services;

use App\Models\Insight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InsightService
{
    public function GetAllInsights() {
        return Insight::with('category')
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
                ];
            });
    }

    public function getInsightBySlug($slug) {
        $insight = Insight::with('category')->where('slug', $slug)->firstOrFail();

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
        ];
    }


    public function createInsight(Request $request) {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'slug' => 'required|string|unique:insights,slug',
            'isi' => 'required|string',
            'penulis' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
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
        return $this->GetInsightById($insight->id);
    }

    public function updateInsight(Request $request, $slug) {
        $insight = Insight::findOrFail($slug);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'slug' => 'required|string|unique:insights,slug,'.$slug,
            'isi' => 'required|string',
            'penulis' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'TanggalTerbit' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $insightData = collect($validated)->except('image')->toArray();

        // Upload gambar baru jika ada
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($insight->image_url) {
                $oldPath = str_replace('/storage/', '', $insight->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('insights', 'public');
            $insightData['image_url'] = '/storage/' . $path;
        }

        $insight->update($insightData);
        return $this->GetInsightById($insight->id);
    }

    public function deleteInsight($id) {
        $insight = Insight::findOrFail($id);

        // Hapus gambar jika ada
        if ($insight->image_url) {
            $path = str_replace('/storage/', '', $insight->image_url);
            Storage::disk('public')->delete($path);
        }

        $insight->delete();
        return ['message' => 'Insight berhasil dihapus'];
    }
}
