<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // if (app()->environment('local')) {
        //     URL::forceScheme('https');
        // } 

            View::composer('*', function ($view) {
        $cart = json_decode(Cookie::get('cart', '[]'), true);
        $cartCount = collect($cart)->sum('quantity'); // Подсчитываем общее количество товаров

        $view->with('cartCount', $cartCount);
    });
    }
}
