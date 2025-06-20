<?php

namespace App\Livewire\Admin\Product;

use Livewire\Component;
use App\Models\Category;
use App\Models\Product;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;


class CreateProduct extends Component
{
    use WithFileUploads;

    public $name;
    public $slug;
    public $description;
    public $price;
    public $category_id;
    public $categories;
    public $status = 'active';
    public $download_link;

    public $newImages = []; // Tijdelijke lijst met geselecteerde bestanden
    public $images = []; // Definitieve lijst van te uploaden afbeeldingen

    public function mount()
    {
        $this->authorize('create', Product::class);
        // Haal alle categorieën op zodat ze in een dropdown kunnen getoond worden
        $this->categories = Category::all();
    }

    public function updatedNewImages()
    {
        // Valideer elk nieuw geselecteerd beeld
        $this->validateOnly('newImages.*', [
            'newImages.*' => 'image|max:2048|mimes:jpeg,png,jpg',  // Max 2MB per bestand
        ]);

        // Voeg ze één voor één toe aan de definitieve lijst van afbeeldingen
        foreach ($this->newImages as $image) {
            $this->images[] = $image; // <-- gewoon file opslaan, geen array maken
        }
        // Maak de tijdelijke lijst leeg
        $this->newImages = [];
    }

    public function removeImage($index)
    {
        unset($this->images[$index]); // verwijder op index
        $this->images = array_values($this->images); // hernummer array
    }

    // validaties regels
    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => ['required', 'integer',
                // Check of categorie bestaat én bij de juiste tenant hoort
                Rule::exists('categories', 'id')->where('tenant_id', tenant('id')),],
            'status' => 'required|in:active,delisted',
            'download_link' => 'required|url|max:255',
            'images.*' => 'image|max:2048',
        ];
    }

    public function save()
    {
        $this->authorize('create', Product::class);
        $validated = $this->validate();

        $validated['slug'] = Str::slug($this->name) . '-' . uniqid(); // voegt unieke id toe, gebruik voor url's

        // Verwijder afbeeldingen uit de validatie (want dat behandelen we apart)
        unset($validated['images']);

        // Product aanmaken als nog niet bestaat
        $product = Product::create($validated);

        // Sla de afbeeldingen pas nu op (na het aanmaken van het product)
        foreach ($this->images as $file) {
            // Upload naar storage (public disk)
            $filePath = Storage::disk('public')->putFile(tenant()->id . '/products/' . $product->id, $file);
            // Koppel de foto aan het product in de database
            $product->photos()->create(['path' => $filePath]);
        }

        // Opslaan van afbeeldingen in product table as images, (handig optehalen $product->images)
        $product->update([
            'images' => json_encode($product->photos->pluck('path')->toArray())
        ]);

        session()->flash('success', 'Product toegevoegd!');
        return $this->redirect(route('admin.products.index'), true);
    }

    public function render()
    {
        return view('livewire.admin.product.create-product');
    }
}
