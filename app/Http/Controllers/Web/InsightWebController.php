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
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Illuminate\Support\Str;

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
        // SEO for insights listing page
        $seoData = new SEOData(
            title: 'Insights',
            description: 'Kumpulan artikel insights terbaru tentang komunikasi, PR, dan strategi branding dari ApCom Solutions.',
            url: route('insights.index')
        );

        $insights = $this->insightService->getAllInsights();

        // Get categories with children and counts
        $categories = Category::withCount('insights')
            ->with(['children' => function($query) {
                $query->withCount('insights')->where('is_active', true)->orderBy('order');
            }])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        // Convert to collection for pagination
        $collection = collect($insights);

        // Sort by publish date (newest first)
        $collection = $collection->sortByDesc('TanggalTerbit');

        // Paginate the collection
        $paginatedInsights = $this->paginateCollection($collection, $request);

        return view('insights.index', [
            'insights' => $paginatedInsights,
            'categories' => $categories,
            'seoData' => $seoData
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug, Request $request)
    {
        // Get raw insight model instead of array
        $insight = Insight::with('category.parent')->where('slug', $slug)->firstOrFail();

        // Get categories with children and counts
        $categories = Category::withCount('insights')
            ->with(['children' => function($query) {
                $query->withCount('insights')->where('is_active', true)->orderBy('order');
            }])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        // Set SEO for this insight
        $seoData = new SEOData(
            title: $insight->judul,
            description: Str::limit(strip_tags($insight->isi), 160),
            image: $insight->image_url,
            url: route('insights.show', $insight->slug)
        );

        // Create breadcrumbs for structured data
        if ($insight->category) {
            $breadcrumbItems = [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => route('home')
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Insights',
                    'item' => route('insights.index')
                ]
            ];

            $position = 3;

            // If this insight is in a subcategory, add the parent category first
            if ($insight->category->parent) {
                $breadcrumbItems[] = [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $insight->category->parent->name,
                    'item' => route('insights.category', $insight->category->parent->slug)
                ];
            }

            // Add the direct category
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $insight->category->name,
                'item' => route('insights.category', $insight->category->slug)
            ];

            // Add the current insight
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $insight->judul,
                'item' => route('insights.show', $insight->slug)
            ];

            $seoData->jsonLd = [
                '@type' => 'Article',
                'breadcrumb' => [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => $breadcrumbItems
                ]
            ];
        }

        // Create a tracking record and pass it to the view
        $trackingService = app(TrackingService::class);
        $tracking = $trackingService->trackView($insight->id, $request);

        return view('insights.detail', compact('insight', 'categories', 'tracking', 'seoData'));
    }

    /**
     * Search for insights based on query
     */
    public function search(Request $request)
    {
        // Log the search request for debugging
        Log::info('Search request received', ['query' => $request->input('query')]);

        $query = $request->input('query');
        $seoData = new SEOData(
            title: "Hasil Pencarian: $query",
            description: "Hasil pencarian untuk \"$query\" di ApCom Solutions Insights.",
            robots: 'noindex,follow' // Prevent search pages from being indexed
        );
        $insights = $this->insightService->searchInsights($query);

        // Get categories with children and counts
        $categories = Category::withCount('insights')
            ->with(['children' => function($query) {
                $query->withCount('insights')->where('is_active', true)->orderBy('order');
            }])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        // Convert to collection for pagination
        $collection = collect($insights);

        // Paginate the collection
        $paginatedInsights = $this->paginateCollection($collection, $request);

        // Return the index view with search results
        return view('insights.index', [
            'insights' => $paginatedInsights,
            'categories' => $categories,
            'searchQuery' => $query,
            'seoData' => $seoData
        ]);
    }

    public function category($slug, Request $request)
    {
        $category = Category::with('parent')
            ->where('slug', $slug)
            ->firstOrFail();

        // SEO for category pages
        $seoData = new SEOData(
            title: "Kategori: {$category->name}",
            description: "Artikel insights tentang {$category->name} dari ApCom Solutions.",
            url: route('insights.category', $slug)
        );

        // For better performance, we could modify this to use the service
        // But keeping it similar to your original implementation
        $insights = Insight::where(function($query) use ($category) {
            // Get insights from this category
            $query->where('category_id', $category->id);

            // If this is a parent category, also include insights from subcategories
            if (!$category->parent_id) {
                $childIds = Category::where('parent_id', $category->id)->pluck('id');
                if ($childIds->count() > 0) {
                    $query->orWhereIn('category_id', $childIds);
                }
            }
        })
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

        // Get categories with children and counts
        $categories = Category::withCount('insights')
            ->with(['children' => function($query) {
                $query->withCount('insights')->where('is_active', true)->orderBy('order');
            }])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        // Convert to collection for pagination
        $collection = collect($insights);

        // Sort by publish date (newest first)
        $collection = $collection->sortByDesc('TanggalTerbit');

        // Paginate the collection
        $paginatedInsights = $this->paginateCollection($collection, $request);

        // Return the dedicated category view
        return view('insights.category', [
            'insights' => $paginatedInsights,
            'categories' => $categories,
            'currentCategory' => $category->name,
            'category' => $category, // Pass the full category model for additional info
            'seoData' => $seoData
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
