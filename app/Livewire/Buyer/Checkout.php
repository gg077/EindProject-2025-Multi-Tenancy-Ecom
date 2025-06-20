<?php

namespace App\Livewire\Buyer;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Checkout extends Component
{
    public $selectedItems = [];
    public $paymentMethod = 'stripe';
    public $errorMessage = '';
    public $cartItems = [];
    public $total = 0;
    public $subtotal = 0;
    public $vatAmount = 0;
    public $vatPercentage = 0;

    public function mount()
    {
        // Als gebruiker niet is ingelogd, stuur naar login
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        // Laad de cart uit de sessie
        $this->cartItems = session()->get('cart', []);
        // Stel geselecteerde producten in
        $this->selectedItems = array_keys($this->cartItems); // → alles wordt standaard geselecteerd
        // Haal btw-percentage van de tenant uit database
        $this->vatPercentage = tenant()->vat_percentage ?? 0;
        
        $this->calculateTotal(); // bereken
    }

    //Herbereken prijzen (subtotaal, btw, totaal)
    public function calculateTotal()
    {
        // Haalt de geselecteerde product-ID's op uit de cart
        $productIds = array_intersect($this->selectedItems, array_keys($this->cartItems));

        // Vraagt de echte prijs op uit de database
        $dbProducts = Product::whereIn('id', $productIds)->get();

        // Som van prijzen zonder btw
        $this->subtotal = $dbProducts->sum('price');

        // Herbereken btw
        $this->vatPercentage = tenant()->vat_percentage ?? 0; // Haal btw-percentage van de tenant, controleer of deze bestaat
        $this->vatAmount = $this->subtotal * ($this->vatPercentage / 100); // Bereken btw bedrag
        $this->total = $this->subtotal + $this->vatAmount; // Bereken totaal inclusief btw
    }

    // Automatisch herberekenen bij wijzigen van selectie,
    public function updatedSelectedItems()
    {
        $this->calculateTotal();
    }

    // Start het afrekenproces
    public function checkout()
    {
        // Nogmaals check of gebruiker is ingelogd
        if (!Auth::check()) {
            session(['intended_checkout' => true]);
            return redirect()->route('login');
        }
        // Valideer de betaalmethode
        $this->validate([
            'paymentMethod' => 'required|in:stripe',
        ]);

        // Haal de geselecteerde product-ID's op
        $productIds = array_intersect($this->selectedItems, array_keys($this->cartItems));

        // Check of er producten zijn geselecteerd
        if (empty($productIds)) {
            $this->errorMessage = 'Please select at least one product to checkout.';
            return;
        }

        // Haal producten veilig op uit database / Hiermee voorkom je dat gebruikers zelf de prijs wijzigen
        $dbProducts = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $this->calculateTotal(); // Herbereken prijzen (subtotaal, btw, totaal)

        DB::beginTransaction(); // Start een database transactie, Hierdoor wordt alles teruggedraaid als er iets misgaat.
        $order = null;
        try {
            // Maak de order aan
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $this->total, // Will be recalculated below based on verified prices
                'order_taxes' => $this->vatAmount, // Store the total VAT amount
                'vat_percentage' => $this->vatPercentage,
                'payment_provider' => $this->paymentMethod,
            ]);
            // Extra velden om mee te sturen naar Stripe
            $custom_fields[] = [
                'name' => 'Order ID',
                'value' => $order->id,
            ];
            
            // Voeg producten toe aan order
            foreach ($productIds as $productId) {
                // sla over als de product niet bestaat
                if (!isset($dbProducts[$productId])) {
                    continue;
                }
                
                $product = $dbProducts[$productId];
                // dus producttax = product prijs maal de percentage die we hebben opgehaald delen door 100
                $itemVatAmount = $product->price * ($this->vatPercentage / 100);
                // itemtotal = product prijs + producttax
                $itemTotal = $product->price + $itemVatAmount;
                
                $order->items()->create([
                    'product_id' => $productId,
                    'quantity' => 1,
                    'product_price' => $product->price, // Secure: using database price
                    'product_taxes' => $itemVatAmount, // Secure: calculated server-side
                    'total_amount' => $itemTotal, // Secure: calculated server-side
                ]);
            }
            // Maak Stripe checkout-sessie aan
            $url = $this->processStripePayment($order, $custom_fields)->url; // Stripe retourneert een link → gebruiker wordt hiernaartoe gestuurd.
            DB::commit(); // als all goed gegaan is, commit de transactie
            // Verwijder items uit winkelwagen
            $this->removeItemsFromCart();
            return redirect()->away($url); // Stuur gebruiker naar Stripe
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorMessage = 'Payment processing failed. Please try again.';
            $order->update(['status' => Order::STATUS_FAILED]);
        }
    }

    //  Maakt een Stripe checkout sessie aan voor betaling.
    protected function processStripePayment(Order $order, $custom_fields)
    {
        // checkoutCharge($amount, $name, $quantity = 1, array $sessionOptions = [], array $customerOptions = [], array $productData = [])
        return Auth::user()->checkoutCharge(
            $this->total * 100, // Bedrag wordt meegegeven in centen (bv. 1299 = €12,99)
            tenant('website_name') . ' Order #' . $order->id,
            1,
            [
                'success_url' => route('checkout.success', $order) . '?session_id={CHECKOUT_SESSION_ID}', // Na Betaling wordt gebruiker naar deze URL gestuurd
                'cancel_url' => route('checkout.cancel', $order),
                'metadata' => ['order_id' => $order->id, 'user_id' => Auth::id()],
            ],
            [],
            [
                // Metadata zoals order_id en user_id worden ook meegestuurd naar Stripe
                'metadata' => [
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                ],
            ]
        );
    }

// Verwijdert de geselecteerde items uit de sessie na succesvolle bestelling.
    protected function removeItemsFromCart()
    {
        $cart = session()->get('cart', []);
        foreach ($this->selectedItems as $productId) {
            unset($cart[$productId]);
        }
        session(['cart' => $cart]);
    }

    public function render()
    {
        return view('livewire.buyer.checkout');
    }
}
