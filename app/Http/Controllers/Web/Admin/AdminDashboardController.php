<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\TrackingService;

class AdminDashboardController extends Controller
{
    protected $trackingService;

    public function __construct(TrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    public function index()
    {
        $stats = $this->trackingService->getOverallStats();
        $topArticles = $this->trackingService->getTopArticles();
        $recentViews = $this->trackingService->getRecentViews();
        $deviceBreakdown = $this->trackingService->getDeviceBreakdown();

        return view('admin.dashboard', compact('stats', 'topArticles', 'recentViews', 'deviceBreakdown'));
    }
}
