<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Livewire\Component;
use Illuminate\Support\Str; // Str::slug
use Illuminate\Validation\Rule;

class CreateCategory extends Component
{
    public string $name = ''; // wire:model="name" in de blade

    public function save()
    {
        // Toegangscontrole: checkt of de gebruiker een categorie mag aanmaken
        $this->authorize('create', Category::class);
        // Validatie van het formulierveld 'name'
        $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->where('tenant_id', tenant('id'))], // unieke naam per tenant
        ]);

        // Aanmaken van een nieuwe categorie in de database
        Category::create([
            'name' => $this->name,
            'slug' => Str::slug($this->name), // lowercase & geen spaties = goed voor url's
        ]);

        return $this->redirect(route('admin.categories.index'), true);
    }


    public function render()
    {
        // Opnieuw toegangscontrole (voor extra veiligheid bij het tonen van het component)
        $this->authorize('create', Category::class);
        return view('livewire.admin.categories.create-category');
    }
}
