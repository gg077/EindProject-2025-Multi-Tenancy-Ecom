<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Stancl\JobPipeline\JobPipeline;
use Stancl\Tenancy\Events;
use Stancl\Tenancy\Jobs;
use Stancl\Tenancy\Listeners;
use Stancl\Tenancy\Middleware;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain; // Als je tenant1.multi-tenant.pk bezoekt, de juiste tenant wordt geladen.

class TenancyServiceProvider extends ServiceProvider
{
    // Dit is optioneel als je een namespace wilt gebruiken voor je tenant controllers.
    public static string $controllerNamespace = '';

    // Alle tenancy events en gekoppelde listeners/jobs
    public function events()
    {
        return [
            // Event: nieuwe tenant wordt aangemaakt
            Events\CreatingTenant::class => [],
            // Event: tenant is succesvol aangemaakt
            Events\TenantCreated::class => [
                // Als een tenant wordt aangemaakt, voer dan automatisch deze jobs uit
                // JobPipeline::make([
                //     Jobs\CreateDatabase::class, // maak apparte database voor elk tenant
                //     Jobs\MigrateDatabase::class,
                // ])->send(function (Events\TenantCreated $event) {
                //     return $event->tenant;
                // })->shouldBeQueued(false),
            ],
            Events\SavingTenant::class => [],
            Events\TenantSaved::class => [],
            Events\UpdatingTenant::class => [],
            Events\TenantUpdated::class => [],
            Events\DeletingTenant::class => [],

            // Event: tenant is verwijderd, je kunt de database opruimen
            Events\TenantDeleted::class => [
                // Wordt uitgevoerd wanneer een tenant wordt verwijderd
                JobPipeline::make([
                    // Jobs\DeleteDatabase::class,
                ])->send(function (Events\TenantDeleted $event) {
                    return $event->tenant;
                })->shouldBeQueued(false),
            ],

            // Event: domein wordt gekoppeld of aangepast
            Events\CreatingDomain::class => [],
            Events\DomainCreated::class => [],
            Events\SavingDomain::class => [],
            Events\DomainSaved::class => [],
            Events\UpdatingDomain::class => [],
            Events\DomainUpdated::class => [],
            Events\DeletingDomain::class => [],
            Events\DomainDeleted::class => [],

            // Event: database acties (bijv. migraties)
            Events\DatabaseCreated::class => [],
            Events\DatabaseMigrated::class => [],
            Events\DatabaseSeeded::class => [],
            Events\DatabaseRolledBack::class => [],
            Events\DatabaseDeleted::class => [],

            // Event: tenant wordt geladen (bijv. op basis van domein)
            Events\InitializingTenancy::class => [],
            Events\TenancyInitialized::class => [
                Listeners\BootstrapTenancy::class, // Zet Laravel context om naar de tenant-omgeving
            ],

            // Event: tenant sessie eindigt, terug naar centrale app
            Events\EndingTenancy::class => [],
            Events\TenancyEnded::class => [
                Listeners\RevertToCentralContext::class, // Reset naar centrale app-context
                function (Events\TenancyEnded $event) {
                    // Herstel de cache key voor Spatie permissions naar de centrale waarde
                    $permissionRegistrar = app(\Spatie\Permission\PermissionRegistrar::class);
                    $permissionRegistrar->cacheKey = 'spatie.permission.cache';
                }
            ],
            Events\BootstrappingTenancy::class => [],
            Events\TenancyBootstrapped::class => [
                function (Events\TenancyBootstrapped $event) {
                    // Zet de permission cache key specifiek voor deze tenant
                    $permissionRegistrar = app(\Spatie\Permission\PermissionRegistrar::class);
                    $permissionRegistrar->cacheKey = 'spatie.permission.cache.tenant.' . $event->tenancy->tenant->getTenantKey();
                }
            ],
            Events\RevertingToCentralContext::class => [],
            Events\RevertedToCentralContext::class => [],

            // Event: resource is gewijzigd in een andere database dan origineel
            Events\SyncedResourceSaved::class => [
                Listeners\UpdateSyncedResource::class,
            ],
            Events\SyncedResourceChangedInForeignDatabase::class => [],
        ];
    }

    // Wordt automatisch aangeroepen bij het opstarten van de app
    public function boot()
    {
        $this->bootEvents(); // Koppel de events aan hun listeners
        $this->mapRoutes(); // Laad tenant-specifieke routes
        $this->makeTenancyMiddlewareHighestPriority(); // Zorg dat tenancy-middleware het eerst wordt uitgevoerd
        $this->livewireUpdateRoute(); // Pas Livewire update route aan op basis van domein

        // Elke tenant kan zijn eigen config-instellingen hebben (zoals eigen logo, Stripe-sleutels, site-naam, enz).
        \Stancl\Tenancy\Features\TenantConfig::$storageToConfigMap = [
            'stripe_publishable_key' => 'cashier.key',
            'stripe_secret_key' => 'cashier.secret',
            'website_name' => 'WEBSITE_NAME',
            'website_logo' => 'WEBSITE_LOGO',
        ];
        
        // toon 404 als er geen tenant gevonden wordt
        \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::$onFail = function ($exception, $request, $next) {
            abort(404, 'Tenant not found or not accessible.');
        };
    }

    // Verbind alle events met hun listeners of jobpipelines
    protected function bootEvents()
    {
        foreach ($this->events() as $event => $listeners) {
            foreach ($listeners as $listener) {
                if ($listener instanceof JobPipeline) {
                    $listener = $listener->toListener(); // Zet JobPipeline om naar een closure of class listener
                }
                Event::listen($event, $listener); // Registreer event listener
            }
        }
    }

    // Laad de routes voor tenants uit routes/tenant.php als dit bestand bestaat
    protected function mapRoutes()
    {
        $this->app->booted(function () {
            if (file_exists(base_path('routes/tenant.php'))) {
                Route::namespace(static::$controllerNamespace)
                    ->group(base_path('routes/tenant.php'));
            }
        });
    }

    // Zorg dat de tenancy-middleware als eerste wordt uitgevoerd
    protected function makeTenancyMiddlewareHighestPriority()
    {
        $tenancyMiddleware = [
            Middleware\PreventAccessFromCentralDomains::class, // Blokkeert toegang tot tenants via centrale domeinen
            Middleware\InitializeTenancyByDomain::class,
            Middleware\InitializeTenancyBySubdomain::class,
            Middleware\InitializeTenancyByDomainOrSubdomain::class,
            Middleware\InitializeTenancyByPath::class,
            Middleware\InitializeTenancyByRequestData::class,
        ];

        // Zorg dat deze middleware als EERSTE worden uitgevoerd
        foreach (array_reverse($tenancyMiddleware) as $middleware) {
            $this->app[\Illuminate\Contracts\Http\Kernel::class]->prependToMiddlewarePriority($middleware);
        }
    }

    // Zorg dat Livewire update-verzoeken per tenant worden afgehandeld
    protected function livewireUpdateRoute()
    {
        Livewire::setUpdateRoute(function ($handle) {
            // Als het centrale domein gebruikt wordt
            if(in_array(request()->getHost(), config('tenancy.central_domains'))) {
                // Centrale app: normale route
                return Route::post('/livewire/update', $handle)->middleware('web');
            } else {
                // Tenant domein: initialiseer tenancy ook!
                return Route::post('/livewire/update', $handle)->middleware([
                    'web',
                    InitializeTenancyByDomain::class,
                ]);
            }
        });
    }
}
