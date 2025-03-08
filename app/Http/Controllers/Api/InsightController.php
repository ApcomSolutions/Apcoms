<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Insight;
use App\Services\InsightService;
use Illuminate\Http\Request;

class InsightController extends Controller
{
    protected $insightService;

    public function __construct(InsightService $insightService) {
        $this->insightService = $insightService;
    }

    public function showAllInsights()
    {
        $insights = $this->insightService->getAllInsights(); // Standardized method name
        return response()->json($insights);
    }

    public function show($slug) {
        $insight = $this->insightService->getInsightBySlug($slug);
        return response()->json($insight, 200);
    }

    public function store(Request $request) {
        $insight = $this->insightService->createInsight($request);

        return response()->json([
            'message' => 'Insight berhasil dibuat',
            'data' => $insight
        ], 201);
    }

    public function update(Request $request, $slug) {
        $insight = $this->insightService->updateInsight($request, $slug);
        return response()->json([
            'message' => 'Insight berhasil diupdate',
            'data' => $insight
        ], 200);
    }

    public function destroy($slug) {
        $result = $this->insightService->deleteInsight($slug);
        return response()->json([
            'message' => 'Insight berhasil dihapus'
        ], 200);
    }
}
