<?php

namespace App\Services;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NewsService
{
    /**
     * Get all news items (respects global scope - published only for public)
     * @return array
     */
    public function getAllNews()
    {
        return News::with('category')
            ->orderBy('publish_date', 'desc')
            ->get()
            ->map(function ($news) {
                return [
                    'id' => $news->id,
                    'title' => $news->title,
                    'slug' => $news->slug,
                    'content' => $news->content,
                    'image_url' => $news->image_url,
                    'author' => $news->author,
                    'publish_date' => $news->publish_date,
                    'news_category_id' => $news->news_category_id,
                    'category_name' => $news->category ? $news->category->name : null,
                    'category_slug' => $news->category ? $news->category->slug : null,
                    'status' => $news->status,
                ];
            });
    }

    /**
     * Get paginated news (respects global scope - published only for public)
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginatedNews($perPage = 9)
    {
        return News::with('category')
            ->orderBy('publish_date', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get a specific news item by ID (works for both published and drafts)
     * @param int $id
     * @return array
     */
    public function getNewsById($id)
    {
        $news = News::withoutGlobalScope('published')
            ->with('category')
            ->findOrFail($id);

        return [
            'id' => $news->id,
            'title' => $news->title,
            'slug' => $news->slug,
            'content' => $news->content,
            'image_url' => $news->image_url,
            'author' => $news->author,
            'publish_date' => $news->publish_date,
            'news_category_id' => $news->news_category_id,
            'category_name' => $news->category ? $news->category->name : null,
            'category_slug' => $news->category ? $news->category->slug : null,
            'status' => $news->status,
        ];
    }

    /**
     * Get a specific news item by slug (works for both published and drafts)
     * @param string $slug
     * @return array
     */
    public function getNewsBySlug($slug)
    {
        $news = News::withoutGlobalScope('published')
            ->with('category')
            ->where('slug', $slug)
            ->firstOrFail();

        return [
            'id' => $news->id,
            'title' => $news->title,
            'slug' => $news->slug,
            'content' => $news->content,
            'image_url' => $news->image_url,
            'author' => $news->author,
            'publish_date' => $news->publish_date,
            'news_category_id' => $news->news_category_id,
            'category_name' => $news->category ? $news->category->name : null,
            'category_slug' => $news->category ? $news->category->slug : null,
            'status' => $news->status,
        ];
    }

