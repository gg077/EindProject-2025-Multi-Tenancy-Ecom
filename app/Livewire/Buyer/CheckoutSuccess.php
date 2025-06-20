<?php

namespace App\Livewire\Buyer;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Cashier;
use Livewire\Component;

class CheckoutSuccess extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        // Je voorkomt hiermee dat iemand anders jouw order kan bekijken door zomaar een ID in de URL te zetten.
        if ($order->user_id !== Auth::id()) {
            return $this->redirect(route('home'), true);
        }

        $this->order = $order;

        // Stripe betaling verifiëren
        if($this->order->payment_provider == 'stripe') {

            $sessionId = request()->get('session_id'); // Haal de session_id op uit de URL die Stripe meestuurt (in success_url).

            if ($sessionId === null) {
                return $this->redirect(route('home'), true); // Als die ontbreekt → stuur gebruiker terug naar home.
            }

            // Stripe session ophalen en controleren
            $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

            if ($session->payment_status !== 'paid') { // Verifieer of de betaling bij Stripe echt gelukt is (payment_status = paid).
                dd('Payment not completed');
            }

            $orderId = $session['metadata']['order_id'] ?? null; // Haal de order_id op uit de metadata die Stripe meestuurt

            $order = Order::with('items.product')->findOrFail($orderId); // Je haalt het nu terug op en zoekt de bestelling terug uit je database

            $order->update(['status' => Order::STATUS_PAID, 'payment_reference' => $session->payment_intent]); //  Je bewaart ook het Stripe payment_intent ID als referentie

            $order->sendDownloadLinksEmail(); // order.php

            // Elk product krijgt er +1 bij op het veld completed_orders_count.
            foreach ($order->items as $item) {
                $item->product->increment('completed_orders_count');
            }
        }
    }

    public function render()
    {
        return view('livewire.buyer.checkout-success');
    }
}
