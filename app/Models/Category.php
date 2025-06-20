<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Import van de Laravel trait voor model factories (voor het automatisch genereren van testdata)
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Category extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = ['name', 'slug'];

    /**
     * Products in this category
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
