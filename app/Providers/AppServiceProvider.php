<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        // Establecer longitud predeterminada de cadena para MySQL
        Schema::defaultStringLength(191);
        
        // Deshabilitar caché de vistas en entorno de desarrollo
        if (app()->environment('local')) {
            config(['view.cache' => false]);
        }
    }
}
