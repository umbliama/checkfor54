<?php

namespace App\Providers;

use Inertia\Inertia;
use Auth;
use Illuminate\Support\ServiceProvider;

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
    public function boot(): void
    {
        Inertia::share([
            // Always share the authenticated user
            'user' => function () {
                return Auth::user() ? Auth::user()->only('id', 'name', 'email') : null;
            },
            // You can also share flash messages or errors
            'flash' => function () {
                return [
                    'success' => session('success'),
                    'error' => session('error'),
                ];
            },
        ]); 

    }
}
