<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Insight;
use App\Services\InsightService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\TrackingService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class InsightWebController extends Controller
{
    protected $insightService;
    protected $perPage = 9; // 3x3 grid layout

    public function __construct(InsightService $insightService)
    {
        $this->insightService = $insightService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $insights = $this->insightService->getAllInsights();
        $categories = Category::withCount('insights')->get();

        // Convert to collection for pagination
        $collection = collect($insights);

        // Sort by publish date (newest first)
        $collection = $collection->sortByDesc('TanggalTerbit');

        // Paginate the collection
        $paginatedInsights = $this->paginateCollection($collection, $request);

        return view('insights.index', [
            'insights' => $paginatedInsights,
            'categories' => $categories
        ]);
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

        // Convert to collection for pagination
        $collection = collect($insights);

        // Paginate the collection
        $paginatedInsights = $this->paginateCollection($collection, $request);

        // Return the index view with search results
        return view('insights.index', [
            'insights' => $paginatedInsights,
            'categories' => $categories,
            'searchQuery' => $query
        ]);
    }

    /**
     * Display insights filtered by category
     */
//    public function category($slug, Request $request)
//    {
//        $category = Category::where('slug', $slug)->firstOrFail();
//
//        // For better performance, we could modify this to use the service
//        // But keeping it similar to your original implementation
//        $insights = Insight::where('category_id', $category->id)
//            ->with('category')
//            ->get()
//            ->map(function ($insight) {
//                return [
//                    'id' => $insight->id,
//                    'judul' => $insight->judul,
//                    'slug' => $insight->slug,
//                    'isi' => $insight->isi,
//                    'image_url' => $insight->image_url,
//                    'penulis' => $insight->penulis,
//                    'TanggalTerbit' => $insight->TanggalTerbit,
//                    'category_id' => $insight->category_id,
//                    'category_name' => $insight->category ? $insight->category->name : null,
//                ];
//            });
//
//        $categories = Category::withCount('insights')->get();
//
//        // Convert to collection for pagination
//        $collection = collect($insights);
//
//        // Sort by publish date (newest first)
//        $collection = $collection->sortByDesc('TanggalTerbit');
//
//        // Paginate the collection
//        $paginatedInsights = $this->paginateCollection($collection, $request);
//
//        return view('insights.index', [
//            'insights' => $paginatedInsights,
//            'categories' => $categories,
//            'currentCategory' => $category->name
//        ]);
//    }


    public function category($slug, Request $request)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        // For better performance, we could modify this to use the service
        // But keeping it similar to your original implementation
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

        // Convert to collection for pagination
        $collection = collect($insights);

        // Sort by publish date (newest first)
        $collection = $collection->sortByDesc('TanggalTerbit');

        // Paginate the collection
        $paginatedInsights = $this->paginateCollection($collection, $request);

        // Return the dedicated category view instead of the index view
        return view('insights.category', [
            'insights' => $paginatedInsights,
            'categories' => $categories,
            'currentCategory' => $category->name,
            'category' => $category // Pass the full category model for additional info
        ]);
    }
    /**
     * Helper method to paginate collections
     * This allows us to paginate array data from the service
     */
    private function paginateCollection(Collection $collection, Request $request)
    {
        // Get current page from request
        $currentPage = $request->input('page', 1);

        // Calculate offset
        $offset = ($currentPage - 1) * $this->perPage;

        // Slice the collection for the current page
        $items = $collection->slice($offset, $this->perPage)->values();

        // Create a custom paginator
        $paginator = new LengthAwarePaginator(
            $items,
            $collection->count(),
            $this->perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return $paginator;
    }
}
