<div class="p-4 bg-white shadow-md rounded-lg dark:bg-gray-800">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Order #{{ $order->id }} Details</h2>

        <a href="{{ route('admin.orders.invoice', $order) }}"
           class="bg-gray-900 text-white rounded font-semibold hover:bg-gray-800 transition inline-flex items-center px-4 py-2 duration-200 dark:bg-indigo-700 dark:hover:bg-indigo-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Download Invoice
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

    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">Product Details</h3>
    <div class="space-y-6">
{{--        Toon alle items van de bestelling--}}
        @forelse ($order->items as $item)
            <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h4 class="text-lg font-medium text-gray-800 dark:text-white">Product: {{ $item->product->name }}</h4>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <p>Product Price: €{{ number_format($item->product_price, 2, ',', '.') }}</p>
                            <p>VAT ({{ number_format($order->vat_percentage, 2) }}%): €{{ number_format($item->product_taxes, 2, ',', '.') }}</p>
                            <p class="font-medium">Total: €{{ number_format($item->total_amount, 2, ',', '.') }}</p>
                        </div>
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
            </div>
        @empty
            <p class="text-gray-600 dark:text-gray-400">No items found in this order.</p>
        @endforelse
    </div>
</div>
