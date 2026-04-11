<?php

namespace App\Services;

use App\Models\ProductVariant;

class InventoryService
{
    public function getLowStockVariants(int $threshold = 5, int $perPage = 20)
    {
        return ProductVariant::with('product')
            ->where('is_active', true)
            ->where('stock_quantity', '<=', $threshold)
            ->where('stock_quantity', '>', 0)
            ->orderBy('stock_quantity', 'asc')
            ->paginate($perPage);
    }

    public function getOutOfStockVariants(int $perPage = 20)
    {
        return ProductVariant::with('product')
            ->where('is_active', true)
            ->where('stock_quantity', '<=', 0)
            ->paginate($perPage);
    }

    public function updateStock(int $variantId, int $quantity): ProductVariant
    {
        $variant = ProductVariant::findOrFail($variantId);
        $variant->update(['stock_quantity' => $quantity]);
        return $variant;
    }

    public function bulkUpdateStock(array $updates): int
    {
        $count = 0;
        foreach ($updates as $update) {
            ProductVariant::where('id', $update['id'])
                ->update(['stock_quantity' => $update['stock_quantity']]);
            $count++;
        }
        return $count;
    }

    public function getInventorySummary(): array
    {
        return [
            'total_variants' => ProductVariant::active()->count(),
            'in_stock' => ProductVariant::active()->where('stock_quantity', '>', 5)->count(),
            'low_stock' => ProductVariant::active()->lowStock()->count(),
            'out_of_stock' => ProductVariant::active()->where('stock_quantity', '<=', 0)->count(),
            'total_stock_value' => ProductVariant::active()->selectRaw('SUM(price * stock_quantity) as value')->value('value') ?? 0,
        ];
    }
}
