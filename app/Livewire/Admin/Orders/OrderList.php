<?php

namespace App\Livewire\Admin\Orders;

use Livewire\Component;
use App\Models\Order;

class OrderList extends Component
{
    public $orders;

    public function mount()
    {
        $this->authorize('viewAny', Order::class);
        // Laad alle orders inclusief hun order items
        $this->orders = Order::with('items')->latest()->get();
    }
    public function render()
    {
        return view('livewire.admin.orders.order-list');
    }
}
