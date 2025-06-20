<?php

namespace App\Http\Middleware;

use Closure; // Closure nodig voor next() functie in middleware = PHP-functie
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MustBeBuyer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check: is de gebruiker ingelogd én GEEN admin?
        if (auth()->check() && !auth()->user()->isAdmin()) {
            return $next($request);
        }

        // Gebruiker ingelogd, maar GEEN admin
        if(auth()->check()) {
            // Redirect naar zijn gepaste startpagina
            return redirect(auth()->user()->getRedirectRoute())->with('error', 'You do not have super-admin access.');
        }

        // Gebruiker niet ingelogd? Stuur naar de loginpagina
        return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
    }
}
