<div class="p-6 bg-white dark:bg-gray-800 rounded shadow space-y-4">

    <h1 class="text-2xl font-bold text-black dark:text-white mb-6">Orders
        <span class="text-gray-500 dark:text-gray-400 text-sm">({{ $orders->count() }})</span>
    </h1>

    @forelse ($orders as $order)
        <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 border dark:border-gray-600 rounded p-4 hover:bg-gray-100 dark:hover:bg-gray-600">
            <div class="space-y-1">

                <h2 class="font-semibold text-lg text-black dark:text-white">Order #{{ $order->id }} - €{{ number_format($order->total_amount, 2, ',', '.') }}</h2>

                <p class="text-gray-500 dark:text-gray-300 text-sm">Status:
                    @if($order->status == $order::STATUS_PENDING)
                        <span class="text-yellow-500 dark:text-yellow-400">In afwachting</span>
                    @elseif($order->status == $order::STATUS_PAID)
                        <span class="text-green-500 dark:text-green-400">Voltooid</span>
                    @elseif($order->status == $order::STATUS_FAILED)
                        <span class="text-red-500 dark:text-red-400">Mislukt</span>
                    @else
                        <span class="font-semibold dark:text-white">{{ ucfirst($order->status) }}</span>
                    @endif
                </p>
                <p class="text-gray-500 dark:text-gray-300 text-sm">Datum: {{ $order->created_at->format('d-m-Y H:i') }}</p>

                @if($order->payment_reference)
                    <p class="text-gray-500 dark:text-gray-300 text-sm">Payment id: <span class="font-mono text-xs">{{ $order->payment_reference }}</span></p>
                @endif

                {{-- user --}}
                <p class="text-gray-500 dark:text-gray-300 text-sm">User: {{ $order->user->email }}</p>

                <div class="text-gray-600 dark:text-gray-300 text-sm mt-2">
                    <p class="font-semibold dark:text-gray-200">Items:</p>
                    <ul class="list-disc ml-5 space-y-1">
                        @foreach ($order->items as $item)
                            <li>
                                {{ $item->product->name ?? 'Product verwijderd' }} -
                                €{{ number_format($item->product_price, 2, ',', '.') }}
                                + €{{ number_format($item->product_taxes, 2, ',', '.') }} btw =
                                <strong class="dark:text-white">€{{ number_format($item->total_amount, 2, ',', '.') }}</strong>

                                @if($order->isPaid() && $item->product && $item->product->download_link)
                                    <div class="mt-1">
                                        <a href="{{ $item->product->download_link }}"
                                           target="_blank"
                                           class="inline-flex items-center text-xs text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-200 underline">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Download
                                        </a>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <a wire:navigate href="{{ auth()->user()->isAdmin() ? route('admin.orders.show', $order) : route('orders.show', $order) }}" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200">Bekijken</a>

                <a href="{{ route('orders.invoice', $order) }}"
                   class="bg-gray-900 text-white hover:bg-gray-800 transition inline-flex items-center px-3 py-1 text-sm font-medium rounded duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Invoice PDF
                </a>
            </div>
        </div>
    @empty
        <p class="text-gray-600 dark:text-gray-300">Geen orders gevonden.</p>
    @endforelse

</div>
