<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Bepaalt of de gebruiker bestellingen in het algemeen mag bekijken (bijv. lijstweergave).
     */
    public function viewAny(User $user): bool
    {
        // Sta toe als de gebruiker geen admin is, of als hij de permissie view order heeft.
        return !$user->isAdmin() || $user->can('view order');
    }

    /**
     * Bepaalt of de gebruiker een specifieke bestelling mag bekijken.
     */
    public function view(User $user, Order $order): bool
    {
        // Een gebruiker mag een order zien als hij óf de eigenaar is, óf als hij daarvoor de juiste permissie heeft.
        return $user->id == $order->user_id || $user->can('view order');
    }
}
