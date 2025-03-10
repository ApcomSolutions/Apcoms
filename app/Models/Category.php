<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model{
    use HasFactory;
    protected $table = 'categories';
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

    public function insights()
    {
        return $this->hasMany(Insight::class);
    }
}
