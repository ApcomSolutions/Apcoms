<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory;

    protected $table = 'news';

    protected $fillable = [
        'title', 'slug', 'content', 'author', 'image_url', 'publish_date', 'news_category_id', 'status'
    ];

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

    public static function boot()
    {
        parent::boot();
        static::saving(function ($news) {
            $news->slug = Str::slug($news->title, '-');
        });
    }
}
