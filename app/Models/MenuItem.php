<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_id', 'parent_id', 'title', 'url', 'type',
        'linkable_type', 'linkable_id', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort_order');
    }

    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getResolvedUrlAttribute(): string
    {
        if ($this->type === 'category' && $this->linkable) {
            return route('category.show', $this->linkable->slug);
        }
        if ($this->type === 'page' && $this->linkable) {
            return route('page.show', $this->linkable->slug);
        }
        return $this->url ?? '#';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
