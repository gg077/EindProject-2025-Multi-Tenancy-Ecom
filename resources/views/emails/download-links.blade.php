<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Links - Bestelling #{{ $order->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .order-info {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 25px;
        }
        .download-item {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 6px;
        }
        .download-link {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
        .download-link:hover {
            background-color: #218838;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Bedankt voor je aankoop!</h1>
        <p>Je downloadlinks zijn klaar</p>
    </div>

    <div class="order-info">
        <h2>Bestelling Details</h2>
        <p><strong>Bestelnummer:</strong> #{{ $order->id }}</p>
        <p><strong>Datum:</strong> {{ $order->created_at->format('d-m-Y H:i') }}</p>
        <p><strong>Totaal bedrag:</strong> €{{ number_format($order->total_amount, 2, ',', '.') }}</p>
    </div>

    @if($downloadableItems->count() > 0)
        <h2>Je Downloads</h2>
        <p>Hieronder vind je de downloadlinks voor je aangekochte producten:</p>

        @foreach($downloadableItems as $item)
            <div class="download-item">
                <h3>{{ $item->product->name }}</h3>
                @if($item->product->description)
                    <p>{{ $item->product->description }}</p>
                @endif
                <p><strong>Aantal:</strong> {{ $item->quantity }}</p>
                <p><strong>Prijs:</strong> €{{ number_format($item->product_price, 2, ',', '.') }}</p>

                <a href="{{ $item->product->download_link }}" class="download-link" target="_blank">
                    📥 Download Nu
                </a>
            </div>
        @endforeach

        <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 6px; margin-top: 20px;">
            <p><strong>💡 Tip:</strong> Sla deze email op voor toekomstige referentie. Je kunt ook altijd inloggen op je account om je downloadlinks terug te vinden.</p>
        </div>
    @else
        <p>Er zijn geen downloadbare items in deze bestelling.</p>
    @endif

    <div class="footer">
        <p>Als je vragen hebt over je bestelling of download problemen ervaart, neem dan contact met ons op.</p>
        <p>Bedankt voor je vertrouwen!</p>
    </div>
</body>
</html>
