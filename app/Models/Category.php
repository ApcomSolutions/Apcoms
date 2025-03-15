<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'is_active',
        'order'
    ];

    /**
     * Get the parent category
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get all subcategories (children)
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('order');
    }

    /**
     * Get all active subcategories
     */
    public function activeChildren()
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('order');
    }

    /**
     * Check if category is a parent (has no parent_id)
     */
    public function isParent()
    {
        return is_null($this->parent_id);
    }

    /**
     * Get insights in this category
     */
    public function insights()
    {
        return $this->hasMany(Insight::class);
    }

    /**
     * Get all insights in this category and all its subcategories
     */
    public function allInsights()
    {
        // Get direct insights
        $insightIds = $this->insights()->pluck('id')->toArray();

        // Get child categories' insight IDs
        foreach ($this->children as $child) {
            $insightIds = array_merge($insightIds, $child->insights()->pluck('id')->toArray());
        }

        // Return collection of all insights
        return Insight::whereIn('id', $insightIds);
    }

    /**
     * Auto-generate slug when creating/updating
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name, '-');
            }
        });
    }
}
