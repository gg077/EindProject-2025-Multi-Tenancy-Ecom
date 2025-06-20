<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Product extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'status',
        'images',
        'average_rating',
        'completed_orders_count',
        'total_revenue',
        'download_link',
    ];

    protected $casts = [
        'average_rating' => 'decimal:1',
        'total_revenue' => 'decimal:2',
    ];

    /**
     * Verbergt gevoelige of interne velden tijdens serialisatie naar JSON of API-output.
     *
     * @var list<string>
     */
    protected $hidden = [
        'download_link' // Downloadlink wordt niet standaard meegestuurd in API-responses
    ];

    /**
     * Relatie: Haalt de oudste foto op (bijv. als hoofdfoto van het product).
     * * Gebruik in frontend om een enkele afbeelding te tonen.
     */
    public function photo()
    {
        return $this->hasOne(Photo::class)->oldestOfMany();
    }

    /**
     * Relatie: een product kan meerdere foto's hebben.
     * Handig voor een galerijweergave.
     */
    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    /**
     * Relatie: een product kan meerdere reviews hebben.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    //  Relatie: elk product behoort tot één categorie.
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Query scope: filtert alleen actieve producten = Product::active()->get();
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
