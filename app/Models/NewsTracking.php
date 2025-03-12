<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'news_id',
        'device_id',         // Anonymized device identifier (hashed)
        'user_agent',        // Browser/device info
        'ip_hash',           // Hashed IP address for general location tracking without storing actual IP
        'read_time_seconds', // Time spent reading
        'referrer',          // Where the reader came from
        'is_completed',      // Whether they read to the end (or spent enough time reading)
    ];

    /**
     * Get the news item that this tracking belongs to
     */
    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
