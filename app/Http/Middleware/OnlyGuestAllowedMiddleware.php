<?php

namespace App\Http\Middleware;

use Closure; // Closure is nodig voor de next() functie in middleware = annonnieme PHP-functie
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OnlyGuestAllowedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Controleer: is de gebruiker ingelogd?
        if (auth()->check()) {
            // Als de gebruiker ingelogd is, stuur hem dan door naar zijn gepaste route
            return redirect()->intended(auth()->user()->getRedirectRoute());
        }
        // anders volgende middleware of controller
        return $next($request);
    }
}
