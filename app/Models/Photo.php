<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    /**
     * Laravel boot method wordt automatisch aangeroepen bij het laden van het model.
     * Hier luisteren we naar het 'deleted' event om automatisch de foto te verwijderen van opslag.
     */
    protected static function boot()
    {
        parent::boot();
        // Voegt een event listener toe: wanneer een Photo record verwijderd wordt...
        static::deleted(function ($photo) {
            // ...controleer of er een pad bestaat én het bestand daadwerkelijk aanwezig is
            if ($photo->path && Storage::disk('public')->exists($photo->path)) {
                // ...dan verwijder je het fysieke bestand uit de 'public' opslagdisk
                Storage::disk('public')->delete($photo->path);
            }
        });
    }

    protected $fillable = [
        'product_id',
        'path',
        'alternate_text',
    ];

    /**
     * Hulpfunctie om de URL op te halen van de foto
     *
     * @return string|null
     */
    public function getUrl()
    {
        // Als er een path bestaat, geef dan de volledige publieke URL terug
        return $this->path ? Storage::disk('public')->url($this->path) : null;
    }
}