    /**
     * Create a new news item with proper slug handling
     * @param Request $request
     * @return array
     */
    public function createNews(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'publish_date' => 'required|date',
            'news_category_id' => 'nullable|exists:news_categories,id',
            'status' => 'nullable|in:published,draft',
        ]);

        // Generate base slug from title
        $baseSlug = Str::slug($validated['title']);

        // Check if slug exists
        $slug = $this->generateUniqueSlug($baseSlug);

        Log::info('Creating news with unique slug: ' . $slug);

        $newsData = [
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
            'author' => $validated['author'],
            'publish_date' => $validated['publish_date'],
            'news_category_id' => $validated['news_category_id'] ?? null,
            'status' => $validated['status'] ?? 'published',
        ];

        // Upload image if available
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('news', 'public');
            $newsData['image_url'] = '/storage/' . $path;
        }

        $news = News::create($newsData);
        return $this->getNewsById($news->id);
    }

    /**
     * Generate a unique slug for news
     * @param string $baseSlug
     * @param int $id
     * @return string
     */
    private function generateUniqueSlug($baseSlug, $id = null)
    {
        // Check if base slug exists (excluding current news if ID provided)
        $query = News::withoutGlobalScope('published')->where('slug', $baseSlug);

        if ($id) {
            $query->where('id', '!=', $id);
        }

        $exists = $query->exists();

        if (!$exists) {
            return $baseSlug;
        }

        // If exists, add a unique suffix
        $counter = 1;
        $newSlug = $baseSlug;

        while (News::withoutGlobalScope('published')
            ->where('slug', $newSlug)
            ->when($id, function ($query) use ($id) {
                return $query->where('id', '!=', $id);
            })
            ->exists()) {
            $newSlug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $newSlug;
    }

    /**
     * Update an existing news item with proper slug handling
     * @param Request $request
     * @param string $slug
     * @return array
     */
    public function updateNews(Request $request, $slug)
    {
        $news = News::withoutGlobalScope('published')->where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'publish_date' => 'required|date',
            'news_category_id' => 'nullable|exists:news_categories,id',
            'status' => 'nullable|in:published,draft',
            'delete_image' => 'nullable|boolean',
        ]);

        // Generate new slug if title has changed
        if ($news->title != $validated['title']) {
            $baseSlug = Str::slug($validated['title']);
            $validated['slug'] = $this->generateUniqueSlug($baseSlug, $news->id);

            Log::info('Updating news with new slug: ' . $validated['slug']);
        } else {
            $validated['slug'] = $news->slug;
        }

        // Prepare data for update
        $newsData = [
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'content' => $validated['content'],
            'author' => $validated['author'],
            'publish_date' => $validated['publish_date'],
            'news_category_id' => $validated['news_category_id'] ?? null,
            'status' => $validated['status'] ?? $news->status,
        ];

        // Handle image deletion if requested
        if ($request->boolean('delete_image') && $news->image_url) {
            // Delete the image from storage
            $oldPath = str_replace('/storage/', '', $news->image_url);
            Storage::disk('public')->delete($oldPath);

            // Set image_url to null in the database
            $newsData['image_url'] = null;
        }
        // Upload a new image if available
        elseif ($request->hasFile('image')) {
            // Delete old image if exists
            if ($news->image_url) {
                $oldPath = str_replace('/storage/', '', $news->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('news', 'public');
            $newsData['image_url'] = '/storage/' . $path;
        }

        $news->update($newsData);
        return $this->getNewsById($news->id);
    }

    /**
     * Delete a news item
     * @param string $slug
     * @return array
     */
    public function deleteNews($slug)
    {
        $news = News::withoutGlobalScope('published')->where('slug', $slug)->firstOrFail();

        // Delete image if exists
        if ($news->image_url) {
            $path = str_replace('/storage/', '', $news->image_url);
            Storage::disk('public')->delete($path);
        }

        $news->delete();
        return ['message' => 'News deleted successfully'];
    }

    /**
     * Search for news based on a query string
     * @param string $query
     * @return array
     */
    public function searchNews($query)
    {
        return News::with('category')
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%")
                    ->orWhere('author', 'like', "%{$query}%");
            })
            ->orderBy('publish_date', 'desc')
            ->get()
            ->map(function ($news) {
                return [
                    'id' => $news->id,
                    'title' => $news->title,
                    'slug' => $news->slug,
                    'content' => $news->content,
                    'image_url' => $news->image_url,
                    'author' => $news->author,
                    'publish_date' => $news->publish_date,
                    'news_category_id' => $news->news_category_id,
                    'category_name' => $news->category ? $news->category->name : null,
                    'category_slug' => $news->category ? $news->category->slug : null,
                    'status' => $news->status,
                ];
            });
    }

    /**
     * Get paginated search results for news
     * @param string $query
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginatedSearchResults($query, $perPage = 9)
    {
        return News::with('category')
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%")
                    ->orWhere('author', 'like', "%{$query}%");
            })
            ->orderBy('publish_date', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get news items by category ID
     * @param int $categoryId
     * @return array
     */
    public function getNewsByCategory($categoryId)
    {
        return News::with('category')
            ->where('news_category_id', $categoryId)
            ->orderBy('publish_date', 'desc')
            ->get()
            ->map(function ($news) {
                return [
                    'id' => $news->id,
                    'title' => $news->title,
                    'slug' => $news->slug,
                    'content' => $news->content,
                    'image_url' => $news->image_url,
                    'author' => $news->author,
                    'publish_date' => $news->publish_date,
                    'news_category_id' => $news->news_category_id,
                    'category_name' => $news->category ? $news->category->name : null,
                    'category_slug' => $news->category ? $news->category->slug : null,
                    'status' => $news->status,
                ];
            });
    }

    /**
     * Get paginated news by category slug
     * @param string $categorySlug
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginatedNewsByCategory($categorySlug, $perPage = 9)
    {
        return News::with('category')
            ->whereHas('category', function ($query) use ($categorySlug) {
                $query->where('slug', $categorySlug);
            })
            ->orderBy('publish_date', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get featured/latest news articles
     * @param int $limit
     * @return array
     */
    public function getFeaturedNews($limit = 5)
    {
        return News::with('category')
            ->orderBy('publish_date', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($news) {
                return [
                    'id' => $news->id,
                    'title' => $news->title,
                    'slug' => $news->slug,
                    'content' => Str::limit(strip_tags($news->content), 150),
                    'image_url' => $news->image_url,
                    'author' => $news->author,
                    'publish_date' => $news->publish_date,
                    'category_name' => $news->category ? $news->category->name : null,
                    'category_slug' => $news->category ? $news->category->slug : null,
                    'status' => $news->status,
                ];
            });
    }

    /**
     * Get related news based on category
     * @param int $newsId
     * @param int $limit
     * @return array
     */
    public function getRelatedNews($newsId, $limit = 3)
    {
        $news = News::withoutGlobalScope('published')->findOrFail($newsId);
        $categoryId = $news->news_category_id;

        return News::with('category')
            ->where('id', '!=', $newsId)
            ->where('news_category_id', $categoryId)
            ->orderBy('publish_date', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($news) {
                return [
                    'id' => $news->id,
                    'title' => $news->title,
                    'slug' => $news->slug,
                    'content' => Str::limit(strip_tags($news->content), 150),
                    'image_url' => $news->image_url,
                    'author' => $news->author,
                    'publish_date' => $news->publish_date,
                    'category_name' => $news->category ? $news->category->name : null,
                    'category_slug' => $news->category ? $news->category->slug : null,
                    'status' => $news->status,
                ];
            });
    }

    /**
     * Get news archive by year/month
     * @param int $year
     * @param int|null $month
     * @return array
     */
    public function getNewsArchive($year, $month = null)
    {
        $query = News::with('category')
            ->whereYear('publish_date', $year);

        if ($month) {
            $query->whereMonth('publish_date', $month);
        }

        return $query->orderBy('publish_date', 'desc')
            ->get()
            ->map(function ($news) {
                return [
                    'id' => $news->id,
                    'title' => $news->title,
                    'slug' => $news->slug,
                    'content' => Str::limit(strip_tags($news->content), 150),
                    'image_url' => $news->image_url,
                    'author' => $news->author,
                    'publish_date' => $news->publish_date,
                    'category_name' => $news->category ? $news->category->name : null,
                    'category_slug' => $news->category ? $news->category->slug : null,
                    'status' => $news->status,
                ];
            });
    }

    /**
     * Get available archive periods
     * @return array
     */
    public function getArchivePeriods()
    {
        $periods = News::selectRaw('YEAR(publish_date) as year, MONTH(publish_date) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return $periods->map(function ($period) {
            $date = Carbon::createFromDate($period->year, $period->month, 1);
            return [
                'year' => $period->year,
                'month' => $period->month,
                'month_name' => $date->format('F'),
                'count' => $period->count,
                'date_formatted' => $date->format('F Y'),
            ];
        });
    }

    /**
     * Get news statistics by period
     * @param string $period day|week|month|year
     * @return array
     */
    public function getNewsStatsByPeriod($period = 'month')
    {
        $now = Carbon::now();

        switch ($period) {
            case 'day':
                $startDate = $now->copy()->startOfDay();
                break;
            case 'week':
                $startDate = $now->copy()->startOfWeek();
                break;
            case 'month':
                $startDate = $now->copy()->startOfMonth();
                break;
            case 'year':
                $startDate = $now->copy()->startOfYear();
                break;
            default:
                $startDate = $now->copy()->startOfMonth();
        }

        return [
            'total' => News::count(),
            'period_total' => News::where('publish_date', '>=', $startDate)->count(),
            'by_category' => $this->getNewsByCategory(),
            'latest' => $this->getFeaturedNews(5),
        ];
    }

    /**
     * Generate excerpt from content
     * @param string $content
     * @param int $length
     * @return string
     */
    public function generateExcerpt($content, $length = 150)
    {
        return Str::limit(strip_tags($content), $length);
    }

    /**
     * Get all news items including drafts for admin purposes
     * @return array
     */
    public function getAllNewsWithDrafts()
    {
        return News::withoutGlobalScope('published')
            ->with('category')
            ->orderBy('publish_date', 'desc')
            ->get()
            ->map(function ($news) {
                return [
                    'id' => $news->id,
                    'title' => $news->title,
                    'slug' => $news->slug,
                    'content' => $news->content,
                    'image_url' => $news->image_url,
                    'author' => $news->author,
                    'publish_date' => $news->publish_date,
                    'news_category_id' => $news->news_category_id,
                    'category_name' => $news->category ? $news->category->name : null,
                    'category_slug' => $news->category ? $news->category->slug : null,
                    'status' => $news->status,
                ];
            });
    }
}
