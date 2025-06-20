<?php

namespace App\Http\Middleware;

use Closure; // Closure nodig voor next() functie in middleware = PHP-functie
use Illuminate\Http\Request;

class EnsureTenantSetupCompleted
{
    public function handle(Request $request, Closure $next)
    {
        if (
            // 1. Is de gebruiker ingelogd?
            auth()->check() &&
            // 2. Is de gebruiker een Tenant Super Admin?
            auth()->user()->isTenantSuperAdmin() &&
            // 3. maar heeft de setup nog niet afgerond
            !tenant()->is_setup_completed &&
            // 4. en zit niet al op de onboardingpagina
            $request->route()->getName() !== 'admin.onboarding' &&
            // 5. en is niet bezig in admin/settings (om bv. z’n setup te voltooien)
            !$request->is('admin/settings/*')
        ) {
            // Als alle bovenstaande waar zijn, redirect dan naar de onboarding pagina
            return redirect()->route('admin.onboarding');
        }
        // Als alle checks ok zijn, laat de request doorgaan naar de volgende middleware of controller
        return $next($request);
    }
}
