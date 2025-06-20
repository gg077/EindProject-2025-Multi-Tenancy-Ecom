<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Haal de huidige tenant op / stancl
        $tenant = tenant();

        // Controleer of er GEEN tenant is of dat de status niet 'active' is
        if (!$tenant || $tenant->status !== 'active') {
            // Voor gewone (web) requests: toon een aangepaste foutpagina met tenant-info
            return response()->view('errors.tenant-suspended', [
                'tenant' => $tenant
            ], 403);
        }
        // Als de tenant actief is, laat de request gewoon doorgaan naar de volgende middleware of controller
        return $next($request);
    }
}
