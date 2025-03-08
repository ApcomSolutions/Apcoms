<?php

namespace App\Services;

use App\Models\Insight;
use App\Models\InsightTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

}
