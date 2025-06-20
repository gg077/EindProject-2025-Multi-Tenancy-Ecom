<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as ModelsPermission;

class Permission extends ModelsPermission
{
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
