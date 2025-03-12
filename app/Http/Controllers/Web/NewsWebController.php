<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\NewsCategory;
use App\Models\News;
use App\Services\NewsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\NewsTrackingService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class NewsWebController extends Controller
{
    protected $newsService;
    protected $perPage = 9; // 3x3 grid layout

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    /**
     * Display a listing of the news.
     */
    public function index(Request $request)
    {
        // Get all news from service - will respect global scope and only show published
        $news = $this->newsService->getAllNews();

        $categories = NewsCategory::withCount('news')->get();

        // Convert to collection for pagination
        $collection = collect($news);

        // Sort by publish date (newest first)
        $collection = $collection->sortByDesc('publish_date');

        // Paginate the collection
        $paginatedNews = $this->paginateCollection($collection, $request);

        return view('news.index', [
            'news' => $paginatedNews,
            'categories' => $categories
        ]);
    }

    /**
     * Display the specified news.
     */
    public function show(string $slug, Request $request)
    {
        // Global scope will automatically filter for published only
        $news = News::with('category')
            ->where('slug', $slug)
            ->firstOrFail();

        $categories = NewsCategory::withCount('news')->get();

        // Get related news articles (will respect global scope)
        $relatedNews = $this->newsService->getRelatedNews($news->id, 4);

        // Create a tracking record and pass it to the view
        $trackingService = app(NewsTrackingService::class);
        $tracking = $trackingService->trackView($news->id, $request);

        return view('news.detail', compact('news', 'categories', 'tracking', 'relatedNews'));
    }

    /**
     * Search for news based on query
     */
    public function search(Request $request)
    {
        // Log the search request for debugging
        Log::info('Search request received', ['query' => $request->input('query')]);

        $query = $request->input('query');
        $category = $request->input('category'); // Optional category filter

        // Get search results from service (will respect global scope)
        $news = $this->newsService->searchNews($query);

        $categories = NewsCategory::withCount('news')->get();

        // If category filter is applied
        if ($category) {
            $targetCategory = NewsCategory::where('slug', $category)->first();
            if ($targetCategory) {
                $news = collect($news)->filter(function ($item) use ($targetCategory) {
                    return $item['news_category_id'] == $targetCategory->id;
                })->values()->all();
            }
        }

        // Convert to collection for pagination
        $collection = collect($news);

        // Paginate the collection
        $paginatedNews = $this->paginateCollection($collection, $request);

        // Return the index view with search results
        return view('news.index', [
            'news' => $paginatedNews,
            'categories' => $categories,
            'searchQuery' => $query
        ]);
    }

    /**
     * Display news filtered by category
     */
    public function category($slug, Request $request)
    {
        $category = NewsCategory::where('slug', $slug)->firstOrFail();

        // Global scope will filter for published articles only
        $news = News::where('news_category_id', $category->id)
            ->with('category')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'slug' => $item->slug,
                    'content' => $item->content,
                    'image_url' => $item->image_url,
                    'author' => $item->author,
                    'publish_date' => $item->publish_date,
                    'news_category_id' => $item->news_category_id,
                    'category_name' => $item->category ? $item->category->name : null,
                    'category_slug' => $item->category ? $item->category->slug : null,
                ];
            });

        $categories = NewsCategory::withCount('news')->get();

        // Convert to collection for pagination
        $collection = collect($news);

        // Sort by publish date (newest first)
        $collection = $collection->sortByDesc('publish_date');

        // Paginate the collection
        $paginatedNews = $this->paginateCollection($collection, $request);

        // Return the dedicated category view
        return view('news.category', [
            'news' => $paginatedNews,
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
