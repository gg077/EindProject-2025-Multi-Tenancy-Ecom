<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class Module extends Model
{
    protected $fillable = [
        'name', // "User Management" of "Order"
        'user_type' // 1: tenant super-admin, 2: super super-admin
    ];

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}





