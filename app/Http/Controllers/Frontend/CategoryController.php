<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService,
        protected ProductService $productService
    ) {}

    public function show(Request $request, string $slug)
    {
        $category = $this->categoryService->getBySlug($slug);
        if (!$category) abort(404);

        $filters = array_merge($request->all(), ['category_id' => $category->id]);
        $products = $this->productService->getProducts($filters);
        $subcategories = $category->children()->active()->get();

        return view('frontend.category.show', compact('category', 'products', 'subcategories'));
    }
}
