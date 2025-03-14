<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\Support\HasSEO;

class NewsCategory extends Model
{
    use HasFactory, HasSEO;

    protected $table = 'news_categories';
    protected $fillable = ['name', 'slug', 'description'];

    public static function boot()
    {
        parent::boot();
        static::saving(function ($category) {
            // Generate slug if it's empty or if the name has changed
            if (empty($category->slug) || $category->isDirty('name')) {
                $category->slug = Str::slug($category->name, '-');
            }
        });
    }

    public function news()
    {
        return $this->hasMany(News::class, 'news_category_id');
    }
}
