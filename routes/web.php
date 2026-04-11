<?php

use App\Http\Controllers\Frontend;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Frontend Routes
|--------------------------------------------------------------------------
*/
Route::middleware('share.menu')->group(function () {
    Route::get('/', [Frontend\HomeController::class, 'index'])->name('home');

    // Products
    Route::get('/products', [Frontend\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{slug}', [Frontend\ProductController::class, 'show'])->name('products.show');

    // Categories
    Route::get('/category/{slug}', [Frontend\CategoryController::class, 'show'])->name('category.show');

    // CMS Pages
    Route::get('/page/{slug}', [Frontend\PageController::class, 'show'])->name('page.show');

    // Cart
    Route::get('/cart', [Frontend\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [Frontend\CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update', [Frontend\CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove', [Frontend\CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/coupon', [Frontend\CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
    Route::delete('/cart/coupon', [Frontend\CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

    // Currency Switcher
    Route::get('/currency/switch/{currency}', [Frontend\CurrencyController::class, 'switch'])->name('currency.switch');

    // Auth-required frontend routes
    Route::middleware('auth')->group(function () {
        Route::get('/checkout', [Frontend\CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [Frontend\CheckoutController::class, 'store'])->name('checkout.store');
        Route::get('/checkout/payment/{order}', [Frontend\CheckoutController::class, 'payment'])->name('checkout.payment');
        Route::post('/checkout/verify', [Frontend\CheckoutController::class, 'verify'])->name('checkout.verify');
        Route::get('/checkout/cancel/{order}', [Frontend\CheckoutController::class, 'cancel'])->name('checkout.cancel');

        Route::get('/orders', [Frontend\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{orderNumber}', [Frontend\OrderController::class, 'show'])->name('orders.show');

        Route::post('/reviews', [Frontend\ReviewController::class, 'store'])->name('reviews.store');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['auth', 'admin'])
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Categories
        Route::resource('categories', Admin\CategoryController::class);
        Route::post('categories/order', [Admin\CategoryController::class, 'updateOrder'])->name('categories.order');

        // Products
        Route::get('products/export', [Admin\ProductController::class, 'export'])->name('products.export');
        Route::resource('products', Admin\ProductController::class);
        Route::post('products/{product}/variants', [Admin\ProductController::class, 'storeVariant'])->name('products.variants.store');
        Route::put('variants/{variant}', [Admin\ProductController::class, 'updateVariant'])->name('variants.update');
        Route::delete('variants/{variant}', [Admin\ProductController::class, 'destroyVariant'])->name('variants.destroy');
        Route::delete('products/{product}/images', [Admin\ProductController::class, 'destroyImage'])->name('products.images.destroy');

        // Orders
        Route::get('orders/export', [Admin\OrderController::class, 'export'])->name('orders.export');
        Route::get('orders', [Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
        Route::put('orders/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.status');

        // Menus
        Route::get('menus', [Admin\MenuController::class, 'index'])->name('menus.index');
        Route::post('menus', [Admin\MenuController::class, 'store'])->name('menus.store');
        Route::delete('menus/{menu}', [Admin\MenuController::class, 'destroy'])->name('menus.destroy');
        Route::post('menu-items', [Admin\MenuController::class, 'storeItem'])->name('menu-items.store');
        Route::put('menu-items/{menuItem}', [Admin\MenuController::class, 'updateItem'])->name('menu-items.update');
        Route::delete('menu-items/{menuItem}', [Admin\MenuController::class, 'destroyItem'])->name('menu-items.destroy');
        Route::post('menus/reorder', [Admin\MenuController::class, 'reorder'])->name('menus.reorder');

        // Banners
        Route::resource('banners', Admin\BannerController::class);

        // Pages
        Route::resource('pages', Admin\PageController::class);

        // Reviews
        Route::get('reviews/export', [Admin\ReviewController::class, 'export'])->name('reviews.export');
        Route::get('reviews', [Admin\ReviewController::class, 'index'])->name('reviews.index');
        Route::put('reviews/{review}/approve', [Admin\ReviewController::class, 'approve'])->name('reviews.approve');
        Route::put('reviews/{review}/reject', [Admin\ReviewController::class, 'reject'])->name('reviews.reject');
        Route::delete('reviews/{review}', [Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

        // Inventory
        Route::get('inventory/export', [Admin\InventoryController::class, 'export'])->name('inventory.export');
        Route::get('inventory', [Admin\InventoryController::class, 'index'])->name('inventory.index');
        Route::put('inventory/update', [Admin\InventoryController::class, 'updateStock'])->name('inventory.update');
        Route::put('inventory/bulk', [Admin\InventoryController::class, 'bulkUpdate'])->name('inventory.bulk');

        // Coupons
        Route::get('coupons/export', [Admin\CouponController::class, 'export'])->name('coupons.export');
        Route::resource('coupons', Admin\CouponController::class);

        // System Settings
        Route::get('settings', [Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [Admin\SettingController::class, 'store'])->name('settings.store');
    });

require __DIR__.'/auth.php';
