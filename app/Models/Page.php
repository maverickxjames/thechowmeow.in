<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'meta_title', 'meta_description', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->name);
            }
        });
    }

    public function menuItems()
    {
        return $this->morphMany(MenuItem::class, 'linkable');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
