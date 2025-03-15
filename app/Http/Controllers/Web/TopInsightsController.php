<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\TrackingService;
use Illuminate\Http\Request;

class TopInsightsController extends Controller
{
    protected $trackingService;

    public function __construct(TrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    /**
     * Get top insights for the API endpoint
     */
    public function getTopInsights(Request $request)
    {
        // Get the limit parameter (default to 5 if not provided)
        $limit = $request->input('limit', 5);

        // Get top articles from tracking service
        $topArticles = $this->trackingService->getTopArticles();

        // Convert to array suitable for frontend
        $insights = $topArticles->take($limit)->map(function ($article) {
            // Get the insight model to access additional fields not in the tracking result
            $insight = \App\Models\Insight::select('slug', 'image_url', 'category_id')
                ->with('category:id,name')
                ->find($article['id']);

            if (!$insight) {
                return null;
            }

            return [
                'id' => $article['id'],
                'judul' => $article['judul'],
                'slug' => $insight->slug,
                'views' => $article['views'],
                'avg_read_time' => $article['avg_read_time'],
                'completion_rate' => $article['completion_rate'],
                'image_url' => $insight->image_url,
                'category' => $insight->category ? $insight->category->name : null
            ];
        })->filter()->values();

        return response()->json($insights);
    }
}
