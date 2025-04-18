<?php

namespace App\Providers;

use App\Support\Setting;
use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the Setting class as a singleton
        $this->app->singleton(Setting::class, function ($app) {
            return new Setting();
        });

        // Bind 'setting' alias to the Setting class
        $this->app->alias(Setting::class, 'setting');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
} 