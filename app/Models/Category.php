<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'name', 'slug', 'parent_id', 'description', 'image',
        'is_active', 'sort_order', 'meta_title', 'meta_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withTimestamps();
    }

    public function menuItems()
    {
        return $this->morphMany(MenuItem::class, 'linkable');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function getAncestors(): array
    {
        $ancestors = [];
        $parent = $this->parent;
        while ($parent) {
            array_unshift($ancestors, $parent);
            $parent = $parent->parent;
        }
        return $ancestors;
    }

    public function getFullSlugAttribute(): string
    {
        $slugs = [];
        $category = $this;
        while ($category) {
            array_unshift($slugs, $category->slug);
            $category = $category->parent;
        }
        return implode('/', $slugs);
    }
}
