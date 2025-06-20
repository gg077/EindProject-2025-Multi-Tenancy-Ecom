<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Address extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country'
    ];

    // volledige adres als één string op te halen
    public function getFullAddressAttribute()
    {
        // Bouwt een string van alle adresonderdelen en verwijdert overbodige spaties aan het begin/einde
        return trim("{$this->address_line_1} {$this->address_line_2}, {$this->city}, {$this->state} {$this->postal_code}, {$this->country}");
    }
}
