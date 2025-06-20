<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Bepaalt of een gebruiker *alle* categorieën mag bekijken (bijv. in een lijst).
     */
    public function viewAny(User $user): bool
    {
        // Controleer of de gebruiker de 'view category' permission heeft
        return $user->can('view category');
    }

    /**
     * Bepaalt of een gebruiker een specifieke categorie mag bekijken.
     */
    public function view(User $user, Category $category): bool
    {
        // Gebruiker moet binnen dezelfde tenant zitten én de juiste permission hebben
        return $user->tenant_id === $category->tenant_id && $user->can('view category');
    }

    /**
     * Bepaalt of een gebruiker een nieuwe categorie mag aanmaken.
     */
    public function create(User $user): bool
    {
        // Enkel controle op permissie, want nieuwe categorie is nog niet gekoppeld aan een tenant
        return $user->can('create category');
    }

    /**
     * Bepaalt of een gebruiker een categorie mag bijwerken.
     */
    public function update(User $user, Category $category): bool
    {
        // Moet dezelfde tenant zijn én de juiste update-permissie hebben
        return $user->tenant_id === $category->tenant_id && $user->can('update category');
    }

    /**
     * Bepaalt of een gebruiker een categorie mag verwijderen.
     */
    public function delete(User $user, Category $category): bool
    {
        // Gebruiker moet:
        // - tot dezelfde tenant behoren, delete-permissie hebben en de categorie mag géén producten bevatten (products_count === 0)
        return $user->tenant_id === $category->tenant_id && $user->can('delete category') && $category->products_count === 0;
    }
}
