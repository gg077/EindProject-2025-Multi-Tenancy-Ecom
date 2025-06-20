<div class="p-6 bg-white dark:bg-gray-800 rounded shadow space-y-4" x-data="{ confirmDeleteId: null }">
    {{-- Alpine.js state: houdt bij welk product ID geselecteerd is om te verwijderen --}}
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-black dark:text-white">Producten</h1>
        {{-- Alleen tonen als de gebruiker 'create' mag doen op Product --}}
    @can('create', App\Models\Product::class)
            <a wire:navigate href="{{ route('admin.products.create') }}" class="bg-gray-900 text-white rounded font-semibold hover:bg-gray-800 transition dark:bg-blue-600 dark:hover:bg-blue-700 px-4 py-2">
                + Nieuw product
            </a>
        @endcan
    </div>
    {{-- Loop over alle producten --}}
    @forelse ($products as $product)
        <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 border dark:border-gray-600 rounded p-4 hover:bg-gray-100 dark:hover:bg-gray-600">
            <div class="flex items-center space-x-4">
                {{-- Afbeelding of placeholder --}}
                <div class="w-16 h-16 bg-gray-300 dark:bg-gray-600 flex items-center justify-center rounded overflow-hidden">
                    @if ($product->photo?->path)
                        <img src="{{ Storage::disk('public')->url($product->photo->path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        📦 {{-- Emoji als fallback --}}

                    @endif
                </div>
                {{-- Product details --}}
                <div>
                    <h2 class="font-semibold text-lg text-black dark:text-white">{{ $product->name }}</h2>
                    <p class="text-gray-600 dark:text-gray-300 font-bold">€{{ number_format($product->price, 2, ',', '.') }}</p>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">{{ $product->description }}</p>
                    {{-- Status badge --}}
                    <div class="mt-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                            {{ ucfirst($product->status) }}
                        </span>
                    </div>
                </div>
            </div>
            {{-- Actieknoppen --}}
            <div class="flex items-center space-x-2">
                <a wire:navigate href="{{ route('admin.products.show', $product->slug)}}" class="px-3 py-1 bg-gray-200 dark:bg-gray-600 dark:text-white rounded hover:bg-gray-300 dark:hover:bg-gray-500">View</a>
                <a href="{{ route('admin.products.edit', $product->id) }}" class="bg-gray-900 text-white rounded font-semibold hover:bg-gray-800 transition px-3 py-1 dark:bg-blue-600 dark:hover:bg-blue-700">Edit</a>
                {{-- Verwijder-knop activeert bevestigingsmodal --}}
                <button @click="confirmDeleteId = {{ $product->id }}" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700">
                    Verwijderen
                </button>
            </div>
        </div>
    @empty
        {{-- Geen producten gevonden --}}
        <p class="text-gray-600 dark:text-gray-400">Geen producten gevonden.</p>
    @endforelse

    {{-- Bevestigingsmodal voor verwijderen --}}
    <div
        x-show="confirmDeleteId"
        class="fixed inset-0 flex items-center justify-center bg-white/60 dark:bg-gray-900/60 backdrop-blur-md z-50"
        style="display: none;"
        x-transition
    >
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-md space-y-4">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Bevestig verwijdering</h2>
            <p class="text-gray-600 dark:text-gray-400">Weet je zeker dat je dit product wilt verwijderen?</p>
            <div class="flex justify-end space-x-3">
                {{-- Verwijderen bevestigen, roep Livewire functie aan --}}
                <button
                    @click="$wire.call('deleteProduct', confirmDeleteId); confirmDeleteId = null;"
                    class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700">
                    Verwijderen
                </button>
                {{-- Annuleer modal --}}
                <button
                    @click="confirmDeleteId = null"
                    class="bg-gray-200 dark:bg-gray-700 dark:text-white px-4 py-2 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                    Annuleren
                </button>
            </div>
        </div>
    </div>
</div>
