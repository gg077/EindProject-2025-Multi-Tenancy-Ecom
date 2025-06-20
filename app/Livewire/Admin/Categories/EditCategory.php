<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Illuminate\Validation\Rule;
use Livewire\Component;

class EditCategory extends Component
{
    public Category $category;
    public string $name = ''; // wire:model="name" in de blade

    protected array $rules = [ // protected = Deze regels kunnen niet veranderen. Ze zijn vast.
        'name' => 'required|min:2|unique:categories,name',
    ];

    public function mount(Category $category)
    {
        $this->category = $category; // Variabele om de categorie model op te halen
        $this->name = $category->name; // Vul het formulier vooraf in met de huidige naam
    }

    public function save()
    {
        $this->authorize('update', $this->category);

        $this->validate(['name' => ['required', 'string', 'max:255',
            // unieke waarde in categorieën, binnen deze tenant & negeer de huidige categorie (zodat je 'm niet met zichzelf vergelijkt)
            Rule::unique('categories')->where('tenant_id', tenant('id'))->ignore($this->category->id)]]);

        // Werk de categorie bij in de database
        $this->category->update(['name' => $this->name]);

        return $this->redirect(route('admin.categories.index'), true);
    }


    public function render()
    {
        return view('livewire.admin.categories.edit-category');
    }
}
