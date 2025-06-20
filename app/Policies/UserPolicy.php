<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Mag gebruiker de lijst van gebruikers zien?
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view user');
    }

    /**
     * Mag gebruiker een specifieke gebruiker zien?
     */
    public function view(User $user, User $model): bool
    {
        return $user->can('view user') && // Moet permissie hebben
            $user->tenant_id == $model->tenant_id; // En moet in dezelfde tenant zitten
    }

    /**
     * Mag gebruiker een nieuwe gebruiker aanmaken?
     */
    public function create(User $user): bool
    {
        return $user->can('create user');
    }

    /**
     * Mag gebruiker een bestaande gebruiker aanpassen?
     */
    public function update(User $user, User $model): bool
    {
        // Zelfde tenant én admin zijn En permissie hebben
        return $this->verifyUser($user, $model) && $user->can('update user');
    }

    /**
     * Mag gebruiker een andere gebruiker verwijderen?
     */
    public function delete(User $user, User $model): bool
    {
        // Mag zichzelf niet verwijderen Zelfde tenant én admin Permissie Mag geen superadmin verwijderen
        return $user->id != $model->id && $this->verifyUser($user, $model) && $user->can('delete user') && !$model->isTenantSuperAdmin();
    }

    /**
     * Mag gebruiker een verwijderde gebruiker herstellen?
     */
    public function restore(User $user, User $model): bool
    {
        return $this->verifyUser($user, $model) && $user->can('restore user');
    }

    /**
     * Mag gebruiker een gebruiker permanent verwijderen?
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $this->verifyUser($user, $model) && $user->can('forceDelete user');
    }

    /**
     * Hulpmethode: controleer of gebruiker admin is én tot dezelfde tenant behoort
     */
    private function verifyUser(User $user, User $model): bool
    {
        return $user->tenant_id == $model->tenant_id && $user->isAdmin();
    }
}
