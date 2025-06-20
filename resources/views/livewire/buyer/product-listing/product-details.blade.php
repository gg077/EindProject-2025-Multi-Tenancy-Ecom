<div>
    {{-- Alleen tonen als gebruiker NIET is ingelogd of GEEN admin is --}}
@if(!auth()->check() || (auth()->check() && !auth()->user()->isAdmin()))
        <div class="bg-white dark:bg-gray-800 shadow-sm">
            <div class="container mx-auto px-4 py-4">
                <div class="flex justify-between items-center">
                    {{-- Logo links --}}
                    <div class="flex-shrink-0">
                        {{-- <img class="h-12 w-auto" src="{{ asset('images/logo.png') }}" alt="Store Logo"> --}}
                        @guest
                            <a href="{{ route('home') }}" class="mr-5 flex items-center space-x-2" wire:navigate>
                                <x-app-logo />
                            </a>
                        @endguest
                    </div>
                    {{-- Mini winkelwagen component rechts --}}
                    <livewire:buyer.product-listing.mini-cart />
                </div>
            </div>
        </div>
    @endif

    <div class="container mx-auto px-4 py-8 relative">
        {{-- Flash message bij bv. toevoegen aan winkelwagen --}}
    @if (session()->has('message'))
            <div
                    x-data="{ show: true }"
                    x-init="setTimeout(() => show = false, 5000)"
                    x-show="show"
                    x-transition
                    class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800"
                    role="alert"
            >
                {{ session('message') }}
            </div>
        @endif
        {{-- Hoofdkaart met productdetails --}}
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden">
            <div class="md:flex">
                {{-- Linkerzijde: afbeelding --}}
                <div class="md:w-1/2">
                    @php
                        $allPhotos = collect();
                        if ($product->photos) {
                            $product->photos->each(function ($photo) use ($allPhotos) {
                                if ($allPhotos->doesntContain('id', $photo->id)) {
                                    $allPhotos->push($photo);
                                }
                            });
                        }
                        $photoUrls = $allPhotos->map(fn($p) => $p->getUrl())->filter()->values()->toArray();
                    @endphp
                    {{-- Fotogalerij met Alpine.js --}}
                    @if (!empty($photoUrls))
                        <div x-data="{ currentSlide: 0, slides: {{ json_encode($photoUrls) }} }" class="relative">
                            <div class="aspect-[4/3] w-full overflow-hidden">
                                <template x-for="(slide, index) in slides" :key="index">
                                    <img x-show="currentSlide === index"
                                         :src="slide"
                                         alt="{{ $product->name }} - Image {{ '${index + 1}' }}"
                                         class="h-full w-full object-cover transition-opacity duration-300 ease-in-out"
                                         x-transition:enter="opacity-0"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         x-transition:leave="opacity-100"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         onerror="this.onerror=null; this.src='https://placehold.co/600x400?text=Image+Not+Available';"
                                    >
                                </template>
                            </div>
                            {{-- Pijlen en indicators als er meerdere slides zijn --}}
                            @if (count($photoUrls) > 1)
                                {{-- Vorige slide --}}
                                <button @click="currentSlide = (currentSlide - 1 + slides.length) % slides.length"
                                        class="absolute top-1/2 left-2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 dark:hover:bg-opacity-90 focus:outline-none z-10">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                                {{-- Volgende slide --}}
                                <button @click="currentSlide = (currentSlide + 1) % slides.length"
                                        class="absolute top-1/2 right-2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 dark:hover:bg-opacity-90 focus:outline-none z-10">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                                {{-- Slide indicators (bolletjes) --}}
                                <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2 z-10">
                                    <template x-for="(slide, index) in slides" :key="index">
                                        <button @click="currentSlide = index"
                                                :class="{'bg-white': currentSlide === index, 'bg-gray-400': currentSlide !== index}"
                                                class="h-2 w-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none"></button>
                                    </template>
                                </div>
                            @endif
                        </div>
                    @else
                        {{-- Geen afbeelding --}}
                        <div class="h-96 w-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                </div>
                {{-- Rechterzijde: informatie en knoppen --}}
                <div class="md:w-1/2 p-6 flex flex-col justify-between">
                    <div>
                        {{-- Titel + categorie --}}
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $product->name }}</h1>
                        @if($product->category)
                            <span class="text-sm text-gray-500 dark:text-gray-400 mb-4 block">Category: {{ $product->category->name }}</span>
                        @endif

                        {{-- Reviews + sterren + sales --}}
                        <div class="flex items-center mb-4">
                            @if ($reviewCount > 0)
                                {{-- Sterren --}}
                            @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.28 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                @endfor
                                <span class="ml-2 text-gray-600 dark:text-gray-300">{{ number_format($product->average_rating, 1) }} out of 5</span>
                                <span class="mx-2 text-gray-400 dark:text-gray-500">|</span>
                                <span class="text-gray-600 dark:text-gray-300">{{ $reviewCount }} {{ Str::plural('Review', $reviewCount) }}</span>
                            @else
                                <span class="text-gray-500 dark:text-gray-400">No reviews yet.</span>
                            @endif
                            <span class="mx-2 text-gray-400 dark:text-gray-500">|</span>
                            <span class="text-gray-600 dark:text-gray-300">{{ $product->completed_orders_count }} {{ Str::plural('Sale', $product->completed_orders_count) }}</span>
                        </div>
                        {{-- Prijs --}}
                        <p class="text-3xl text-gray-700 dark:text-gray-200 font-semibold mb-6">€{{ number_format($product->price, 2, ',', '.') }}</p>
                        {{-- Beschrijving (HTML allowed) --}}
                        <div class="prose dark:prose-invert max-w-none mb-6 text-gray-600 dark:text-gray-300">
                            {!! $product->description !!}
                        </div>
                    </div>
                    {{-- Knoppen voor klanten (geen admin) --}}
                    @if(!auth()->check() || (auth()->check() && !auth()->user()->isAdmin()))
                        <div class="mt-6 space-y-3">
                            {{-- Toevoegen aan winkelmand --}}
                            <button wire:click="addToCart" class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white font-bold py-3 px-4 rounded-lg transition duration-150 ease-in-out flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                Add to Cart
                            </button>
                            {{-- Direct kopen --}}
                            <button wire:click="buyNow" class="w-full bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white font-bold py-3 px-4 rounded-lg transition duration-150 ease-in-out flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                Buy Now
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Klantbeoordelingen --}}
            <div class="mt-8 p-6">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Customer Reviews</h2>
                @if ($reviews->isNotEmpty())
                    <div class="space-y-6">
                        @foreach ($reviews as $review)
                            <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                <div class="flex items-center mb-2">
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ $review->user->name ?? 'Anonymous' }}</span>
                                    <span class="mx-2 text-gray-400 dark:text-gray-500">-</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $review->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex items-center mb-1">
                                    {{-- Sterren --}}
                                @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.28 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    @endfor
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">({{ $review->rating }}/5)</span>
                                </div>
                                <p class="text-gray-600 dark:text-gray-300 italic">"{{ $review->comment }}"</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6">
                        {{ $reviews->links() }}
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">This product has no reviews yet.</p>
                @endif
            </div>

            {{-- Aanbevolen producten --}}
            @if ($randomProducts->isNotEmpty() && (!auth()->check() || (auth()->check() && !auth()->user()->isAdmin())))
                <div class="mt-12 p-6 border-t border-gray-200 dark:border-gray-700">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">You might also like</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach ($randomProducts as $randomProduct)
                            <div class="bg-white dark:bg-gray-700/90 border border-gray-100 dark:border-gray-600 rounded-lg shadow-md overflow-hidden transition-transform duration-300 hover:scale-105 hover:shadow-lg dark:hover:shadow-gray-900/50 flex flex-col">
                                <a href="{{ route('products.show', ['slug' => $randomProduct->slug]) }}" wire:navigate class="block">
                                    @if ($randomProduct->photo)
                                        <img src="{{ $randomProduct->photo->getUrl() }}" alt="{{ $randomProduct->name }}"
                                             class="w-full h-48 object-cover"
                                             onerror="this.onerror=null; this.src='https://placehold.co/400x300?text=No+Image';"
                                        >
                                    @else
                                        <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </a>
                                <div class="p-4 flex flex-col flex-grow">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1 truncate">
                                        <a href="{{ route('products.show', ['slug' => $randomProduct->slug]) }}" wire:navigate class="hover:text-blue-600 dark:hover:text-blue-400">
                                            {{ $randomProduct->name }}
                                        </a>
                                    </h3>

                                    {{-- Sterren + Sales --}}
                                    <div class="mt-1 text-xs text-gray-700 dark:text-gray-300 flex items-center">
                                        @if($randomProduct->average_rating > 0)
                                            <div class="flex items-center">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $randomProduct->average_rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.28 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                @endfor
                                                <span class="ml-1 text-gray-600 dark:text-gray-400">({{ number_format($randomProduct->average_rating, 1) }})</span>
                                            </div>
                                            <span class="mx-1">|</span>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">No ratings</span>
                                            <span class="mx-1">|</span>
                                        @endif
                                        <span>Sales: {{ $randomProduct->completed_orders_count ?? 0 }}</span>
                                    </div>

                                    <p class="text-gray-700 dark:text-gray-200 font-bold text-xl mb-3">€{{ number_format($randomProduct->price, 2, ',', '.') }}</p>

                                    <div class="mt-auto">
                                        <button wire:click="addRandomProductToCart({{ $randomProduct->id }})" class="w-full bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition duration-150 ease-in-out flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                            Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
