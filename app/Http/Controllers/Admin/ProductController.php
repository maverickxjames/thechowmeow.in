<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    public function index(Request $request)
    {
        $products = Product::with(['primaryImage', 'activeVariants', 'categories'])
            ->when($request->search, fn($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->when($request->status === 'active',  fn($q) => $q->where('is_active', true))
            ->when($request->status === 'draft',   fn($q) => $q->where('is_active', false))
            ->when($request->featured === '1',     fn($q) => $q->where('is_featured', true))
            ->when($request->category_id, fn($q, $v) => $q->whereHas('categories', fn($cq) => $cq->where('categories.id', $v)))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function export(Request $request)
    {
        $products = Product::with(['categories', 'activeVariants'])
            ->when($request->search, fn($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->when($request->status === 'active',  fn($q) => $q->where('is_active', true))
            ->when($request->status === 'draft',   fn($q) => $q->where('is_active', false))
            ->when($request->featured === '1',     fn($q) => $q->where('is_featured', true))
            ->when($request->category_id, fn($q, $v) => $q->whereHas('categories', fn($cq) => $cq->where('categories.id', $v)))
            ->latest()
            ->get();

        $filename = 'products_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($products) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Name', 'Base Price', 'Total Stock', 'Variants', 'Categories', 'Status', 'Featured', 'Created At']);
            foreach ($products as $p) {
                fputcsv($out, [
                    $p->id,
                    $p->name,
                    $p->base_price,
                    $p->activeVariants->sum('stock_quantity'),
                    $p->activeVariants->count(),
                    $p->categories->pluck('name')->join(', '),
                    $p->is_active ? 'Active' : 'Draft',
                    $p->is_featured ? 'Yes' : 'No',
                    $p->created_at->format('Y-m-d'),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'base_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');

        $product = $this->productService->create($validated);

        if ($request->hasFile('images')) {
            $this->productService->uploadImages($product, $request->file('images'), true);
        }

        return redirect()->route('admin.products.edit', $product)->with('success', 'Product created. Now add variants.');
    }

    public function edit(Product $product)
    {
        $product->load(['images', 'variants', 'categories']);
        $categories = Category::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'base_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');

        $this->productService->update($product, $validated);

        if ($request->hasFile('images')) {
            $this->productService->uploadImages($product, $request->file('images'));
        }

        return redirect()->route('admin.products.edit', $product)->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->productService->delete($product);
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    // Variant management
    public function storeVariant(Request $request, Product $product)
    {
        $validated = $request->validate([
            'size' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:50',
            'sku' => 'required|string|unique:product_variants',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'discount_percent' => 'nullable|integer|min:0|max:100',
            'stock_quantity' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') || !$request->has('_method');
        $validated['product_id'] = $product->id;

        ProductVariant::create($validated);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Variant added successfully.');
    }

    public function updateVariant(Request $request, ProductVariant $variant)
    {
        $validated = $request->validate([
            'size' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:50',
            'sku' => 'required|string|unique:product_variants,sku,' . $variant->id,
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'discount_percent' => 'nullable|integer|min:0|max:100',
            'stock_quantity' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $variant->update($validated);

        return redirect()->route('admin.products.edit', $variant->product)->with('success', 'Variant updated.');
    }

    public function destroyVariant(ProductVariant $variant)
    {
        $product = $variant->product;
        $variant->delete();
        return redirect()->route('admin.products.edit', $product)->with('success', 'Variant deleted.');
    }

    public function destroyImage(Request $request, Product $product)
    {
        $image = $product->images()->findOrFail($request->image_id);
        $this->productService->deleteImage($image);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Image deleted.');
    }
}
