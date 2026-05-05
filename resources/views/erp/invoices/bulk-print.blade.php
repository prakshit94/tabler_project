<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .page-break { page-break-after: always; }
        .header { text-align: center; margin-bottom: 30px; }
        .section { margin-bottom: 20px; }
        .row { width: 100%; display: table; }
        .col { display: table-cell; width: 50%; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        .text-right { text-align: right; }
        .total-section { margin-top: 30px; }
        .total-row { font-size: 14px; font-weight: bold; }
    </style>
</head>
<body>
    @foreach($invoices as $invoice)
    <div class="{{ !$loop->last ? 'page-break' : '' }}">
        <div class="header">
            <h1>INVOICE</h1>
            <p>{{ $invoice->invoice_number }}</p>
        </div>

        <div class="row section">
            <div class="col">
                <strong>From:</strong><br>
                {{ config('app.name') }}<br>
                Warehouse: {{ $invoice->order->warehouse->name ?? 'N/A' }}
            </div>
            <div class="col">
                <strong>To:</strong><br>
                {{ $invoice->party->name }}<br>
                Date: {{ $invoice->invoice_date->format('d M, Y') }}
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($item->total_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section text-right">
            <p>Subtotal: {{ number_format($invoice->sub_total, 2) }}</p>
            <p>Tax: {{ number_format($invoice->tax_amount, 2) }}</p>
            <p class="total-row">Total: ₹ {{ number_format($invoice->total_amount, 2) }}</p>
        </div>
    </div>
    @endforeach
</body>
</html>
