<div class="p-6 bg-white dark:bg-gray-800 rounded shadow space-y-6">

    <h1 class="text-2xl font-bold text-black dark:text-white mb-6">Product bewerken</h1>

    <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-3 gap-8">

        <!-- LEFT SIDE - TEXT FIELDS -->
        <div class="md:col-span-2 space-y-4">
            <!-- Naam -->
            <div>
                <label class="block mb-1 text-black dark:text-white">Naam</label>
                <input type="text" wire:model="name" class="border p-2 rounded w-full text-black dark:text-white dark:bg-gray-700 dark:border-gray-600">
                @error('name') <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Categorie -->
            <div>
                <label class="block mb-1 text-black dark:text-white">Categorie</label>
                <select wire:model="category_id" class="border p-2 rounded w-full text-black dark:text-white dark:bg-gray-700 dark:border-gray-600">
                    <option value="">Selecteer een categorie</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Staat je categorie er niet tussen? <a href="{{ route('admin.categories.create') }}" class="text-blue-500 dark:text-blue-400 underline">Voeg een nieuwe categorie toe</a>.
                </p>
            </div>

            <!-- Omschrijving -->
            <div>
                <label class="block mb-1 text-black dark:text-white">Omschrijving</label>
                <textarea wire:model="description" class="border p-2 rounded w-full text-black dark:text-white dark:bg-gray-700 dark:border-gray-600"></textarea>
                @error('description') <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Prijs -->
            <div>
                <label class="block mb-1 text-black dark:text-white">Prijs (€)</label>
                <input type="number" step="0.01" wire:model="price" class="border p-2 rounded w-full text-black dark:text-white dark:bg-gray-700 dark:border-gray-600">
                @error('price') <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Download Link -->
            <div>
                <label class="block mb-1 text-black dark:text-white">Download Link</label>
                <input type="url" wire:model="download_link" class="border p-2 rounded w-full text-black dark:text-white dark:bg-gray-700 dark:border-gray-600" placeholder="https://example.com/download/file.zip">
                @error('download_link') <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Status -->
            <div>
                <label class="block mb-1 text-black dark:text-white">Status</label>
                <select wire:model="status" class="border p-2 rounded w-full text-black dark:text-white dark:bg-gray-700 dark:border-gray-600">
                    <option value="active">Active</option>
                    <option value="delisted">Delisted</option>
                </select>
                @error('status') <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- RIGHT SIDE - IMAGES -->
        <div class="space-y-4">
            <h2 class="text-lg font-bold text-black dark:text-white">Afbeeldingen</h2>

            <!-- Bestaande afbeeldingen in een grid -->
            <div class="grid grid-cols-3 gap-4">
                @foreach ($images as $photo)
                    <div class="relative group border rounded overflow-hidden dark:border-gray-600">
                        <img src="{{ Storage::disk('public')->url($photo->path) }}" class="w-full h-32 object-cover">

                        <!-- Verwijder knop -->
                        <button
                            type="button"
                            wire:click="removeImage({{ $photo->id }})"
                            class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition"
                            title="Verwijderen"
                        >
                            &times;
                        </button>
                    </div>
                @endforeach
            </div>

            <!-- Nieuwe afbeelding uploaden -->
            <div>
                <label class="block mb-1 text-black dark:text-white">Nieuwe afbeelding toevoegen
                    <span class="text-sm text-gray-500 dark:text-gray-400">(Max 2MB per afbeelding)</span>
                </label>
                <label
                    class="cursor-pointer border border-dashed border-gray-400 dark:border-gray-600 p-4 rounded flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white"
                >
                    Klik hier om een afbeelding te kiezen
                    <input type="file" wire:model="newImage" class="hidden" accept="image/jpeg,image/png,image/jpg">
                </label>

                @error('newImage')
                <p class="text-red-500 dark:text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>
        </div>


        <!-- Submit Button -->
        <div class="md:col-span-3">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded font-semibold hover:bg-gray-800 transition dark:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">Opslaan</button>
        </div>

    </form>
</div>
