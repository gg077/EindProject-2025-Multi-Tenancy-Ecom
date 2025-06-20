<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class ShowCategory extends Component
{
    use WithPagination;

    public function render()
    {
        $this->authorize('viewAny', Category::class);

        // Haalt de categorieën op:
        return view('livewire.admin.categories.show-category', [
            // met producten (products_count) gesorteerd op nieuwste eerst
            'categories' => Category::withCount('products')->latest()->paginate(10)
        ]);
    }

    public function delete($categoryId)
    {
        //  Zoek de categorie op met het aantal gekoppelde producten.
        $category = Category::withCount('products')->findOrFail($categoryId);
        // Als er producten gekoppeld zijn aan deze categorie → stop met foutmelding (403 Forbidden)
        abort_if($category->products_count > 0, 403);
        $this->authorize('delete', $category);
        $category->delete();
    }
}
