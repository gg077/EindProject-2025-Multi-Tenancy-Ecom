<?php

namespace App\Livewire;

use Livewire\Component;

class TenantSwitcher extends Component
{
    public function getTenantsProperty()
    {
        return auth()->user()
            ->tenants() // Haal alle tenants op waar deze gebruiker bestaat
            ->where('id', '!=', auth()->user()->tenant_id) // Sluit de huidige actieve tenant uit
            ->get(['id', 'website_name']); // Beperk de velden naar id en website_name
    }

    public function switchTenant($tenantId)
    {
        $user = auth()->user(); //Stap 1: haalt de inlogde gebruiker op
        $tenant = \App\Models\Tenant::find($tenantId); // Stap 2:  zoek de tenant met de gegeven ID in de database

        if (!$tenant) {
            $this->dispatch('error', 'Tenant not found.');
            return;
        }

        // Stap 3: Controleren of gebruiker op die tenant bestaat en schakel naar die tenant's database
        $userOnTenant = $tenant->run(function () use ($user) {
            return \App\Models\User::where('email', $user->email)->first(); // en controleert of deze gebruiker zelfde email heeft
        });

        // Als er geen matchende gebruiker is op de andere tenant
        if (!$userOnTenant) {
            $this->dispatch('error', 'You do not have access to this tenant.');
            return;
        }

        // Stap 4: Impersonatie-token genereren
        $token = tenancy()->impersonate(
            $tenant, // naar welk tenant moet je gaan
            $userOnTenant->id, // wie moet je zijn op die tenant
            '/admin/dashboard', // waar je terechtkomt ná het switchen
            'web' // welke auth guard je gebruikt
        );

        // Stap 5: Redirect naar dat tenant-domein
        $domain = $tenant->domains->first()->domain; // haalt het eerste gekoppelde domein van die tenant
        $currentScheme = request()->getScheme(); // Haal het protocol op (http of https), vermijd verkeerde redirects
        return redirect()->away("{$currentScheme}://{$domain}/impersonate/{$token->token}");
    }

    public function render()
    {
        return view('livewire.tenant-switcher');
    }
}
