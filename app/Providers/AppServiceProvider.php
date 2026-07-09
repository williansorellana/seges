<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\RoutePlanning;
use App\Policies\RoutePlanningPolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra cualquier servicio de la aplicación.
     */
    public function register(): void
    {
        //
    }

    /**
     * Inicializa cualquier servicio de la aplicación.
     */
    public function boot(): void
    {
        Gate::policy(RoutePlanning::class, RoutePlanningPolicy::class);
    }
}
