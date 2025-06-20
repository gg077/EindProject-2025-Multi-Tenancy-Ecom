<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate; // Laravel's toegangssysteem (permissions/abilities).
use Illuminate\Support\ServiceProvider; // de basisproviderklasse die je moet uitbreiden
use Livewire\Features\SupportFileUploads\FilePreviewController; // van Livewire, gebruikt voor bestandspreviews
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain; // Middleware om multitenancy te initialiseren op basis van het domein.

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
        // Het stelt globale toegangsregels in. Als een gebruiker superadmin of tenant-superadmin is, geven we automatisch toegang tot alles.
        // Dit voorkomt dat we in elke Gate of Policy afzonderlijk superadmin-controle moeten doen.
        Gate::before(function (User $user, $ability) {
            // if the permission is checked for a tenant super super-admin, Allow everything for /super-admin/*
            if($user->isTenantSuperAdmin()) {
                return true;
            }

            if($user->isSuperAdmin()) {
                return true;
            }
            // Zonder null wordt er niks meer gecheckt. Met null zeg je expliciet: “Ga verder met normale checks.”
            return null;
        });

        //Deze regel zorgt ervoor dat bestandspreviews in een multitenant Laravel-app correct werken door ervoor te zorgen dat de juiste tenant
        // wordt geladen op basis van het domein. Zonder deze middleware zou Livewire niet weten in welke tenant-context het moet werken,
        // wat kan leiden tot foute data of toegangsproblemen.
        FilePreviewController::$middleware = ['web', InitializeTenancyByDomain::class];
        //filePreviewController is van livewire, InitializeTenancyByDomain is van stancl
    }
}
