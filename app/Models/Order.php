<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'order_number', 'status', 'subtotal', 'discount',
        'tax', 'shipping_cost', 'total', 'payment_status', 'payment_method',
        'payment_id', 'shipping_address_id', 'billing_address_id',
        'coupon_code', 'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public static function generateOrderNumber(): string
    {
        return 'PW-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function canTransitionTo(string $newStatus): bool
    {
        $transitions = [
            'pending' => ['paid', 'cancelled'],
            'paid' => ['shipped', 'cancelled'],
            'shipped' => ['delivered'],
            'delivered' => [],
            'cancelled' => [],
        ];

        return in_array($newStatus, $transitions[$this->status] ?? []);
    }
}
