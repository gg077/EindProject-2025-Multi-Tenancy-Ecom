<?php

namespace App\Livewire\Buyer\ProductListing;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductListing extends Component
{
    use WithPagination;

    public $selectedCategory = '';
    public $sortBy = 'name_asc';
    public $search = '';
    public $placeholderImage = 'https://placehold.co/400x400?text=No+Image';

    protected $queryString = [
        'selectedCategory' => ['except' => ''],
        'sortBy' => ['except' => 'name_asc'],
        'search' => ['except' => ''],
    ];

    protected $listeners = [
        'productAdded' => '$refresh',
        'productRemoved' => '$refresh',
        'categoryUpdated' => 'updateCategory',
        'sortUpdated' => 'updateSort',
        'searchUpdated' => 'updateSearch'
    ];

    public function mount()
    {

    }

    // Category Filter
    public function updateCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->resetPage();
    }

    // Sorteerkeuze bijwerken
    public function updateSort($sortBy)
    {
        $this->sortBy = $sortBy;
        $this->resetPage();
    }

    // Zoeken bijwerken
    public function updateSearch($search)
    {
        $this->search = $search;
        $this->resetPage();
    }

    // Reset pagina bij wijziging
    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    // Reset pagina bij wijziging
    public function updatedSearch()
    {
        $this->resetPage();
    }

    // Als gebruiker een product toevoegt aan de winkelmand
    public function addToCart($productId)
    {
        // Stuurt een event naar een ander Livewire-component (mini-cart)
        $this->dispatch('productAdded', productId: $productId)->to('buyer.product-listing.mini-cart');
    }

    public function render()
    {
        $query = Product::active() // Alleen actieve producten
            ->with(['category', 'photo']) // Laad categorie en foto-relaties mee
            ->when($this->selectedCategory, function ($query) {
            // Filter op gekozen categorie
            return $query->where('category_id', $this->selectedCategory);
            })
            ->when($this->search, function ($query) {
                // Zoek op naam of beschrijving
                return $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            });

        // Sorteer: bv. 'name_asc' wordt ['name', 'asc']
        [$field, $direction] = explode('_', $this->sortBy);
        $query->orderBy($field, $direction);

        // Haal gepagineerde resultaten op (12 per pagina)
        $products = $query->paginate(12);
        // Haal alle categorieën op voor filters
        $categories = Category::all();

        // Kies het juiste layout afhankelijk van login-status
        $layout = auth()->check() && !auth()->user()->isAdmin()
            ? 'components.layouts.app' // Normale gebruiker layout
            : 'layouts.guest'; // Gast layout

        return view('livewire.buyer.product-listing.product-listing', [
            'products' => $products,
            'categories' => $categories,
            'placeholderImage' => $this->placeholderImage
        ])->layout($layout);
    }
}
