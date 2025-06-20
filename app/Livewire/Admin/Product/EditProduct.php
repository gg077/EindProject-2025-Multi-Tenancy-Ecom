<?php

namespace App\Livewire\Admin\Product;

use App\Models\Photo;
use Livewire\Component;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class EditProduct extends Component
{
    use WithFileUploads;

    public $product;

    public $name;
    public $slug;
    public $description;
    public $price;
    public $category_id;
    public $images = [];
    public $download_link;

    public $categories;
    public $newImage;
    public $status;

    public function mount(Product $product)
    {
        $this->authorize('update', $product);
        // Initieer de eigenschappen op basis van het bestaande product
        $this->name = $product->name;
        $this->slug = $product->slug;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->category_id = $product->category_id;
        $this->images = $product->photos;
        $this->status = $product->status;
        $this->download_link = $product->download_link;
        // Laad alle beschikbare categorieën (voor selectie in de form)
        $this->categories = \App\Models\Category::all();
    }

    // Wordt automatisch uitgevoerd wanneer een gebruiker een nieuwe afbeelding uploadt
    public function updatedNewImage()
    {
        if ($this->newImage) {
            // Validatie
            $this->validate([
                'newImage' => 'image|max:2048|mimes:jpeg,png,jpg', // Max 2MB, JPEG/PNG/JPG
            ]);
            
            // Opslaan onder juiste tenant map
            $path = Storage::disk('public')->putFile(tenant()->id . '/products/' . $this->product->id, $this->newImage);

            // Koppel de nieuwe afbeelding aan het product via de 'photos'-relatie
            $this->product->photos()->create(['path' => $path]);

            // Ververs het model zodat de nieuwe foto zichtbaar is in het overzicht
            $this->product->refresh();
            $this->images = $this->product->photos;

            // Reset de tijdelijke upload
            $this->newImage = null;
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => ['required', 'integer',
                Rule::exists('categories', 'id')->where('tenant_id', tenant('id'))], // Zorg dat de categorie hoort bij de juiste tenant
            'status' => 'required|in:active,delisted',
            'download_link' => 'required|url|max:255'
        ];
    }

    public function save()
    {
        $this->authorize('update', $this->product);
        $validated = $this->validate();

        // Genereer een unieke slug op basis van naam
        $validated['slug'] = Str::slug($this->name) . '-' . uniqid();

        // Opslaan van afbeeldingen in product table as images, handig om later het op te halen met $product->images
        $this->product->update($validated + ['images' => json_encode($this->product->photos->pluck('path')->toArray())]);

        session()->flash('success', 'Product bijgewerkt!');

        return $this->redirect(route('admin.products.index'), true);
    }

    // Verwijder een afbeelding (per ID) en ververs de lijst
    public function removeImage($photoId)
    {
        Photo::find($photoId)?->delete(); // Verwijder uit DB

        // Ververs zodat het meteen zichtbaar is
        $this->product->refresh();
        $this->images = $this->product->photos;
    }

    public function render()
    {
        return view('livewire.admin.product.edit-product');
    }
}
