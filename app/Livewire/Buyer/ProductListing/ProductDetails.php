<?php

namespace App\Livewire\Buyer\ProductListing;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class ProductDetails extends Component
{
    use WithPagination;

    public Product $product;
    public int $reviewCount = 0;
    public Collection $randomProducts;
    // Willekeurige andere producten voor aanbevelingen

    public function mount(string $slug): void
    {
        // Zoek het product op via de slug en laad direct alle gerelateerde data (foto's, categorie)
        $this->product = Product::where('slug', $slug)
            ->with(['photo', 'photos', 'category'])
            ->firstOrFail();

        // Tel het aantal reviews afzonderlijk (zodat pagination op reviews correct werkt)
        $this->reviewCount = $this->product->reviews()->count();

        // Haal 4 willekeurige producten op voor "andere klanten bekeken ook" – behalve het huidige product
        $this->randomProducts = Product::where('id', '!=', $this->product->id)
            ->with('photo') // Eager load photo for random products
            ->inRandomOrder()
            ->take(4)
            ->get();
    }

    public function addToCart(): void
    {
        // Zend event naar MiniCart component (en andere) om product toe te voegen
        $this->dispatch('productAdded', productId: $this->product->id)->to('buyer.product-listing.mini-cart');
        session()->flash('message', 'Product '.$this->product->name.' added to cart!');
        $this->dispatch('cartUpdated'); // Laat andere componenten weten dat de cart is bijgewerkt
    }

    public function buyNow(): void
    {
        // Laad opnieuw het product met foto (voor zekerheid)
        $productModel = Product::with('photo')->find($this->product->id);

        // Als het product niet meer bestaat, geef fout
        if (!$productModel) {
            session()->flash('error', 'Product not found.');
            return;
        }

        // Haal bestaande cart uit sessie
        $cart = session()->get('cart', []);
        $productId = $productModel->id;

        $placeholderImage = 'https://placehold.co/400x400?text=No+Image';

        // Voeg product toe aan de cart
        $cart[$productId] = [
            'id' => $productModel->id,
            'name' => $productModel->name,
            'price' => $productModel->price,
            'quantity' => 1,
            'image' => $productModel->photo ? $productModel->photo->getUrl() : $placeholderImage
        ];

        session()->put('cart', $cart);

        // Laat andere componenten weten dat de cart is gewijzigd
        $this->dispatch('cartUpdated');

        $this->redirect(route('checkout'), true);
    }

    public function addRandomProductToCart(int $productId): void
    {
        $product = Product::find($productId);
        if ($product) {
            $this->dispatch('productAdded', productId: $product->id)->to('buyer.product-listing.mini-cart');
            session()->flash('message', 'Product '. $product->name .' added to cart!');
            $this->dispatch('cartUpdated');
        }
    }

    public function render()
    {
        // Gebruik aangepast layout afhankelijk van of de gebruiker is ingelogd
        $layout = auth()->check()
            ? 'components.layouts.app' // Layout voor ingelogde gebruikers
            : 'layouts.guest'; // Layout voor niet-ingelogde gebruikers
        return view('livewire.buyer.product-listing.product-details', [
            'reviews' => $this->product->reviews()->with('user')->latest()->paginate(15),
            'randomProducts' => $this->randomProducts
        ])->layout($layout);
    }
}
