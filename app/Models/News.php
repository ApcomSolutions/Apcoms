<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\Support\HasSEO;

class News extends Model
{
    use HasFactory, HasSEO;

    protected $table = 'news';

    protected $fillable = [
        'title', 'slug', 'content', 'author', 'image_url', 'publish_date', 'news_category_id', 'status'
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug when saving
        static::saving(function ($news) {
            if (empty($news->slug) || $news->isDirty('title')) {
                $news->slug = Str::slug($news->title, '-');
            }
        });

        // Global scope to only show published articles on public routes
        static::addGlobalScope('published', function (Builder $builder) {
            // Check if we're in an admin route - if so, don't filter
            $isAdminRoute = request()->is('admin*') ||
                request()->is('api/admin*') ||
                request()->segment(1) === 'admin';

            // Also check if it's an API request from the admin dashboard
            $isAdminApiCall = request()->header('X-Admin-Request') === 'true';

            // Don't apply filter on admin routes or admin API calls
            if (!$isAdminRoute && !$isAdminApiCall) {
                $builder->where('status', 'published');
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    /**
     * Get the tracking records for this news item
     */
    public function trackings()
    {
        return $this->hasMany(NewsTracking::class);
    }

    /**
     * Get view count
     */
    public function viewCount()
    {
        return $this->trackings()->count();
    }

    /**
     * Get unique device count
     */
    public function uniqueViewCount()
    {
        return $this->trackings()->select('device_id')->distinct()->count('device_id');
    }

    /**
     * Get average read time in seconds
     */
    public function avgReadTime()
    {
        return $this->trackings()->avg('read_time_seconds') ?? 0;
    }
}
