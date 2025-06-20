<?php

namespace App\Livewire\Buyer\ProductListing;

use App\Models\Product;
use Livewire\Component;

class MiniCart extends Component
{
    public $cart = [];
    public $total = 0;
    public $placeholderImage = 'https://placehold.co/400x400?text=No+Image';

    protected $listeners = ['productAdded' => 'addToCart', 'productRemoved' => 'removeFromCart'];

    public function mount()
    {
        $this->loadCart(); // Winkelwagen ophalen uit sessie
    }

    public function loadCart()
    {
        $this->cart = session()->get('cart', []); // Haalt cart-array op uit sessie
        $this->calculateTotal(); // Totaalbedrag opnieuw berekenen
    }

    // Voeg een product toe aan de winkelwagen
    public function addToCart($productId)
    {
        // Zoek het product op uit de database, met foto
        $product = Product::with('photo')->find($productId);

        if (!$product) {
            return; // Als het product niet bestaat, stoppen we
        }
        // Winkelwagen ophalen uit sessie
        $cart = session()->get('cart', []);
        // Toevoegen of overschrijven van product in winkelwagen
        $cart[$productId] = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => 1, // Altijd 1 bij toevoegen (geen update-logica hier)
            'image' => $product->photo ? $product->photo->getUrl() : $this->placeholderImage
        ];
        // Opslaan van bijgewerkte winkelwagen in sessie
        session()->put('cart', $cart);
        $this->loadCart();
        $this->dispatch('cartUpdated');
    }

    public function removeFromCart($productId)
    {
        $cart = session()->get('cart', []);

        // Als product in winkelwagen zit → verwijderen
        if (isset($cart[$productId])) {
            unset($cart[$productId]); // Verwijder het item
            session()->put('cart', $cart); // Update sessie
            $this->loadCart(); // Laad cart opnieuw in
            $this->dispatch('cartUpdated');
        }
    }

    // Bereken totaalbedrag van winkelwagen (prijs × hoeveelheid)
    public function calculateTotal()
    {
        $this->total = collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    public function render()
    {
        return view('livewire.buyer.product-listing.mini-cart');
    }
}
