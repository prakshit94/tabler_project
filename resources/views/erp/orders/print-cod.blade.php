<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>COD Label {{ $order->order_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #000; }
        .label-container { border: 2px solid #000; padding: 20px; width: 400px; margin: auto; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 10px; }
        .cod-amount { font-size: 24px; font-weight: bold; text-align: center; margin: 20px 0; }
        .section { margin-bottom: 10px; }
        .footer { border-top: 1px solid #ddd; margin-top: 20px; padding-top: 10px; font-size: 10px; }
    </style>
</head>
<body>
    <div class="label-container">
        <div class="header">
            <h2>COD DELIVERY</h2>
            <p>Order #{{ $order->order_number }}</p>
        </div>

        <div class="cod-amount">
            CASH TO COLLECT:<br>
            ₹ {{ number_format($order->total_amount, 2) }}
        </div>

        <div class="section">
            <strong>Customer:</strong><br>
            {{ $order->party->name }}<br>
            {{ $order->shippingAddress->address_line_1 ?? '' }}<br>
            {{ $order->shippingAddress->city ?? '' }}, {{ $order->shippingAddress->state ?? '' }}
        </div>

        <div class="section">
            <strong>Shipping From:</strong><br>
            {{ $order->warehouse->name }}
        </div>

        <div class="footer">
            Printed on: {{ date('d M, Y H:i') }}
        </div>
    </div>
</body>
</html>
