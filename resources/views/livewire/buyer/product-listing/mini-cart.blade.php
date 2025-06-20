<div class="relative" x-data="{ open: false }" @click.away="open = false">
    <!-- Winkelwagen knop -->
    <button
        @click="open = !open"
        class="relative p-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white focus:outline-none"
    >
        <!-- Icon (winkelwagen) -->
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <!-- Aantal producten in winkelwagen (badge) -->
    @if(count($cart) > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-indigo-600 dark:bg-indigo-500 rounded-full">
                {{ count($cart) }}
            </span>
        @endif
    </button>

    <!-- Dropdown winkelwagen -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 mt-2 w-96 bg-white dark:bg-gray-700 border border-gray-200 dark:border-indigo-700/50 rounded-lg shadow-xl overflow-hidden z-50"
    >
        <div class="p-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-600 pb-2">Shopping Cart</h3>

            @if(count($cart) > 0)
                <!-- Productlijst -->
                <div class="flow-root">
                    <ul class="-my-6 divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach($cart as $item)
                            <li class="py-6 flex">
                                <!-- Product afbeelding -->
                                <div class="flex-shrink-0 w-24 h-24 border border-gray-200 dark:border-gray-600 rounded-md overflow-hidden">
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-center object-cover" onerror="this.onerror=null; this.src='{{ asset($placeholderImage) }}';">
                                </div>

                                <!-- Product details -->
                                <div class="ml-4 flex-1 flex flex-col">
                                    <div>
                                        <div class="flex justify-between text-base font-medium text-gray-900 dark:text-white">
                                            <h3>{{ $item['name'] }}</h3>
                                            <p class="ml-4">
                                                €{{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex-1 flex items-end justify-between text-sm">
                                        <div class="flex items-center">
                                        </div>
                                        <!-- Verwijder knop -->
                                        <button
                                            wire:click="removeFromCart({{ $item['id'] }})"
                                            class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Subtotaal en afrekenen -->
                <div class="border-t border-gray-200 dark:border-gray-600 py-6">
                    <div class="flex justify-between text-base font-medium text-gray-900 dark:text-white">
                        <p>Subtotal</p>
                        <p>€{{ number_format($total, 2, ',', '.') }}</p>
                    </div>
                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Shipping and taxes calculated at checkout.</p>
                    <div class="mt-6">
                        <a
                            href="{{ route('checkout') }}"
                            class="flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600"
                        >
                            Checkout
                        </a>
                    </div>
                </div>
            @else
                <!-- Lege winkelwagen -->
                <div class="text-center py-6">
                    <p class="text-gray-500 dark:text-gray-400">Your cart is empty</p>
                </div>
            @endif
        </div>
    </div>
</div>
