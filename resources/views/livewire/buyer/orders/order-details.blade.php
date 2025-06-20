<div class="p-4 bg-white shadow-md rounded-lg dark:bg-gray-800">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Order #{{ $order->id }} Details</h2>

        <a href="{{ route('orders.invoice', $order) }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Download Invoice PDF
        </a>
    </div>

    <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-200">Order Information</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Status:</strong> <span class="px-2 py-1 text-xs font-semibold leading-tight {{ $order->status === \App\Models\Order::STATUS_PAID ? 'text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100' : ($order->status === \App\Models\Order::STATUS_PENDING ? 'text-yellow-700 bg-yellow-100 rounded-full dark:bg-yellow-700 dark:text-yellow-100' : 'text-red-700 bg-red-100 rounded-full dark:bg-red-700 dark:text-red-100') }}">{{ $order->status }}</span></p>
        <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Subtotal:</strong> €{{ number_format($order->total_amount - $order->order_taxes, 2, ',', '.') }}</p>
        <p class="text-sm text-gray-600 dark:text-gray-400"><strong>VAT ({{ number_format($order->vat_percentage, 2) }}%):</strong> €{{ number_format($order->order_taxes, 2, ',', '.') }}</p>
        <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Total Amount:</strong> €{{ number_format($order->total_amount, 2, ',', '.') }}</p>
        <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y, g:i a') }}</p>
        @if($order->payment_reference)
            <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Payment id:</strong> <span class="font-mono text-xs">{{ $order->payment_reference }}</span></p>
        @endif
    </div>

    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">Items & Reviews</h3>
    <div class="space-y-6">
        @forelse ($order->items as $item)
            <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h4 class="text-lg font-medium text-gray-800 dark:text-white">{{ $item->product->name }}</h4>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <p>Unit Price: €{{ number_format($item->product_price, 2, ',', '.') }}</p>
                            <p>VAT ({{ number_format($order->vat_percentage, 2) }}%): €{{ number_format($item->product_taxes, 2, ',', '.') }}</p>
                            <p class="font-medium">Total: €{{ number_format($item->total_amount, 2, ',', '.') }}</p>
                        </div>

                        @if($order->isPaid() && $item->product->download_link)
                            <div class="mt-2 p-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded">
                                <p class="text-sm text-green-700 dark:text-green-300 font-medium mb-1">Download beschikbaar:</p>
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
                    @if ($item->product->photo)
                        <img src="{{ $item->product->photo->getUrl() }}" alt="{{ $item->product->name }}" class="w-20 h-20 object-cover rounded">
                    @else
                        <div class="w-20 h-20 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center text-gray-400 dark:text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                </div>

                @php
                    // bekijk of er al een review bestaat voor dit specifieke product
                    $existingReviewForProduct = $existingReviews[$item->product->id] ?? null;
                @endphp

                {{--alleen de koper van de bestelling toegang geven--}}
                @if(auth()->id() == $order->user_id)
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <h5 class="text-md font-semibold text-gray-700 dark:text-gray-200 mb-2">Product Review</h5>
                        @if ($existingReviewForProduct)
                            <div class="p-3 text-sm text-blue-700 bg-blue-100 rounded-lg dark:bg-gray-700 dark:text-blue-300">
                                <p class="font-semibold">Your Review:</p>
                                <div class="flex items-center my-1">
                                    {{-- for lus om de 5 sterren te maken  --}}
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= $existingReviewForProduct->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-500' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.28 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    @endfor
                                    <span class="ml-2 text-sm font-medium">({{ $existingReviewForProduct->rating }}/5)</span>
                                </div>
                                <p class="pl-1 italic">"{{ $existingReviewForProduct->comment }}"</p>
                            </div>
                        @else
{{--                            Als de gebruiker betaald heeft Dan wordt het submit-review Livewire-component getoond. Hiermee kan de gebruiker een beoordeling indienen.--}}
                            @if ($order->isPaid())
                                <livewire:buyer.submit-review :product-id="$item->product->id" :order-id="$order->id" :key="'review-' . $item->product->id . '-' . $order->id" />
                            @else
{{--                                Als de order nog niet betaald is: Dan zien ze enkel dit bericht:--}}
                                <p class="text-sm text-gray-500 dark:text-gray-400">You can review this product once the order is completed or paid.</p>
                            @endif
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <p class="text-gray-600 dark:text-gray-400">No items found in this order.</p>
        @endforelse
    </div>
</div>
