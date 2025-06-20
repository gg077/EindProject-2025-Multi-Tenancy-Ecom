<?php

// declare(strict_types=1);

return [
    /**
     * Naam van de rol die automatisch gegeven wordt aan de gebruiker die de tenant aanmaakt
     * Deze gebruiker krijgt volledige toegang tot de tenant
     */
    'admin_role' => 'Owner',

    /**
     * Standaard rol voor gebruikers die zich registreren bij een tenant
     * Deze gebruikers krijgen beperkte rechten binnen de tenant
     */
    'default_role' => 'User',
];
