<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Insight;
use App\Services\InsightService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\TrackingService;


class InsightWebController extends Controller
{
    protected $insightService;

    public function __construct(InsightService $insightService)
    {
        $this->insightService = $insightService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $insights = $this->insightService->getAllInsights();
        $categories = Category::withCount('insights')->get();

        return view('insights.index', compact('insights', 'categories'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug, Request $request)
    {
        // Get raw insight model instead of array
        $insight = Insight::with('category')->where('slug', $slug)->firstOrFail();
        $categories = Category::withCount('insights')->get();

        // Create a tracking record and pass it to the view
        $trackingService = app(TrackingService::class);
        $tracking = $trackingService->trackView($insight->id, $request);

        return view('insights.detail', compact('insight', 'categories', 'tracking'));
    }

    /**
     * Search for insights based on query
     */
    public function search(Request $request)
    {
        // Log the search request for debugging
        Log::info('Search request received', ['query' => $request->input('query')]);

        $query = $request->input('query');
        $insights = $this->insightService->searchInsights($query);
        $categories = Category::withCount('insights')->get();

        // Return the index view with search results
        return view('insights.index', [
            'insights' => $insights,
            'categories' => $categories,
            'searchQuery' => $query
        ]);
    }

    /**
     * Display insights filtered by category
     */
    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $insights = Insight::where('category_id', $category->id)
            ->with('category')
            ->get()
            ->map(function ($insight) {
                return [
                    'id' => $insight->id,
                    'judul' => $insight->judul,
                    'slug' => $insight->slug,
                    'isi' => $insight->isi,
                    'image_url' => $insight->image_url,
                    'penulis' => $insight->penulis,
                    'TanggalTerbit' => $insight->TanggalTerbit,
                    'category_id' => $insight->category_id,
                    'category_name' => $insight->category ? $insight->category->name : null,
                ];
            });

        $categories = Category::withCount('insights')->get();

        return view('insights.index', [
            'insights' => $insights,
            'categories' => $categories,
            'currentCategory' => $category->name
        ]);
    }
}
