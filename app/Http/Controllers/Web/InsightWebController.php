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
        $insights = $this->insightService->getAllInsights(); // Standardized method name
        return view('insights.index', compact('insights'));
    }

    public function show($slug)
    {
        $insightArray = $this->insightService->getInsightBySlug($slug);
        $insight = (object) $insightArray; // Convert to object to use ->property notation
        return view('insights.detail', compact('insight'));
    }
}
