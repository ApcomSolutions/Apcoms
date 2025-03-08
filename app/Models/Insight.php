<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Insight extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul', 'slug', 'isi', 'penulis', 'image_url', 'TanggalTerbit', 'category_id'
    ];

    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }


    /**
     * Get the tracking records for this insight
     */
    public function trackings()
    {
        return $this->hasMany(InsightTracking::class);
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
        static::saving(function ($insight) {
            $insight -> slug = Str::slug($insight -> judul, '-');
        });
    }
}
