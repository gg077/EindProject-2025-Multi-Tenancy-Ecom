<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;
class TenantPolicy
{
    /**
     * Bepaalt of de gebruiker een lijst van alle tenants mag bekijken.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view tenant');
    }

    /**
     * Bepaalt of de gebruiker een specifieke tenant mag bekijken.
     */
    public function view(User $user, Tenant $tenant): bool
    {
        return $user->can('view tenant');
    }

    /**
     * Bepaalt of de gebruiker een nieuwe tenant mag aanmaken.
     */
    public function create(User $user): bool
    {
        return $user->can('create tenant');
    }

    /**
     * Bepaalt of de gebruiker een bestaande tenant mag aanpassen.
     */
    public function update(User $user, Tenant $tenant): bool
    {
        // Gebruiker moet de 'update website settings' permissie hebben / En moet bij dezelfde tenant horen / En moet admin zijn binnen die tenant
        return ($user->can('update website settings') && $user->tenant_id == $tenant->id && $user->isAdmin()) || // TenantAdmin check
            // OF gebruiker hoort bij geen enkele tenant EN heeft de 'update tenant' permissie (bv. een superadmin)
            ($user->tenant_id == null && $user->can('update tenant'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tenant $tenant): bool
    {
        return $user->can('delete tenant');
    }
}
