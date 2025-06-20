<?php

namespace App\Models;

use Spatie\Permission\Models\Role as ModelsRole;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Role extends ModelsRole
{
    use BelongsToTenant;
}
