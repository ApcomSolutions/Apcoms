<?php
// File: app/Providers/GoogleMapsServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\View\Components\GoogleMap;

class GoogleMapsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/google-maps.php', 'google-maps'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publikasikan konfigurasi
        $this->publishes([
            __DIR__.'/../../config/google-maps.php' => config_path('google-maps.php'),
        ], 'google-maps-config');

        // Daftarkan komponen Blade
        Blade::component('google-map', GoogleMap::class);
    }
}
