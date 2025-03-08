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

    public static function boot()
    {
        parent::boot();
        static::saving(function ($insight) {
            $insight -> slug = Str::slug($insight -> judul, '-');
        });
    }
}
