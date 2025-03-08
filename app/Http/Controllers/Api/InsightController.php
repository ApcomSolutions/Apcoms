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
            $insights = Insight::all(); // Ambil semua data
            return response()->json($insights);
        }



    public function show($id) {
        $insight = $this->insightService->GetInsightById($id);
        return response()->json($insight, 200);
    }

    public function store(Request $request) {
        $insight = $this->insightService->createInsight($request);

        return response()->json([
            'message' => 'Insight berhasil dibuat',
            'data' => $insight
        ], 201);
    }


    public function update(Request $request, $id) {
        $insight = $this->insightService->updateInsight($request, $id);
        return response()->json([
            'message' => 'Insight berhasil dirubah',
            'data' => $insight
        ], 200);
    }

    public function destroy($id) {
        $insight = $this->insightService->deleteInsight($id);
        return response()->json([
            'message' => 'Insight berhasil dihapus',
            'data' => $insight
        ], 200);
    }
}
