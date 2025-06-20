<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase; // database-functionaliteit per tenant
use Stancl\Tenancy\Database\Concerns\HasDomains;  // domein-functionaliteit per tenant

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    // Velden die massaal toewijsbaar zijn
    protected $fillable = [
        'id',
        'website_name',
        'website_logo',
        'website_description',
        'stripe_publishable_key',
        'stripe_secret_key',
        'is_setup_completed',
        'vat_number',
        'vat_percentage',
        'data',
        'status',
    ];

    /**
     * Casts zorgen ervoor dat bepaalde velden op de juiste manier worden gelezen/geschreven.
     * Bijvoorbeeld: encryptie, JSON, boolean, datetime, enz.
     */
    protected $casts = [
        'stripe_secret_key' => 'encrypted',
    ];

    /**
     * Velden die verborgen worden bij serialisatie (bijv. JSON output naar API)
     */
    protected $hidden = [
        'stripe_secret_key'
    ];

    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function tenantOwner()
    {
        return $this->hasOne(User::class)
            ->where('is_admin', true)
            ->whereDoesntHave('roles');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'website_name',
            'website_logo',
            'website_description',
            'stripe_publishable_key',
            'stripe_secret_key',
            'is_setup_completed',
            'created_at',
            'updated_at',
            'status'
        ];
    }
}
