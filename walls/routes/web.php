<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\WarehouseController;

Route::get('/address', function () {
    return view('navigations.address');
});

Route::get('/delivery', function () {
    return view('navigations.delivery');
});

Route::get('/about-products', function () {
    return view('navigations.about-products');
});

Route::get('/welcome', function () {
    return view('welcome');
});








Route::get('/calculator', [WebsiteController::class, 'Calculator'])->name('calculator');

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

Route::get('/make-me-admin', [\App\Http\Controllers\AdminController::class, 'makeMeAdmin'])->middleware('auth')->name('make-me-admin');

Route::get('/admin/batches/by-sku/{sku}', [SaleController::class, 'bySku'])->middleware('auth');

Route::get('/admin/sales/warehouses', [SaleController::class, 'selectWarehouse'])->name('admin.sales.select_warehouse');


Route::get('/admin/warehouses/{warehouse}/stocks', [WarehouseController::class, 'editWarehouse'])->middleware('auth')->name('admin.stocks.edit_warehouse');

Route::post('/admin/warehouses/{warehouse}/stocks', [WarehouseController::class, 'updateWarehouse'])->middleware('auth')->name('admin.stocks.update_warehouse');

Route::get('/admin/warehouses', [WarehouseController::class, 'listWarehouses'])->middleware('auth')->name('admin.stocks.warehouses');

Route::get('/admin/stocks/view-all', [WarehouseController::class, 'viewAllBatches'])->middleware('auth')->name('admin.stocks.view_all');

Route::post('/admin/batches/add', [WarehouseController::class, 'addBatchToWarehouse'])->middleware('auth')->name('admin.batches.add');

Route::post('/admin/batches/delete', [WarehouseController::class, 'removeBatch'])->middleware('auth')->name('admin.batches.remove');

Route::post('/admin/batches/update', [WarehouseController::class, 'updateQuantity'])->middleware('auth')->name('admin.batches.update');

Route::post('/admin/warehouses', [WarehouseController::class, 'storeWarehouse'])->middleware('auth')->name('warehouses.store');

Route::delete('/warehouses/{warehouse}', [WarehouseController::class, 'destroyWarehouse'])->middleware('auth')->name('warehouses.destroy');

Route::get('/admin/warehouse/{id}/batches', [WarehouseController::class, 'batchOverview'])->middleware('auth')->name('admin.stocks.batch_overview');

Route::get('/admin/warehouses/overview', [WarehouseController::class, 'stockWarehousesPage'])->middleware('auth')->name('admin.warehouses.overview');

Route::get('/admin/variants/autocomplete', [AdminController::class, 'autocomplete'])->name('admin.variants.autocomplete');



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
    Route::delete('/variants/{id}', [AdminController::class, 'deleteVariant'])->name('variant.delete');
    Route::get('/variants/search', [AdminController::class, 'searchVariants'])->name('variants.search');
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');
});

Auth::routes();
Route::get('/admin', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
