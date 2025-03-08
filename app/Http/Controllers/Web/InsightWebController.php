<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Insight;
use App\Services\InsightService;

class InsightWebController extends Controller
{
    protected $insightService;

    public function __construct(InsightService $insightService) {
        $this->insightService = $insightService;
    }

    public function index()
    {
        $insights = $this->insightService->GetAllInsights(); // Ambil semua data insights
        return view('insights.index', compact('insights')); // Kirim ke Blade
    }

    public function show($slug)
    {
        $insightArray = $this->insightService->getInsightBySlug($slug);
        $insight = (object) $insightArray; // Convert array to object for the detail view
        return view('insights.detail', compact('insight'));
    }
}
