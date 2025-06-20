<?php

namespace App\Livewire\Admin\Product;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductDetails extends Component
{
    use WithPagination;

    public Product $product;
    public int $reviewCount = 0; // Totaal aantal reviews (los bijgehouden zodat paginatie niet in de weg zit)

    public function mount(string $slug): void
    {
        // Zoek het product op basis van de slug en laad ook de bijbehorende foto's en categorie
        $product = Product::where('slug', $slug)->with(['photos', 'category'])->firstOrFail();
        $this->authorize('view', $product);
        // Laad het product
        $this->product = $product;

        // Haal het totaal aantal reviews op (los van paginatie)
        $this->reviewCount = $this->product->reviews()->count();
    }

    public function render()
    {
        // Als de gebruiker ingelogd is, gebruik dan het 'app'-layout; anders de 'guest'-layout
        $layout = auth()->check() ? 'components.layouts.app' : 'layouts.guest';

        // Haal de reviews op (met bijbehorende gebruikers)
        return view('livewire.admin.product.product-details', [
            'reviews' => $this->product->reviews()->with('user')->latest()->paginate(15)
        ])->layout($layout);
    }
}
