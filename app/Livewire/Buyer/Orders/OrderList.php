<?php

namespace App\Livewire\Buyer\Orders;

use Livewire\Component;
use App\Models\Order;

class OrderList extends Component
{
    public $orders;

    public function mount()
    {
        $this->authorize('viewAny', Order::class);
        $this->orders = Order::where('user_id', auth()->id())->with('items')->latest()->get();
    }
    public function render()
    {
        return view('livewire.buyer.orders.order-list');
    }
}
