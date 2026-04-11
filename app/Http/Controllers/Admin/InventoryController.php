<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(protected InventoryService $inventoryService) {}

    public function index(Request $request)
    {
        $summary = $this->inventoryService->getInventorySummary();

        $tab    = $request->input('tab', 'low_stock');
        $search = $request->input('search');

        $query = ProductVariant::with('product')
            ->where('is_active', true)
            ->when($search, fn($q, $v) =>
                $q->where('sku', 'like', "%{$v}%")
                  ->orWhereHas('product', fn($pq) => $pq->where('name', 'like', "%{$v}%")));

        if ($tab === 'out_of_stock') {
            $variants = (clone $query)->where('stock_quantity', '<=', 0)
                ->orderBy('stock_quantity')->paginate(20)->withQueryString();
        } elseif ($tab === 'low_stock') {
            $variants = (clone $query)->where('stock_quantity', '>', 0)
                ->where('stock_quantity', '<=', 5)
                ->orderBy('stock_quantity')->paginate(20)->withQueryString();
        } else {
            $variants = (clone $query)->orderBy('stock_quantity', 'asc')
                ->paginate(20)->withQueryString();
        }

        return view('admin.inventory.index', compact('summary', 'variants', 'tab'));
    }

    public function export(Request $request)
    {
        $tab    = $request->input('tab', 'low_stock');
        $search = $request->input('search');

        $query = ProductVariant::with('product')
            ->where('is_active', true)
            ->when($search, fn($q, $v) =>
                $q->where('sku', 'like', "%{$v}%")
                  ->orWhereHas('product', fn($pq) => $pq->where('name', 'like', "%{$v}%")));

        if ($tab === 'out_of_stock') {
            $variants = (clone $query)->where('stock_quantity', '<=', 0)->orderBy('stock_quantity')->get();
        } elseif ($tab === 'low_stock') {
            $variants = (clone $query)->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 5)->orderBy('stock_quantity')->get();
        } else {
            $variants = (clone $query)->orderBy('stock_quantity', 'asc')->get();
        }

        $format = $request->input('format', 'csv');
        $filename = 'inventory_' . $tab . '_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($variants) {
            $out = fopen('php://output', 'w');
            // Add BOM for accurate Excel UTF-8 representation
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Product', 'SKU', 'Size', 'Color', 'Stock Qty', 'Price']);
            foreach ($variants as $v) {
                fputcsv($out, [
                    $v->product->name ?? 'N/A',
                    $v->sku,
                    $v->size ?? '',
                    $v->color ?? '',
                    $v->stock_quantity,
                    $v->price,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function updateStock(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $this->inventoryService->updateStock($request->variant_id, $request->stock_quantity);

        return back()->with('success', 'Stock updated.');
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'updates' => 'required|array',
            'updates.*.id' => 'required|exists:product_variants,id',
            'updates.*.stock_quantity' => 'required|integer|min:0',
        ]);

        $count = $this->inventoryService->bulkUpdateStock($request->updates);

        return back()->with('success', "{$count} variants updated.");
    }
}
