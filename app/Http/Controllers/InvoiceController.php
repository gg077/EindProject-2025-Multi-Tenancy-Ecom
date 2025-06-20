<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InvoiceController extends Controller
{
    use AuthorizesRequests; // staat toe om authorize te gebruiken in de controller

    public function downloadInvoice(Order $order)
    {
        // User kan alleen zijn eigen bestelling downloaden
        // of als hij de 'view order' permissie heeft
        $this->authorize('view', $order);

        // laad order inclusief producten
        $order->load(['user', 'items.product']);

        // laad tenant inclusief adres
        $tenantAddress = tenant()->address;

        // genereer pdf
        $pdf = Pdf::loadView('invoices.invoice-small', compact('order', 'tenantAddress'));

        // zet het op A4 portret
        $pdf->setPaper('A4', 'portrait');

        // Geef pdf klaar om te downloaden
        return $pdf->download('invoice-' . $order->id . '.pdf');
    }
}
