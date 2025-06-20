<?php

namespace App\Http\Middleware;

use Closure; // Closure is nodig voor de next() functie in middleware = annonnieme PHP-functie
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TeamsPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Controleer: is er een ingelogde gebruiker en is er een actieve tenant geladen?
        if (auth()->check() && tenant('id')) {
            // Hiermee zorg je ervoor dat bij elke permissie-check (zoals can(), hasRole(), ...) de juiste team_id (hier: tenant_id) wordt gebruikt.
            setPermissionsTeamId(tenant('id'));
        }
        // Laat de request verdergaan naar de volgende middleware of controller
        return $next($request);
    }
}
