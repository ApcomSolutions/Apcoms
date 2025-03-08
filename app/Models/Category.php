<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model{
    use HasFactory;
    protected $table = 'categories';
    protected $fillable = ['name', 'slug',];

    public static function boot()
    {
        parent::boot();
        static::saving(function ($insight) {
            $insight -> slug = Str::slug($insight -> judul, '-');
        });
    }
}
