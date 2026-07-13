<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Vercel terminates TLS and forwards HTTP to the function, so force
        // HTTPS on generated URLs to avoid mixed-content blocking of assets.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
