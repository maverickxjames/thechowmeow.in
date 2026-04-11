<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    public function index(Request $request)
    {
        $products = $this->productService->getProducts($request->all());
        return view('frontend.products.index', compact('products'));
    }

    public function show(string $slug)
    {
        $product = $this->productService->getBySlug($slug);
        if (!$product) abort(404);

        $relatedProducts = Product::active()
            ->whereHas('categories', function ($q) use ($product) {
                $q->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->where('id', '!=', $product->id)
            ->with(['primaryImage', 'activeVariants'])
            ->take(4)
            ->get();

        return view('frontend.products.show', compact('product', 'relatedProducts'));
    }
}
