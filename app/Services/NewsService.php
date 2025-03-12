<?php

namespace App\Services;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NewsService
{
    /**
     * Get all news items
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
                    'status' => $news->status, // Added status field
                ];
            });
    }

    /**
     * Get paginated news
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
     * Get a specific news item by ID
     * @param int $id
     * @return array
     */
    public function getNewsById($id)
    {
        $news = News::with('category')->findOrFail($id);

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
            'status' => $news->status, // Added status field
        ];
    }

    /**
     * Get a specific news item by slug
     * @param string $slug
     * @return array
     */
    public function getNewsBySlug($slug)
    {
        $news = News::with('category')->where('slug', $slug)->firstOrFail();

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
            'status' => $news->status, // Added status field
        ];
    }

    /**
     * Create a new news item
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
            'status' => 'nullable|in:published,draft', // Added status validation
        ]);

        // Generate slug automatically
        $slug = Str::slug($validated['title']);

        // Check if slug exists, if so, append a unique identifier
        $count = News::where('slug', $slug)->count();
        if ($count > 0) {
            $slug = $slug . '-' . uniqid();
        }

        $newsData = [
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
            'author' => $validated['author'],
            'publish_date' => $validated['publish_date'],
            'news_category_id' => $validated['news_category_id'] ?? null,
            'status' => $validated['status'] ?? 'published', // Add status with default
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
     * Update an existing news item
     * @param Request $request
     * @param string $slug
     * @return array
     */
    public function updateNews(Request $request, $slug)
    {
        $news = News::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'publish_date' => 'required|date',
            'news_category_id' => 'nullable|exists:news_categories,id',
            'status' => 'nullable|in:published,draft', // Added status validation
            'delete_image' => 'nullable|boolean', // Add validation for delete_image flag
        ]);

        // Generate new slug if title has changed
        if ($news->title != $validated['title']) {
            $newSlug = Str::slug($validated['title']);

            // Check if new slug exists (excluding current news item)
            $count = News::where('slug', $newSlug)->where('id', '!=', $news->id)->count();
            if ($count > 0) {
                $newSlug = $newSlug . '-' . uniqid();
            }

            $validated['slug'] = $newSlug;
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
            'status' => $validated['status'] ?? $news->status, // Keep existing status if not provided
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
        $news = News::where('slug', $slug)->firstOrFail();

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
            ->where('title', 'like', "%{$query}%")
            ->orWhere('content', 'like', "%{$query}%")
            ->orWhere('author', 'like', "%{$query}%")
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
                    'status' => $news->status, // Added status field
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
            ->where('title', 'like', "%{$query}%")
            ->orWhere('content', 'like', "%{$query}%")
            ->orWhere('author', 'like', "%{$query}%")
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
                    'status' => $news->status, // Added status field
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
                    'status' => $news->status, // Added status field
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
        $news = News::findOrFail($newsId);
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
                    'status' => $news->status, // Added status field
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
                    'status' => $news->status, // Added status field
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
}
