<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image_url',
        'is_carousel',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_carousel' => 'boolean',
        'order' => 'integer'
    ];
}
