<?php

namespace App\Livewire\Admin\Product;

use Livewire\Component;
use App\Models\Product;

class ShowProducts extends Component
{
    public $products;

    public function mount()
    {
        $this->authorize('viewAny', Product::class);
        $this->loadProducts(); // Laad de producten
    }

    public function loadProducts()
    {
        // Laad alle producten, inclusief foto relatie. with('photo') is voor Eager Loading
        $this->products = Product::with('photo')->get();
    }

    public function deleteProduct($productId)
    {
        $product = Product::findOrFail($productId); // Zoek het juiste product
        $this->authorize('delete', $product);
        $product->delete();
        $this->loadProducts(); // Herlaad de lijst na verwijdering
        session()->flash('success', 'Product verwijderd!');
    }

    public function render()
    {
        return view('livewire.admin.product.show-products', [
            'products' => $this->products
        ]);
    }
}
