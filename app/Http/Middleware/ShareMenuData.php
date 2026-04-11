<?php

namespace App\Http\Middleware;

use App\Services\CartService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShareMenuData
{
    public function handle(Request $request, Closure $next): Response
    {
        $menuService = app(\App\Services\MenuService::class);
        $categoryService = app(\App\Services\CategoryService::class);
        $cartService = app(\App\Services\CartService::class);

        $menu = $menuService->getMenu('header');
        $categories = $categoryService->getTree();
        $cart = $cartService->getCart();

        view()->share('headerMenu', $menu);
        view()->share('categoryTree', $categories);
        view()->share('cart', $cart);

        return $next($request);
    }
}
