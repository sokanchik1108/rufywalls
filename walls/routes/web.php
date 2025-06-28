<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;





Route::get('/', [WebsiteController::class, 'website'])->name('website');

Route::get('/catalog', [WebsiteController::class, 'catalog'])->name('catalog');

Route::get('/product/{product}', [WebsiteController::class, 'show'])->name('product.show');

Route::get('/variant/{id}', [WebsiteController::class, 'variantData'])->name('variant.data');

Route::get('/cart', [WebsiteController::class, 'cart'])->name('cart');

Route::post('/cart/add', [WebsiteController::class, 'addToCart'])->name('cart.add');

Route::post('/cart/update/{variantId}', [WebsiteController::class, 'updateCart'])->name('cart.update');

Route::post('/cart/remove/{variantId}', [WebsiteController::class, 'removeFromCart'])->name('cart.remove');

Route::post('/cart/clear', [WebsiteController::class, 'clearCart'])->name('cart.clear');

Route::get('/cart/count', [WebsiteController::class, 'count']);

Route::get('/how-to-order', [WebsiteController::class, 'howToOrder'])->name('how-to-order');

Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');

Route::post('/checkout', [OrderController::class, 'submit'])->name('checkout.submit');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/product/create', [AdminController::class, 'create'])->name('form');
    Route::post('/products', [AdminController::class, 'store'])->name('products.store');
    Route::get('/database', [AdminController::class, 'index'])->name('database');
    Route::delete('/products/{id}', [AdminController::class, 'delete'])->name('products.destroy');
    Route::put('/products/{id}', [AdminController::class, 'update'])->name('products.update');
    Route::get('/orders', [AdminController::class, 'adminIndex'])->name('orders');
    Route::put('/orders/{id}/status', [AdminController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::delete('/orders/{id}', [AdminController::class, 'destroy'])->name('orders.destroy');
    Route::delete('/orders', [AdminController::class, 'clearAll'])->name('orders.clear');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('toggleAdmin');
});

Auth::routes();
Route::get('/admin', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
