<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'size', 'color', 'sku', 'price',
        'sale_price', 'discount_percent', 'stock_quantity', 'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $appends = ['effective_price', 'variant_label'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getEffectivePriceAttribute()
    {
        if ($this->sale_price && $this->sale_price > 0) {
            return $this->sale_price;
        }
        if ($this->discount_percent && $this->discount_percent > 0) {
            return round($this->price * (1 - $this->discount_percent / 100), 2);
        }
        return $this->price;
    }

    public function getVariantLabelAttribute(): string
    {
        $parts = [];
        if ($this->size) $parts[] = "Size: {$this->size}";
        if ($this->color) $parts[] = "Color: {$this->color}";
        return implode(' | ', $parts);
    }

    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    public function isLowStock(int $threshold = 5): bool
    {
        return $this->stock_quantity > 0 && $this->stock_quantity <= $threshold;
    }

    public function decrementStock(int $quantity): bool
    {
        if ($this->stock_quantity >= $quantity) {
            $this->decrement('stock_quantity', $quantity);
            return true;
        }
        return false;
    }

    public function incrementStock(int $quantity): void
    {
        $this->increment('stock_quantity', $quantity);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeLowStock($query, int $threshold = 5)
    {
        return $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', $threshold);
    }
}
