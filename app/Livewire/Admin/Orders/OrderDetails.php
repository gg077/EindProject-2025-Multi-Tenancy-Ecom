<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use Livewire\Component;

class OrderDetails extends Component
{
    public Order $order;

    public function mount(Order $order): void
    {
        $this->authorize('view', $order);
        // Sla de bestelling op
        $this->order = $order;
        // Laad ook meteen alle bijbehorende producten van de bestelling
        $this->order->load(['items.product']);
    }

    public function render()
    {
        return view('livewire.admin.orders.order-details');
    }
}
