<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Checkout</h2>
        </div>

        @if($errorMessage)
            <div class="p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400 dark:text-red-300" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700 dark:text-red-300">{{ $errorMessage }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="p-6">
            <!-- Cart Items -->
            <div class="space-y-4">
                @forelse($cartItems as $productId => $item)
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <input type="checkbox"
                                   value="{{ $productId }}"
                                   wire:model.live="selectedItems"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-400 border-gray-300 dark:border-gray-600 rounded">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $item['name'] }}</h3>
                            </div>
                        </div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">
                            €{{ number_format($item['price'], 2, ',', '.') }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400">Your cart is empty</p>
                    </div>
                @endforelse
            </div>

            <!-- Total -->
            <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-8">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-md font-medium text-gray-700 dark:text-gray-300">Subtotal</span>
                    <span class="text-md font-medium text-gray-700 dark:text-gray-300">€{{ number_format($subtotal, 2, ',', '.') }}</span>
                </div>
                
                @if($vatPercentage > 0)
                <div class="flex justify-between items-center mb-4">
                    <span class="text-md font-medium text-gray-700 dark:text-gray-300">VAT ({{ number_format($vatPercentage, 2) }}%)</span>
                    <span class="text-md font-medium text-gray-700 dark:text-gray-300">€{{ number_format($vatAmount, 2, ',', '.') }}</span>
                </div>
                @endif
                
                <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-gray-700">
                    <span class="text-lg font-medium text-gray-900 dark:text-white">Total</span>
                    <span class="text-2xl font-semibold text-gray-900 dark:text-white">€{{ number_format($total, 2, ',', '.') }}</span>
                </div>
            </div>

            <!-- Payment Method Selection -->
            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Payment Method</h3>
                <div class="space-y-4">
                    <label class="flex items-center p-4 border dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 @if($paymentMethod === 'stripe') border-indigo-500 dark:border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 @endif">
                        <input type="radio"
                               wire:model="paymentMethod"
                               value="stripe"
                               class="h-4 w-4 text-indigo-600 dark:text-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 border-gray-300 dark:border-gray-600">
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-gray-900 dark:text-white">Credit Card (Stripe)</span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- Checkout Button -->
            <div class="mt-8">
                <button wire:click="checkout"
                        wire:loading.attr="disabled"
                        wire:loading.class="bg-gray-400 dark:bg-gray-600 cursor-not-allowed"
                        wire:loading.class.remove="bg-indigo-600 dark:bg-indigo-700 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600"
                        class="w-full px-6 py-3 text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600"
                    >
                    <span wire:loading.remove>Complete Purchase</span>
                    <span wire:loading>
                        Processing...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
