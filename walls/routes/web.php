<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\OrderController;



Route::get('/', function () {
    return view('website');
});

Route::get('/product/create', [MainController::class, 'create'])->name('form');

Route::post('/products', [MainController::class, 'store'])->name('products.store');

Route::get('/database', [MainController::class, 'index'])->name('database');

Route::delete('/products/{id}', [MainController::class, 'destroy'])->name('products.destroy');

Route::put('/products/{id}', [MainController::class, 'update'])->name('products.update');

Route::get('/', [WebsiteController::class, 'website'])->name('website');

Route::get('/catalog' , [WebsiteController::class, 'catalog'])->name('catalog');

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

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/orders', [OrderController::class, 'adminIndex'])->name('orders');
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // ✅ Добавленный маршрут для удаления всех заказов
    Route::delete('/orders', [OrderController::class, 'clearAll'])->name('orders.clear');
});















