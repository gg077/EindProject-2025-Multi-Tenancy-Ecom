<div class="max-w-4xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <div class="text-center">
                <!-- Success Icon -->
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900/30 mb-4">
                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <!-- Titel en bevestigingstekst -->
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Thank you for your purchase!</h2>
                <p class="text-lg text-gray-600 dark:text-gray-300 mb-8">Your order has been successfully processed.</p>

                <!-- Order Details -->
                <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Details</h3>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                        <!-- Rastersysteem: Ordernummer, datum, betaalmethode -->
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Order Number</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $order->id }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $order->created_at->format('F j, Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Method</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst($order->payment_provider) }}</dd>
                            </div>
                        </dl>
                        <!-- PRIJSOVERZICHT -->
                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-600">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Price Breakdown</h4>
                            <div class="grid grid-cols-2 gap-1 text-sm">
                                <dt class="text-gray-500 dark:text-gray-400">Subtotal:</dt>
                                <dd class="text-gray-900 dark:text-white text-right">€{{ number_format($order->total_amount - $order->order_taxes, 2, ',', '.') }}</dd>
                                
                                <dt class="text-gray-500 dark:text-gray-400">VAT ({{ number_format($order->vat_percentage, 2) }}%):</dt>
                                <dd class="text-gray-900 dark:text-white text-right">€{{ number_format($order->order_taxes, 2, ',', '.') }}</dd>
                                
                                <dt class="font-medium text-gray-700 dark:text-gray-300">Total:</dt>
                                <dd class="font-medium text-gray-900 dark:text-white text-right">€{{ number_format($order->total_amount, 2, ',', '.') }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- OVERZICHT AANGEKOCHTE PRODUCTEN -->
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Purchased Items</h3>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex justify-between items-start p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->product->name }}</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $item->product->description }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Quantity: {{ $item->quantity }}</p>
                                    <!-- DOWNLOAD LINK indien beschikbaar -->
                                    @if($item->product->download_link)
                                        <div class="mt-2 p-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded">
                                            <p class="text-xs text-green-700 dark:text-green-300 font-medium mb-1">Download beschikbaar:</p>
                                            <a href="{{ $item->product->download_link }}"
                                               target="_blank"
                                               class="inline-flex items-center text-sm text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-200 underline">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Download nu
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <!-- Prijsinfo rechts van elk item -->
                                <div class="text-right">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        Price: €{{ number_format($item->product_price, 2, ',', '.') }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        VAT ({{ number_format($order->vat_percentage, 2) }}%): €{{ number_format($item->product_taxes, 2, ',', '.') }}
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                                        Total: €{{ number_format($item->total_amount, 2, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                            <p class="text-xs text-gray-500 opacity-70">Click ‘View Order’ to download your invoice.</p>
                    </div>
                </div>

                <!-- ACTIEKNOPPEN: Naar order of terug naar winkel -->
                <div class="mt-8 space-x-4">
                    <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                        View Order
                    </a>
                    <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
