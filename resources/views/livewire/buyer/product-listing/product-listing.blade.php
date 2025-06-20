<div>
    @guest
        <div class="bg-indigo-50 dark:bg-indigo-900/20 border-b border-indigo-100 dark:border-indigo-800">
            <div class="container mx-auto px-4 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-indigo-700 dark:text-indigo-300">Sign in to get the best shopping experience</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('login') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">Sign in</a>
                        <a href="{{ route('register') }}" class="text-sm font-medium text-white bg-indigo-600 px-3 py-1 rounded-md hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600">Register</a>
                    </div>
                </div>
            </div>
        </div>
    @endguest

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

    <div class="container mx-auto px-4 py-8">
        <div class="gap-6">
            <!-- product Filters -->
            <div class="w-full flex-shrink-0 mb-4">
                <livewire:buyer.product-listing.product-filters />
            </div>

            <!-- Product Grid -->
            <div class="flex-1">
                @if($products->isEmpty())
                    <div class="text-center py-12">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">No products found</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or filter criteria.</p>
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                        @foreach($products as $product)
                            <a href="{{ route('products.show', ['slug' => $product->slug]) }}" class="block bg-white dark:bg-gray-700 rounded-lg shadow-sm overflow-hidden flex flex-col hover:shadow-lg dark:shadow-gray-900/30 transition-shadow duration-200 ease-in-out">
                                <div class="aspect-[4/5] w-full">
                                    @if($product->photo)
                                        <img src="{{ $product->photo->getUrl() }}"
                                             alt="{{ $product->name }}"
                                             class="w-full h-full object-cover"
                                             onerror="this.onerror=null; this.src='{{ $placeholderImage }}';">
                                    @else
                                        <img src="{{ $placeholderImage }}"
                                             alt="{{ $product->name }}"
                                             class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="p-2 flex-1 flex flex-col">
                                    <h3 class="text-xs font-medium text-gray-900 dark:text-white truncate">{{ $product->name }}</h3>
                                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 line-clamp-2 flex-1">{{ $product->description }}</p>
                                    <div class="mt-1 text-xs text-gray-700 dark:text-gray-300 flex items-center">
                                        @if($product->average_rating > 0)
                                            <div class="flex items-center">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.28 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                @endfor
                                                <span class="ml-1 text-gray-600 dark:text-gray-400">({{ number_format($product->average_rating, 1) }})</span>
                                            </div>
                                            <span class="mx-1">|</span>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">No ratings</span>
                                            <span class="mx-1">|</span>
                                        @endif
                                        <span>Sales: {{ $product->completed_orders_count ?? 0 }}</span>
                                    </div>
                                    <div class="mt-1.5 flex items-center justify-between">
                                        <p class="text-xs font-semibold text-gray-900 dark:text-white">€{{ number_format($product->price, 2, ',', '.') }}</p>
                                        <button
                                            wire:click.stop="addToCart({{ $product->id }})"
                                            class="text-xs bg-indigo-600 text-white px-1.5 py-0.5 rounded hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:ring-offset-1 dark:focus:ring-offset-gray-800"
                                        >
                                            Buy Now
                                        </button>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
