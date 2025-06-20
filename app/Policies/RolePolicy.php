<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    /**
     * Bepaalt of de gebruiker de lijst van alle rollen mag bekijken.
     */
    public function viewAny(User $user): bool
    {
        // Gebruiker moet de 'view role' permission hebben
        return $user->can('view role');
    }

    /**
     * Bepaalt of de gebruiker een specifieke rol mag bekijken.
     */
    public function view(User $user, Role $role): bool
    {
        // Alleen binnen eigen tenant én met de juiste permission
        return $user->tenant_id == $role->tenant_id && $user->can('view role');
    }

    /**
     * Bepaalt of de gebruiker een nieuwe rol mag aanmaken.
     */
    public function create(User $user): bool
    {
        // Alleen admins of superadmins mogen rollen aanmaken én ze moeten de juiste permissie hebben
        return $user->can('create role') && ($user->isSuperAdmin() || $user->isAdmin());
    }

    /**
     * Bepaalt of de gebruiker een bestaande rol mag aanpassen.
     */
    public function update(User $user, Role $role): bool
    {
        // Vereisten: Dezelfde tenant / De juiste permission & IsAdmin
        return $user->tenant_id == $role->tenant_id && $user->can('update role') && ($user->isSuperAdmin() || $user->isAdmin());
    }

    /**
     * Bepaalt of de gebruiker een rol mag verwijderen.
     */
    public function delete(User $user, Role $role): bool
    {
        // Vereisten: Dezelfde tenant / De juiste permission & IsAdmin
        // - De rol mag geen gebruikers meer gekoppeld hebben
        return $user->tenant_id == $role->tenant_id && $user->can('delete role') && ($user->isSuperAdmin() || $user->isAdmin()) && $role->users()->count() == 0;
    }
}
