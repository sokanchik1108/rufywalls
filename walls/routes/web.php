<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\WebsiteController;

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

Route::get('/product/{id}', [WebsiteController::class, 'show'])->name('product.show');





