<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NewsTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewsTrackingController extends Controller
{
    protected $trackingService;

    public function __construct(NewsTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    public function getOverallStats()
    {
        return response()->json($this->trackingService->getOverallStats());
    }

    public function getTopNews()
    {
        return response()->json($this->trackingService->getTopNews());
    }

    public function getRecentViews()
    {
        return response()->json($this->trackingService->getRecentViews());
    }

    public function getDeviceBreakdown()
    {
        return response()->json($this->trackingService->getDeviceBreakdown());
    }

    public function trackReadTime(Request $request)
    {
        $validated = $request->validate([
            'tracking_id' => 'required|exists:news_trackings,id',
            'read_time_seconds' => 'required|integer|min:1',
            'is_completed' => 'boolean'
        ]);

        $success = $this->trackingService->trackReadTime(
            $validated['tracking_id'],
            $validated['read_time_seconds'],
            $validated['is_completed'] ?? false
        );

        return $success
            ? response()->json(['message' => 'Read time updated successfully'])
            : response()->json(['error' => 'Tracking ID not found'], 404);
    }

    public function getStats($newsId)
    {
        return response()->json($this->trackingService->getOverallStats($newsId));
    }

    public function getNewsStats($newsId)
    {
        $stats = $this->trackingService->getNewsStats($newsId);

        if (!$stats) {
            return response()->json(['message' => 'News not found or no data available'], 404);
        }

        return response()->json([
            'id' => $stats->id,
            'title' => $stats->title,
            'total_views' => $stats->total_views,
            'unique_viewers' => $stats->unique_viewers,
            'avg_read_time' => round($stats->avg_read_time, 1),
            'max_read_time' => round($stats->max_read_time / 60, 1) ?: 0, // Convert to minutes
            'min_read_time' => round($stats->min_read_time / 60, 1) ?: 0, // Convert to minutes
            'completion_rate' => $stats->total_reads > 0
                ? round(($stats->total_completed / $stats->total_reads) * 100, 1)
                : 0, // Avoid division by zero error
        ]);
    }

    /**
     * Get time-series data for news activity
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActivityTimeSeries(Request $request)
    {
        try {
            $period = $request->input('period', 'day');
            $newsId = $request->input('news_id'); // Optional parameter for single news stats

            $data = $this->trackingService->getActivityTimeSeries($period, $newsId);

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Activity time series error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to fetch activity data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get read time distribution statistics
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReadTimeDistribution(Request $request)
    {
        try {
            $newsId = $request->input('news_id'); // Optional parameter for single news
            $data = $this->trackingService->getReadTimeDistribution($newsId);

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Read time distribution error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to fetch read time distribution',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get trend data comparing current period to previous period
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTrendData(Request $request)
    {
        try {
            $period = $request->input('period', 'day');
            $newsId = $request->input('news_id'); // Optional parameter for single news

            $data = $this->trackingService->getTrendData($period, $newsId);

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Trend data error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to fetch trend data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
