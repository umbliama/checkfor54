<?php

namespace App\Providers;

use Inertia\Inertia;
use Auth;
use Illuminate\Support\ServiceProvider;
use App\Services\CustomChatifyMessenger;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('MyChatifyMessenger', function ($app) {
            return new CustomChatifyMessenger();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inertia::share([
            // Always share the authenticated user
            'user' => function () {
                return Auth::user() ? Auth::user()->only('id', 'name', 'email', 'isAdmin') : null;
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
