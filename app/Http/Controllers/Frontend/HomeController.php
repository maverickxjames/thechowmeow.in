<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Review;
use App\Services\CategoryService;
use App\Services\ProductService;

class HomeController extends Controller
{
    public function __construct(
        protected ProductService $productService,
        protected CategoryService $categoryService
    ) {}

    public function index()
    {
        $banners = Banner::active()->orderBy('sort_order')->get();
        $categories = $this->categoryService->getTree();
        $featuredProducts = $this->productService->getFeatured(8);
        $bestSellers = $this->productService->getBestSellers(8);
        $latestReviews = Review::approved()
            ->with(['user', 'product'])
            ->latest()
            ->take(6)
            ->get();

        return view('frontend.home', compact(
            'banners', 'categories', 'featuredProducts', 'bestSellers', 'latestReviews'
        ));
    }
}
