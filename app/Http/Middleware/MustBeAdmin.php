<?php

namespace App\Http\Middleware;

use Closure; // Closure nodig voor next() functie in middleware = PHP-functie
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response; // Voor de return-type Response

class MustBeAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Controleer is er ingelogde gebruiker en of deze een admin is
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        // Als de gebruiker wél is ingelogd, maar GEEN admin is
        if(auth()->check()) {
            // Stuur gebruiker door naar een alternatieve route (bv. dashboard of home)
            return redirect(auth()->user()->getRedirectRoute())->with('error', 'You do not have super-admin access.');
        }

        // Gebruiker niet ingelogd? Stuur hem naar de loginpagina
        return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
    }
}
