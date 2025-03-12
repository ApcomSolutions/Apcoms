<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NewsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{
    protected $newsService;

    public function __construct(NewsService $newsService) {
        $this->newsService = $newsService;
    }

    /**
     * Display a listing of all news
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showAllNews()
    {
        $news = $this->newsService->getAllNews();
        return response()->json($news);
    }

    /**
     * Display a paginated listing of news
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaginatedNews(Request $request)
    {
        $perPage = $request->input('per_page', 9);
        $news = $this->newsService->getPaginatedNews($perPage);
        return response()->json($news);
    }

    /**
     * Display the specified news by ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showById($id)
    {
        $news = $this->newsService->getNewsById($id);
        return response()->json($news, 200);
    }

    /**
     * Display the specified news by slug
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        $news = $this->newsService->getNewsBySlug($slug);
        return response()->json($news, 200);
    }

    /**
     * Store a newly created news
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Log::info('News store request:', $request->all());

        try {
            // Menggunakan metode createNews yang ada di service
            $news = $this->newsService->createNews($request);

            return response()->json([
                'message' => 'News created successfully',
                'data' => $news
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', ['errors' => $e->errors()]);
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating news:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error creating news: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified news
     *
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $slug)
    {
        Log::info('News update request data:', $request->all());
        Log::info('Status field value for update:', ['status' => $request->input('status', 'not-provided')]);

        try {
            $news = $this->newsService->updateNews($request, $slug);

            Log::info('Updated news object:', ['news' => $news]);

            return response()->json([
                'message' => 'News updated successfully',
                'data' => $news
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating news:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Error updating news: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified news
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($slug)
    {
        $result = $this->newsService->deleteNews($slug);
        return response()->json([
            'message' => 'News deleted successfully'
        ], 200);
    }

    /**
     * Search for news
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $news = $this->newsService->searchNews($query);

        return response()->json([
            'status' => 'success',
            'data' => $news,
            'meta' => [
                'count' => count($news),
                'query' => $query
            ]
        ]);
    }

    /**
     * Get paginated search results
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaginatedSearch(Request $request)
    {
        $query = $request->input('query', '');
        $perPage = $request->input('per_page', 9);

        $news = $this->newsService->getPaginatedSearchResults($query, $perPage);

        return response()->json($news);
    }

    /**
     * Get news by category ID
     *
     * @param int $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNewsByCategory($categoryId)
    {
        $news = $this->newsService->getNewsByCategory($categoryId);
        return response()->json($news);
    }

    /**
     * Get paginated news by category slug
     *
     * @param string $categorySlug
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaginatedNewsByCategory($categorySlug, Request $request)
    {
        $perPage = $request->input('per_page', 9);
        $news = $this->newsService->getPaginatedNewsByCategory($categorySlug, $perPage);
        return response()->json($news);
    }

    /**
     * Get featured/latest news
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFeaturedNews(Request $request)
    {
        $limit = $request->input('limit', 5);
        $news = $this->newsService->getFeaturedNews($limit);
        return response()->json($news);
    }

    /**
     * Get related news
     *
     * @param int $newsId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRelatedNews($newsId, Request $request)
    {
        $limit = $request->input('limit', 3);
        $news = $this->newsService->getRelatedNews($newsId, $limit);
        return response()->json($news);
    }

    /**
     * Get news archive by year/month
     *
     * @param int $year
     * @param int|null $month
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNewsArchive($year, $month = null)
    {
        $news = $this->newsService->getNewsArchive($year, $month);
        return response()->json($news);
    }

    /**
     * Get available archive periods
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getArchivePeriods()
    {
        $periods = $this->newsService->getArchivePeriods();
        return response()->json($periods);
    }

    /**
     * Get news statistics by period
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNewsStatsByPeriod(Request $request)
    {
        $period = $request->input('period', 'month');
        $stats = $this->newsService->getNewsStatsByPeriod($period);
        return response()->json($stats);
    }

    /**
     * Generate excerpt from content
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateExcerpt(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'length' => 'nullable|integer|min:10|max:1000'
        ]);

        $length = $validated['length'] ?? 150;
        $excerpt = $this->newsService->generateExcerpt($validated['content'], $length);

        return response()->json([
            'original_length' => strlen($validated['content']),
            'excerpt_length' => strlen($excerpt),
            'excerpt' => $excerpt
        ]);
    }
}
