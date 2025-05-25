<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\UidFinderService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register the UidFinderService
        $this->app->singleton(UidFinderService::class, function ($app) {
            return new UidFinderService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
