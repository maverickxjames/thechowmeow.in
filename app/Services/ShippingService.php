<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Setting;
use Illuminate\Support\Facades\Session;

class ShippingService
{
    public function detectShippingType(): string
    {
        $currency = session('currency', 'INR');
        return $currency === 'USD' ? 'international' : 'domestic';
    }

    public function calculateShipping(Cart $cart, string $type): float
    {
        $subtotal = $cart->total;
        
        // 1. Check for Free Shipping Threshold (Domestic only by default or both?)
        // Let's check settings for threshold
        $threshold = (float) (Setting::where('key', 'free_shipping_threshold')->value('value') ?? 0);
        if ($threshold > 0 && $subtotal >= $threshold) {
            return 0.00;
        }

        $totalWeight = 0;
        $anyWeightDefined = false;

        foreach ($cart->items as $item) {
            $weight = (float) ($item->variant->product->weight ?? 0);
            if ($weight > 0) {
                $anyWeightDefined = true;
            }
            $totalWeight += $weight * $item->quantity;
        }

        $config = $this->getShippingConfig();
        $typeConfig = $config[$type] ?? [];
        
        if (!$anyWeightDefined || empty($typeConfig['slabs'])) {
            return (float) ($typeConfig['fixed_fee'] ?? 0);
        }

        // Find matching slab
        $shippingFee = (float) ($typeConfig['fixed_fee'] ?? 0);
        $slabs = $typeConfig['slabs'] ?? [];
        
        // Sort slabs by min_weight just in case
        usort($slabs, fn($a, $b) => $a['min_weight'] <=> $b['min_weight']);

        $calcMethod = Setting::where('key', 'shipping_calc_method')->value('value') ?? 'flat';

        foreach ($slabs as $slab) {
            if ($totalWeight >= $slab['min_weight'] && $totalWeight <= $slab['max_weight']) {
                $shippingFee = (float) $slab['fee'];
                return $calcMethod === 'per_kg' ? ($shippingFee * $totalWeight) : $shippingFee;
            }
        }

        // If weight exceeds all slabs, use the fee of the largest slab
        if (!empty($slabs)) {
            $lastSlab = end($slabs);
            if ($totalWeight > $lastSlab['max_weight']) {
                $shippingFee = (float) $lastSlab['fee'];
                return $calcMethod === 'per_kg' ? ($shippingFee * $totalWeight) : $shippingFee;
            }
        }

        return $calcMethod === 'per_kg' ? ($shippingFee * $totalWeight) : $shippingFee;
    }

    public function getShippingConfig(): array
    {
        $config = Setting::where('key', 'shipping_config')->value('value');
        if (!$config) {
            return [
                'domestic' => [
                    'fixed_fee' => 99,
                    'slabs' => []
                ],
                'international' => [
                    'fixed_fee' => 500,
                    'slabs' => []
                ]
            ];
        }
        return json_decode($config, true);
    }
}
