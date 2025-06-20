<?php

namespace App\Livewire\Buyer\ProductListing;

use App\Models\Category;
use Livewire\Component;

class ProductFilters extends Component
{
    public $selectedCategory = '';
    public $sortBy = 'name_asc';
    public $search = '';

    protected $listeners = ['categorySelected' => 'setCategory'];

    public function setCategory($categoryId)
    {
        // Zend event naar ProductListing component om filter toe te passen
        $this->selectedCategory = $categoryId;
        $this->dispatch('categoryUpdated', categoryId: $categoryId)->to('buyer.product-listing.product-listing');
    }

    // aangeroepen wanneer een van de filteropties gewijzigd wordt
    public function updatedSelectedCategory()
    {
        $this->dispatch('categoryUpdated', categoryId: $this->selectedCategory)->to('buyer.product-listing.product-listing');
    }

    // aangeroepen wanneer een van de sorteeropties gewijzigd wordt
    public function updatedSortBy()
    {
        $this->dispatch('sortUpdated', sortBy: $this->sortBy)->to('buyer.product-listing.product-listing');
    }

    // aangeroepen wanneer de zoeksearcher gewijzigd wordt
    public function updatedSearch()
    {
        $this->dispatch('searchUpdated', search: $this->search)->to('buyer.product-listing.product-listing');
    }

    public function render()
    {
        return view('livewire.buyer.product-listing.product-filters', [
            // Haal alle categorieën op om in een dropdown/selectie weer te geven
            'categories' => Category::all()
        ]);
    }
}
