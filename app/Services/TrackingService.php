<?php

namespace App\Services;

use App\Models\Insight;
use App\Models\InsightTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class TrackingService
{
    /**
     * Track a new page view for an insight
     */
    public function trackView(int $insightId, Request $request)
    {
        try {
            $deviceId = $this->getOrCreateDeviceId($request);
            $ipHash = Hash::make($request->ip());

            return InsightTracking::create([
                'insight_id' => $insightId,
                'device_id' => $deviceId,
                'user_agent' => $request->userAgent(),
                'ip_hash' => $ipHash,
                'referrer' => $request->header('referer'),
                'read_time_seconds' => 0,
                'is_completed' => false,
            ]);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get overall statistics
     */
    public function getOverallStats()
    {
        return [
            'total_views' => InsightTracking::count(),
            'unique_devices' => InsightTracking::distinct('device_id')->count('device_id'),
            'avg_read_time' => round(InsightTracking::avg('read_time_seconds') / 60, 1) ?: 0,
            'completion_rate' => round((InsightTracking::where('is_completed', true)->count() / max(InsightTracking::count(), 1)) * 100, 1)
        ];
    }

    /**
     * Get top articles by views
     */
    public function getTopArticles()
    {
        return DB::table('insights')
            ->select(
                'insights.id',
                'insights.judul', // Ganti "title" menjadi "judul"
                DB::raw('COUNT(insight_trackings.id) as views'),
                DB::raw('COUNT(DISTINCT insight_trackings.device_id) as unique_viewers'),
                DB::raw('COALESCE(AVG(insight_trackings.read_time_seconds) / 60, 0) as avg_read_time'), // Fix NULL
                DB::raw('COALESCE(SUM(CASE WHEN insight_trackings.is_completed = 1 THEN 1 ELSE 0 END), 0) as total_completed'),
                DB::raw('COALESCE(COUNT(insight_trackings.id), 1) as total_views')
            )
            ->leftJoin('insight_trackings', 'insights.id', '=', 'insight_trackings.insight_id')
            ->groupBy('insights.id', 'insights.judul')
            ->orderByDesc('views')
            ->limit(10)
            ->get()
            ->map(function ($article) {
                return [
                    'id' => $article->id,
                    'judul' => $article->judul,
                    'views' => $article->views,
                    'unique_viewers' => $article->unique_viewers,
                    'avg_read_time' => round($article->avg_read_time, 1), // Biarkan hanya 1 angka desimal
                    'completion_rate' => $article->total_views > 0
                        ? round(($article->total_completed / $article->total_views) * 100, 1)
                        : 0, // Hindari error pembagian dengan 0
                ];
            });
    }


    /**
     * Get recent page views
     */
    public function getRecentViews()
    {
        return DB::table('insight_trackings')
            ->select(
                'insight_trackings.created_at',
                'insight_trackings.user_agent',
                'insights.judul as title' // Ganti "insights.title" menjadi "insights.judul"
            )
            ->join('insights', 'insight_trackings.insight_id', '=', 'insights.id')
            ->orderByDesc('insight_trackings.created_at')
            ->limit(10)
            ->get()
            ->map(fn($view) => [
                'title' => $view->title, // Tidak perlu diubah, karena sudah pakai alias "title"
                'time' => $view->created_at,
                'device' => $this->getDeviceType($view->user_agent)
            ]);
    }


    /**
     * Get device breakdown statistics
     */
    public function getDeviceBreakdown()
    {
        $devices = InsightTracking::select('user_agent')->get()
            ->groupBy(fn($tracking) => $this->getDeviceType($tracking->user_agent))
            ->map(fn($group) => count($group));

        $total = $devices->sum();
        return $devices->map(fn($count, $type) => [
            'type' => $type,
            'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0
        ])->values();
    }

    /**
     * Track read time
     */
    public function trackReadTime(int $trackingId, int $seconds, bool $completed = false)
    {
        $tracking = InsightTracking::find($trackingId);

        if (!$tracking) {
            return false;
        }

        $tracking->read_time_seconds += $seconds;
        if ($completed) {
            $tracking->is_completed = true;
        }

        return $tracking->save();
    }

    /**
     * Get device type from user agent
     */
    private function getDeviceType($userAgent)
    {
        $userAgent = strtolower($userAgent);

        if (strpos($userAgent, 'mobile') !== false || strpos($userAgent, 'android') !== false) {
            return 'Mobile';
        } elseif (strpos($userAgent, 'tablet') !== false || strpos($userAgent, 'ipad') !== false) {
            return 'Tablet';
        } elseif (strpos($userAgent, 'windows') !== false || strpos($userAgent, 'macintosh') !== false || strpos($userAgent, 'linux') !== false) {
            return 'Desktop';
        } else {
            return 'Other';
        }
    }

    /**
     * Generate or retrieve a unique device ID
     */
    private function getOrCreateDeviceId(Request $request)
    {
        $cookieName = 'insight_device_id';

        // Jika cookie sudah ada, gunakan nilai tersebut
        if ($request->cookie($cookieName)) {
            return $request->cookie($cookieName);
        }

        // Buat ID perangkat baru (hash dari user agent + IP + string random)
        $deviceId = hash('sha256', $request->userAgent() . $request->ip() . Str::random(16));

        // Simpan di cookie selama 2 tahun
        Cookie::queue($cookieName, $deviceId, 60 * 24 * 365 * 2);

        return $deviceId;
    }

    public function getInsightStats(int $insightId)
    {
        return DB::table('insight_trackings')
            ->select(
                'insights.id',
                'insights.judul',
                DB::raw('COUNT(insight_trackings.id) as total_views'),
                DB::raw('COUNT(DISTINCT insight_trackings.device_id) as unique_viewers'),
                DB::raw('COALESCE(AVG(insight_trackings.read_time_seconds) / 60, 0) as avg_read_time'),
                DB::raw('COALESCE(SUM(CASE WHEN insight_trackings.is_completed = 1 THEN 1 ELSE 0 END), 0) as total_completed'),
                DB::raw('COALESCE(COUNT(insight_trackings.id), 1) as total_reads'),
                DB::raw('MAX(insight_trackings.read_time_seconds) as max_read_time'),
                DB::raw('MIN(insight_trackings.read_time_seconds) as min_read_time')
            )
            ->join('insights', 'insight_trackings.insight_id', '=', 'insights.id')
            ->where('insight_trackings.insight_id', $insightId)
            ->groupBy('insights.id', 'insights.judul')
            ->first(); // Ambil satu hasil saja
    }

    /**
     * Get time-series data for insights activity
     *
     * @param string $period day|week|month|all
     * @param int|null $insightId Optional insight ID to filter by
     * @return array
     */
    public function getActivityTimeSeries(string $period = 'day', ?int $insightId = null): array
    {
        $now = Carbon::now();
        $query = DB::table('insight_trackings');

        // Filter by insight if provided
        if ($insightId) {
            $query->where('insight_id', $insightId);
        }

        // Configure the query based on period
        switch ($period) {
            case 'day':
                $startDate = $now->copy()->startOfDay();
                $groupFormat = '%H'; // Group by hour
                $dateFormat = 'H:i';
                $query->select(
                    DB::raw('HOUR(created_at) as time_bucket'),
                    DB::raw('COUNT(*) as views'),
                    DB::raw('COALESCE(AVG(read_time_seconds) / 60, 0) as avg_read_time')
                );
                break;

            case 'week':
                $startDate = $now->copy()->subDays(6)->startOfDay();
                $groupFormat = '%Y-%m-%d'; // Group by day
                $dateFormat = 'M d';
                $query->select(
                    DB::raw('DATE(created_at) as time_bucket'),
                    DB::raw('COUNT(*) as views'),
                    DB::raw('COALESCE(AVG(read_time_seconds) / 60, 0) as avg_read_time')
                );
                break;

            case 'month':
                $startDate = $now->copy()->startOfMonth();
                $groupFormat = '%Y-%m-%d'; // Group by day
                $dateFormat = 'M d';
                $query->select(
                    DB::raw('DATE(created_at) as time_bucket'),
                    DB::raw('COUNT(*) as views'),
                    DB::raw('COALESCE(AVG(read_time_seconds) / 60, 0) as avg_read_time')
                );
                break;

            case 'all':
            default:
                $startDate = $now->copy()->subMonths(11)->startOfMonth();
                $groupFormat = '%Y-%m'; // Group by month
                $dateFormat = 'M Y';
                $query->select(
                    DB::raw("DATE_FORMAT(created_at, '%Y-%m') as time_bucket"),
                    DB::raw('COUNT(*) as views'),
                    DB::raw('COALESCE(AVG(read_time_seconds) / 60, 0) as avg_read_time')
                );
                break;
        }

        // Get data from database
        $results = $query
            ->where('created_at', '>=', $startDate)
            ->groupBy('time_bucket')
            ->orderBy('time_bucket')
            ->get();

        // Format the results for the chart
        $formattedResults = $this->formatTimeSeriesData($results, $period, $startDate);

        return [
            'views' => $formattedResults->map(function ($item) {
                return [
                    'x' => $item['date'],
                    'y' => $item['views']
                ];
            })->values()->all(),

            'readTime' => $formattedResults->map(function ($item) {
                return [
                    'x' => $item['date'],
                    'y' => round($item['avg_read_time'], 1)
                ];
            })->values()->all()
        ];
    }

    /**
     * Format time series data to ensure all time buckets exist
     *
     * @param \Illuminate\Support\Collection $results
     * @param string $period
     * @param \Carbon\Carbon $startDate
     * @return \Illuminate\Support\Collection
     */
    private function formatTimeSeriesData(Collection $results, string $period, Carbon $startDate): Collection
    {
        $formatted = collect();
        $now = Carbon::now();

        // Create all time buckets based on period
        switch ($period) {
            case 'day':
                $timeFormat = 'H:i';
                $interval = 'addHour';
                $buckets = 24;
                break;

            case 'week':
                $timeFormat = 'Y-m-d';
                $interval = 'addDay';
                $buckets = 7;
                break;

            case 'month':
                $timeFormat = 'Y-m-d';
                $interval = 'addDay';
                $daysInMonth = $now->daysInMonth;
                $buckets = $daysInMonth;
                break;

            case 'all':
            default:
                $timeFormat = 'Y-m';
                $interval = 'addMonth';
                $buckets = 12;
                break;
        }

        // Generate all possible time buckets
        $currentDate = $startDate->copy();
        for ($i = 0; $i < $buckets; $i++) {
            $bucket = $currentDate->format($timeFormat);

            // Find matching result or use default
            $resultItem = $results->firstWhere('time_bucket', $bucket);

            $formatted->push([
                'date' => $currentDate->format('c'), // ISO 8601 date format for Chart.js
                'views' => $resultItem ? $resultItem->views : 0,
                'avg_read_time' => $resultItem ? $resultItem->avg_read_time : 0
            ]);

            // Move to next time bucket
            $currentDate->{$interval}();
        }

        return $formatted;
    }

    /**
     * Get read time distribution statistics
     *
     * @param int|null $insightId Optional insight ID to filter by
     * @return array
     */
    public function getReadTimeDistribution(?int $insightId = null): array
    {
        $query = DB::table('insight_trackings')
            ->select(
                DB::raw('
                    CASE
                        WHEN read_time_seconds < 60 THEN "<1 min"
                        WHEN read_time_seconds BETWEEN 60 AND 180 THEN "1-3 min"
                        WHEN read_time_seconds BETWEEN 181 AND 300 THEN "3-5 min"
                        WHEN read_time_seconds BETWEEN 301 AND 600 THEN "5-10 min"
                        ELSE ">10 min"
                    END as time_bucket
                '),
                DB::raw('COUNT(*) as count')
            )
            ->where('read_time_seconds', '>', 0);

        // Filter by insight if provided
        if ($insightId) {
            $query->where('insight_id', $insightId);
        }

        $results = $query->groupBy('time_bucket')->get();

        // Calculate percentages
        $total = $results->sum('count');
        $percentages = [];

        // Time buckets in order
        $buckets = ['<1 min', '1-3 min', '3-5 min', '5-10 min', '>10 min'];

        foreach ($buckets as $bucket) {
            $item = $results->firstWhere('time_bucket', $bucket);
            $count = $item ? $item->count : 0;
            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            $percentages[] = $percentage;
        }

        return [
            'labels' => $buckets,
            'data' => $percentages
        ];
    }

    /**
     * Get trend data comparing current period to previous period
     *
     * @param string $period day|week|month|all
     * @param int|null $insightId Optional insight ID to filter by
     * @return array
     */
    public function getTrendData(string $period = 'day', ?int $insightId = null): array
    {
        $now = Carbon::now();

        // Set time periods for current and previous
        switch ($period) {
            case 'day':
                $currentStart = $now->copy()->startOfDay();
                $previousStart = $now->copy()->subDay()->startOfDay();
                $currentEnd = $now;
                $previousEnd = $now->copy()->subDay();
                break;

            case 'week':
                $currentStart = $now->copy()->startOfWeek();
                $previousStart = $now->copy()->subWeek()->startOfWeek();
                $currentEnd = $now;
                $previousEnd = $now->copy()->subWeek();
                break;

            case 'month':
                $currentStart = $now->copy()->startOfMonth();
                $previousStart = $now->copy()->subMonth()->startOfMonth();
                $currentEnd = $now;
                $previousEnd = $now->copy()->subMonth()->endOfMonth();
                break;

            case 'all':
            default:
                $currentStart = $now->copy()->subMonths(12)->startOfDay();
                $previousStart = $now->copy()->subMonths(24)->startOfDay();
                $currentEnd = $now;
                $previousEnd = $now->copy()->subMonths(12);
                break;
        }

        // Current period stats
        $currentStats = $this->getStatsByPeriod($currentStart, $currentEnd, $insightId);

        // Previous period stats
        $previousStats = $this->getStatsByPeriod($previousStart, $previousEnd, $insightId);

        // Calculate percentage changes
        $viewsChange = $this->calculatePercentageChange(
            $previousStats['total_views'],
            $currentStats['total_views']
        );

        $uniqueViewersChange = $this->calculatePercentageChange(
            $previousStats['unique_viewers'],
            $currentStats['unique_viewers']
        );

        $avgReadTimeChange = $this->calculatePercentageChange(
            $previousStats['avg_read_time'],
            $currentStats['avg_read_time']
        );

        $completionRateChange = $this->calculatePercentageChange(
            $previousStats['completion_rate'],
            $currentStats['completion_rate']
        );

        return [
            'total_views_change' => $viewsChange,
            'unique_viewers_change' => $uniqueViewersChange,
            'avg_read_time_change' => $avgReadTimeChange,
            'completion_rate_change' => $completionRateChange
        ];
    }

    /**
     * Get statistics for a specific time period
     *
     * @param Carbon $start Start date
     * @param Carbon $end End date
     * @param int|null $insightId Optional insight ID to filter by
     * @return array
     */
    private function getStatsByPeriod(Carbon $start, Carbon $end, ?int $insightId = null): array
    {
        $query = DB::table('insight_trackings')
            ->whereBetween('created_at', [$start, $end]);

        // Filter by insight if provided
        if ($insightId) {
            $query->where('insight_id', $insightId);
        }

        $totalViews = $query->count();

        $uniqueViewersQuery = DB::table('insight_trackings')
            ->whereBetween('created_at', [$start, $end]);

        if ($insightId) {
            $uniqueViewersQuery->where('insight_id', $insightId);
        }

        $uniqueViewers = $uniqueViewersQuery->distinct('device_id')->count('device_id');

        $avgReadTimeQuery = DB::table('insight_trackings')
            ->whereBetween('created_at', [$start, $end]);

        if ($insightId) {
            $avgReadTimeQuery->where('insight_id', $insightId);
        }

        $avgReadTime = $avgReadTimeQuery->avg('read_time_seconds') ?? 0;

        $readsQuery = DB::table('insight_trackings')
            ->whereBetween('created_at', [$start, $end]);

        if ($insightId) {
            $readsQuery->where('insight_id', $insightId);
        }

        $reads = $readsQuery->count();

        $completedQuery = DB::table('insight_trackings')
            ->whereBetween('created_at', [$start, $end])
            ->where('is_completed', true);

        if ($insightId) {
            $completedQuery->where('insight_id', $insightId);
        }

        $completed = $completedQuery->count();

        $completionRate = $reads > 0 ? ($completed / $reads) * 100 : 0;

        return [
            'total_views' => $totalViews,
            'unique_viewers' => $uniqueViewers,
            'avg_read_time' => $avgReadTime / 60, // Convert to minutes
            'completion_rate' => $completionRate
        ];
    }

    /**
     * Calculate percentage change between two values
     */
    private function calculatePercentageChange(float $oldValue, float $newValue): float
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }

        return round((($newValue - $oldValue) / $oldValue) * 100, 1);
    }
}
