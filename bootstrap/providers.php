<?php
// Deze bestanden worden als eerste geladen als de app start.
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\TenancyServiceProvider::class,
    // Bijvoorbeeld: TenancyServiceProvider zorgt ervoor dat de juiste tenant wordt ingeladen op basis van het domein.

];
