<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// Eliminar cualquier importación relacionada con Livewire
// use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Eliminar cualquier registro relacionado con Livewire
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Eliminar cualquier código relacionado con Livewire
        // Por ejemplo:
        // Livewire::component('route-explorer', RouteExplorer::class);
    }
}
