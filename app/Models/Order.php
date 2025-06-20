<?php

namespace App\Models;

use App\Mail\DownloadLinkMail;

// Laravel componenten en traits
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Helpers voor mail en logging
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

// Trait van Stancl Tenancy om het model automatisch te koppelen aan een tenant_id
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Order extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'user_id',
        'tenant_id',
        'total_amount',
        'order_taxes',
        'vat_percentage',
        'status',
        'payment_provider',
        'payment_reference',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'order_taxes' => 'decimal:2',
        'vat_percentage' => 'decimal:2',
    ];

    public const STATUS_PENDING = 'Pending';
    public const STATUS_PAID = 'Paid';
    public const STATUS_FAILED = 'Failed';


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_items', 'order_id', 'product_id')
            ->using(OrderItem::class)
            ->withTimestamps();
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the reviews for the order.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // controle of de order betaald is
    public function isPaid()
    {
        return $this->status === self::STATUS_PAID;
    }

    // Verstuurt een e-mail met downloadlinks als de order downloadbare producten bevat
    public function sendDownloadLinksEmail() // here
    {
        // Controleer of er items zijn met producten die een 'download_link' hebben
        $hasDownloadableItems = $this->items()->whereHas('product', function($query) {
            $query->whereNotNull('download_link');
        })->exists();

        if ($hasDownloadableItems) {
            try {
                // Verstuur de downloadlink-mail naar het e-mailadres van de gebruiker
                Mail::to($this->user->email)->send(new DownloadLinkMail($this));
            } catch (\Exception $e) {
                // Foutafhandeling: log de fout, maar laat de app niet crashen
                Log::error('Failed to send download links email for order ' . $this->id . ': ' . $e->getMessage());
            }
        }
    }
}
