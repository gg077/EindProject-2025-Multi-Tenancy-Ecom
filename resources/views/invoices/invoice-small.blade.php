<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 15px;
            color: #333;
            font-size: 11px;
        }
        .header {
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .company-info {
            text-align: right;
            margin-bottom: 15px;
            font-size: 10px;
            line-height: 1.3;
        }
        .invoice-title {
            font-size: 22px;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 8px;
        }
        .invoice-details {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .invoice-details > div {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }
        .bill-to {
            padding-right: 20px;
            font-size: 10px;
        }
        .invoice-info {
            text-align: right;
            font-size: 10px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #4F46E5;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #4F46E5;
            color: white;
            font-weight: bold;
            font-size: 11px;
        }
        .items-table .text-right {
            text-align: right;
        }
        .total-section {
            margin-top: 20px;
            text-align: right;
            font-size: 11px;
        }
        .total-row {
            margin-bottom: 6px;
        }
        .total-label {
            display: inline-block;
            width: 120px;
            text-align: right;
            margin-right: 15px;
        }
        .total-amount {
            font-weight: bold;
        }
        .grand-total {
            font-size: 14px;
            font-weight: bold;
            color: #4F46E5;
            border-top: 2px solid #4F46E5;
            padding-top: 8px;
            margin-top: 8px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 9px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 8px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: -3px;
        }
        .status-paid {
            background-color: #10B981;
            color: white;
        }
        .status-pending {
            background-color: #F59E0B;
            color: white;
        }
        .status-failed {
            background-color: #EF4444;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <div style="font-size: 16px; font-weight: bold;">{{ tenant('website_name') ?? 'Your Company' }}</div>
            <div>{{ tenant('website_description') ?? 'Professional Services' }}</div>
            @if($tenantAddress)
                <div style="margin-top: 6px; line-height: 1.3;">
                    @if($tenantAddress->address_line_1)
                        <div>{{ $tenantAddress->address_line_1 }}</div>
                    @endif
                    @if($tenantAddress->address_line_2)
                        <div>{{ $tenantAddress->address_line_2 }}</div>
                    @endif
                    @if($tenantAddress->city || $tenantAddress->state || $tenantAddress->postal_code)
                        <div>
                            {{ collect([$tenantAddress->city, $tenantAddress->state, $tenantAddress->postal_code])->filter()->implode(', ') }}
                        </div>
                    @endif
                    @if($tenantAddress->country)
                        <div>{{ $tenantAddress->country }}</div>
                    @endif
                </div>
            @endif
            @if(tenant('vat_number'))
                <div style="margin-top: 6px;">
                    <small><strong>VAT:</strong> {{ tenant('vat_number') }}</small>
                </div>
            @endif
        </div>
        <div class="invoice-title">INVOICE</div>
    </div>

    <div class="invoice-details">
        <div class="bill-to">
            <div class="section-title">Bill To:</div>
            <div><strong>{{ $order->user->name }}</strong></div>
            <div>{{ $order->user->email }}</div>
        </div>
        <div class="invoice-info">
            <div class="section-title">Invoice Details:</div>
            <div><strong>Invoice #:</strong> {{ $order->id }}</div>
            <div><strong>Date:</strong> {{ $order->created_at->format('F d, Y') }}</div>
            <div><strong>Status:</strong>
                <span class="status-badge status-{{ strtolower($order->status) }}">
                    {{ $order->status }}
                 </span>
            </div>
            @if($order->payment_reference)
                <div><strong>Payment id:</strong> {{ $order->payment_reference }}</div>
            @endif
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Product</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">VAT ({{ number_format($order->vat_percentage, 2) }}%)</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>                    
                    <td>
                        <strong>{{ $item->product->name ?? 'Product Removed' }}</strong>
                        @if($item->product && $item->product->description)
                            <br><small style="color: #666;">{{ \Illuminate\Support\Str::limit($item->product->description, 100) }}</small>
                        @endif
                    </td>
                    <td class="text-right">€{{ number_format($item->product_price, 2, ',', '.') }}</td>
                    <td class="text-right">€{{ number_format($item->product_taxes, 2, ',', '.') }}</td>
                    <td class="text-right">€{{ number_format($item->total_amount, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>    <div class="total-section">
        @php
            $subtotal = $order->total_amount - $order->order_taxes;
            $totalTax = $order->order_taxes;
        @endphp

        <div class="total-row">
            <span class="total-label">Subtotal:</span>
            <span class="total-amount">€{{ number_format($subtotal, 2, ',', '.') }}</span>
        </div>

        <div class="total-row">
            <span class="total-label">VAT ({{ number_format($order->vat_percentage, 2) }}%):</span>
            <span class="total-amount">€{{ number_format($totalTax, 2, ',', '.') }}</span>
        </div>

                <div class="total-row grand-total">
            <span class="total-label">Total:</span>
            <span class="total-amount">€{{ number_format($order->total_amount, 2, ',', '.') }}</span>
        </div>
    </div>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>This is an automated invoice.</p>
        @if($order->isPaid())
            <p><strong>Payment Status:</strong> Payment has been successfully processed.</p>
        @endif
    </div>
</body>
</html>
