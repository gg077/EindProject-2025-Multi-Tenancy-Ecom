<?php

namespace App\Livewire\Buyer\Orders;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class OrderDetails extends Component
{
    public Order $order;

    public array $existingReviews = []; // array om alle bestaande reviews op te slaan
    protected $listeners = ['reviewSubmitted' => 'refreshOrderData'];

    public function mount(Order $order): void
    {
        $this->authorize('view', $order);
        $this->order = $order;
        $this->order->load(['items.product']);
        $this->loadOrderData();
    }

    // Deze functie wordt automatisch getriggerd als het event 'reviewSubmitted' afgaat
    #[On('reviewSubmitted')]
    public function refreshOrderData($productId = null, $orderId = null): void
    {
        // Controleer of de review betrekking heeft op deze bestelling (of geen ID is meegegeven)
        if ($orderId === null || $orderId == $this->order->id) {
            $this->loadOrderData();
        }
    }

    // Deze functie haalt bestaande reviews op voor de producten in deze bestelling
    public function loadOrderData(): void
    {
        // Enkel uitvoeren als de gebruiker is ingelogd
        if (Auth::check()) {
            // Verzamel alle unieke product_id's uit de orderitems
            $productIds = $this->order->items->pluck('product_id')->unique()->toArray();
            // Haal alle reviews op die door de gebruiker zijn gemaakt voor dit order en deze producten
            $reviews = \App\Models\Review::where('user_id', Auth::id())
                ->where('order_id', $this->order->id)
                ->whereIn('product_id', $productIds)
                ->get()
                ->keyBy('product_id');

            // Vul de existingReviews array met de gevonden reviews (of null als er geen is)
            $this->existingReviews = [];
            foreach ($productIds as $productId) {
                $this->existingReviews[$productId] = $reviews->get($productId);
            }
        }
    }

    public function render()
    {
        return view('livewire.buyer.orders.order-details');
    }
}
