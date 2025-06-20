<?php

namespace App\Livewire\Buyer;

use App\Models\Product;
//use App\Models\Review;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SubmitReview extends Component
{
    public Product $product;
    public Order $order;
    public int $rating = 0;
    public string $comment = '';
    public bool $alreadyReviewed = false; // Heeft gebruiker al een review geschreven?
    public bool $orderAllowsReview = false;  // Is de order betaald? (mag er gereviewd worden?)

    protected function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }

    protected array $messages = [
        'rating.required' => 'Please select a star rating.',
        'rating.min' => 'Rating must be at least 1 star.',
        'rating.max' => 'Rating cannot be more than 5 stars.',
        'comment.required' => 'Please enter your review comment.',
        'comment.min' => 'Your comment must be at least 10 characters long.',
        'comment.max' => 'Your comment cannot exceed 1000 characters.',
    ];

    public function mount(int $productId, int $orderId): void
    {
        $this->product = Product::findOrFail($productId); // Haal het product op
        $this->order = Order::findOrFail($orderId); // Haal de order op
        // Beveiliging: mag alleen eigen bestelling reviewen
        abort_if($this->order->user_id != auth()->id(), 403);
        $this->authorize('view', $this->order); // policy check
        $this->checkIfReviewed(); // Check of deze gebruiker al gereviewd heeft
        $this->checkOrderAllowsReview(); // Check of de order betaald is
    }

    // Controleer of gebruiker al een review heeft geschreven
    public function checkIfReviewed(): void
    {
        if (Auth::check()) {
            $this->alreadyReviewed = Review::where('user_id', Auth::id())
                ->where('product_id', $this->product->id)
                ->where('order_id', $this->order->id)
                ->exists();
        }
    }

    // Check of de order in aanmerking komt voor review (bv. betaald)
    public function checkOrderAllowsReview(): void
    {
        // Access the custom accessor directly
        $this->orderAllowsReview = $this->order->isPaid();
    }

    // Opslaan van de review
    public function saveReview(): void
    {
        // Beveiliging: enkel eigen order en geen dubbele review
        abort_if($this->order->user_id != auth()->id(), 403);
        if ($this->alreadyReviewed || !$this->orderAllowsReview) {
            session()->flash('error', 'Review cannot be submitted.');
            return;
        }

        $this->validate(); // Valideer rating + comment
        // Maak nieuwe review aan
        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $this->product->id,
            'order_id' => $this->order->id,
            'rating' => $this->rating,
            'comment' => $this->comment,
        ]);

        $this->alreadyReviewed = true;

        // Herbereken gemiddelde score van product
        $this->product->average_rating = $this->product->reviews()->avg('rating');
        $this->product->save();

        session()->flash('success', 'Thank you for your review!');
        //  Notify andere componenten (bv. voor live updates)
        $this->dispatch('reviewSubmitted', productId: $this->product->id, orderId: $this->order->id);
    }

    public function render()
    {
        return view('livewire.buyer.submit-review');
    }
}
