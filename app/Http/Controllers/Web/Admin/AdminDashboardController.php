<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Client;
use App\Models\GalleryImage;
use App\Models\Insight;
use App\Models\Team;
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

    public function dashboard()
    {
        // Get counts for dashboard stats
        $stats = [
            'insights' => Insight::count(),
            'categories' => Category::count(),
            'teams' => Team::count(),
            'clients' => Client::count(),
            'gallery' => GalleryImage::count(),
            'carousel' => GalleryImage::where('is_carousel', true)->count(),
        ];

        // Get recent items
        $recentInsights = Insight::orderBy('created_at', 'desc')->take(5)->get();
        $recentTeams = Team::orderBy('created_at', 'desc')->take(5)->get();
        $recentClients = Client::orderBy('created_at', 'desc')->take(5)->get();
        $recentGallery = GalleryImage::orderBy('created_at', 'desc')->take(5)->get();

        return view('Admin.index', compact(
            'stats',
            'recentInsights',
            'recentTeams',
            'recentClients',
            'recentGallery'
        ));
    }
}
