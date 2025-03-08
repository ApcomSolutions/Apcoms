<?php

namespace App\Services;

use App\Models\Insight;
use Illuminate\Http\Request;

class InsightService
{
public function GetAllInsights() {
    return Insight::all();
}

public function GetInsightById($id) {
    return Insight::findOrFail($id);
}

public function createInsight(Request $request) {
    $validated = $request -> validate([
        'judul'=> 'required|string|max:255',
        'slug'=> 'required|string|unique:insights,slug',
        'isi'=> 'required|string',
        'penulis'=> 'required|string|max:255',
        'image_url'=> 'nullable|string|max:255',
        'TanggalTerbit'=> 'required|date',
        'category_id'=> 'nullable|exists:categories,id',
    ]);

    return Insight::create($validated);
}

public function updateInsight(Request $request, $id) {
    $insight = Insight::findOrFail($id);

    $validated = $request->validate([
        'judul'=> 'required|string|max:255',
        'slug'=> 'required|string|unique:insights,slug,'.$id,
        'isi'=> 'required|string',
        'penulis' => 'required|string|max:255',
        'image_url' => 'nullable|string|max:255',
        'TanggalTerbit' => 'required|date',
        'category_id' => 'nullable|exists:categories,id',
    ]);

    $insight->update($validated);

    return $insight;
}
public function deleteInsight($id) {
    $insight = Insight::findOrFail($id);
    $insight->delete();
    return response()->json(['message' => 'Insight Berhasil Dihapus']);
}
}
