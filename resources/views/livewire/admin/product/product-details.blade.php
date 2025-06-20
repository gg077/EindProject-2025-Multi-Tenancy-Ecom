<div>
{{--    Enkel niet-admins of gasten zien deze bovenste headerbalk (met logo & mini-cart). Admins krijgen een andere layout.--}}
    @if(!auth()->check() || (auth()->check() && !auth()->user()->isAdmin()))
        <div class="bg-white dark:bg-gray-800 shadow-sm">
            <div class="container mx-auto px-4 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex-shrink-0">
                        {{-- <img class="h-12 w-auto" src="{{ asset('images/logo.png') }}" alt="Store Logo"> --}}
                        @guest
                            <a href="{{ route('home') }}" class="mr-5 flex items-center space-x-2" wire:navigate>
                                <x-app-logo />
                            </a>
                        @endguest
                    </div>
                    <livewire:buyer.product-listing.mini-cart />
                </div>
            </div>
        </div>
    @endif

    <div class="container mx-auto px-4 py-8 relative">
        @if (session()->has('message'))
            <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                {{ session('message') }}
            </div>
        @endif
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden">
            <div class="md:flex">
                <div class="md:w-1/2">
                    {{-- Verzamel alle unieke foto's van het product --}}
                    @php
                    // We maken een lege Laravel Collection aan met de naam $allPhotos
                        $allPhotos = collect();
                        // Checkt of het product foto's heeft (dus dat de photos-relatie geladen is én niet null).
                        if ($product->photos) {
                            // Gaat elke foto van het product af met een each() loop
                            $product->photos->each(function ($photo) use ($allPhotos) {
                                // Als deze foto-ID nog niet in $allPhotos zit dan voeg deze toe in de Collection door push()
                                if ($allPhotos->doesntContain('id', $photo->id)) {
                                    $allPhotos->push($photo);
                                }
                            });
                        }
                        // Nu transformeren we de $allPhotos collectie naar een lijst van image URLs.
                        $photoUrls = $allPhotos->map(fn($p) => $p->getUrl())->filter()->values()->toArray();
                    @endphp

                    {{-- Foto slideshow met Alpine.js --}}
                    @if (!empty($photoUrls))
                        {{-- Start met slide 0, zet de PHP array met URLs om naar JS array via json_encode()--}}
                        <div x-data="{ currentSlide: 0, slides: {{ json_encode($photoUrls) }} }" class="relative">
                            <div class="aspect-[4/3] w-full overflow-hidden">
                                {{-- Toon één afbeelding tegelijk --}}
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
                            {{-- Vorige knop --}}
                        @if (count($photoUrls) > 1)
                                <button @click="currentSlide = (currentSlide - 1 + slides.length) % slides.length"
                                        class="absolute top-1/2 left-2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 dark:hover:bg-opacity-90 focus:outline-none z-10">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                                {{-- Volgende knop --}}
                                <button @click="currentSlide = (currentSlide + 1) % slides.length"
                                        class="absolute top-1/2 right-2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 dark:hover:bg-opacity-90 focus:outline-none z-10">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                                {{-- Indicator-dotjes --}}
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
                        {{-- Placeholder als er geen foto's zijn --}}
                        <div class="h-96 w-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="md:w-1/2 p-6 flex flex-col justify-between">
                    <div>
                        {{-- Titel van product --}}
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $product->name }}</h1>
                        {{-- Categorie van het product --}}
                    @if($product->category)
                            <span class="text-sm text-gray-500 dark:text-gray-400 mb-4 block">Category: {{ $product->category->name }}</span>
                        @endif
                        {{-- Reviews + rating + aantal verkopen --}}
                        <div class="flex items-center mb-4">
                            @if ($reviewCount > 0)
                                {{-- Gemiddelde rating in sterren --}}
                            @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.28 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                @endfor
                                {{-- Gemiddelde score + aantal reviews --}}
                                <span class="ml-2 text-gray-600 dark:text-gray-300">{{ number_format($product->average_rating, 1) }} out of 5</span>
                                <span class="mx-2 text-gray-400 dark:text-gray-500">|</span>
                                <span class="text-gray-600 dark:text-gray-300">{{ $reviewCount }} {{ Str::plural('Review', $reviewCount) }}</span>
                            @else
                                {{-- Als er geen reviews zijn --}}
                                <span class="text-gray-500 dark:text-gray-400">No reviews yet.</span>
                            @endif
                                {{-- Aantal verkopen --}}
                                <span class="mx-2 text-gray-400 dark:text-gray-500">|</span>
                            <span class="text-gray-600 dark:text-gray-300">{{ $product->completed_orders_count }} {{ Str::plural('Sale', $product->completed_orders_count) }}</span>
                        </div>

                        {{-- Prijs van het product --}}
                        <p class="text-3xl text-gray-700 dark:text-gray-200 font-semibold mb-6">€{{ number_format($product->price, 2, ',', '.') }}</p>
                        {{-- Omschrijving (HTML toegestaan) --}}
                        <div class="prose dark:prose-invert max-w-none mb-6 text-gray-600 dark:text-gray-300">
                            {!! $product->description !!}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Customer review sectie --}}
            <div class="mt-8 p-6">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Customer Reviews</h2>
                @if ($reviews->isNotEmpty())
                    {{-- Reviews tonen --}}
                    <div class="space-y-6">
                        @foreach ($reviews as $review)
                            <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                <div class="flex items-center mb-2">
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ $review->user->name ?? 'Anonymous' }}</span>
                                    <span class="mx-2 text-gray-400 dark:text-gray-500">-</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $review->created_at->format('M d, Y') }}</span>
                                </div>
                                {{-- Sterren van deze review --}}
                                <div class="flex items-center mb-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.28 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    @endfor
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">({{ $review->rating }}/5)</span>
                                </div>
                                {{-- Commentaar van review --}}
                                <p class="text-gray-600 dark:text-gray-300 italic">"{{ $review->comment }}"</p>
                            </div>
                        @endforeach
                    </div>
                    {{-- Paginatie voor reviews --}}
                    <div class="mt-6">
                        {{ $reviews->links() }}
                    </div>
                @else
                    {{-- Geen reviews beschikbaar --}}
                    <p class="text-gray-500 dark:text-gray-400">This product has no reviews yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
