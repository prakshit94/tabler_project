<div class="modal-header">
    <h5 class="modal-title">Order Details: #{{ $order->order_number }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row mb-4">
        <div class="col-6">
            <div class="text-secondary mb-1 small uppercase font-weight-bold">From (Warehouse)</div>
            <address class="mb-0">
                <strong class="text-primary">{{ $order->warehouse->name }}</strong><br>
                {{ $order->warehouse->state }}
            </address>
        </div>
        <div class="col-6 text-end">
            <div class="text-secondary mb-1 small uppercase font-weight-bold">{{ $order->type == 'sale' ? 'To (Customer)' : 'From (Vendor)' }}</div>
            <address class="mb-0">
                <strong>{{ $order->party->name }}</strong><br>
                <span class="text-secondary">GSTIN: {{ $order->party->gstin }}</span><br>
                <span class="text-secondary">{{ $order->party->phone }}</span>
            </address>
        </div>
    </div>

    <div class="hr-text hr-text-left">Items List</div>
    <div class="table-responsive">
        <table class="table table-vcenter table-nowrap card-table">
            <thead>
                <tr>
                    <th class="w-1">#</th>
                    <th>Product</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Unit Price</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $idx => $item)
                <tr>
                    <td class="text-secondary">{{ $idx + 1 }}</td>
                    <td>
                        <div class="font-weight-bold">{{ $item->product->name }}</div>
                        <div class="text-secondary small">{{ $item->product->sku }}</div>
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end text-nowrap">₹ {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end font-weight-bold text-nowrap">₹ {{ number_format($item->total_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end font-weight-bold">Subtotal</td>
                    <td class="text-end text-nowrap">₹ {{ number_format($order->sub_total, 2) }}</td>
                </tr>
                <tr class="bg-surface-secondary">
                    <td colspan="4" class="text-end font-weight-bold text-uppercase">Total Amount</td>
                    <td class="text-end font-weight-bold text-nowrap text-primary h3 mb-0">₹ {{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<div class="modal-footer bg-surface-secondary">
    <div class="btn-list w-100">
        @if($order->status == 'pending')
        <form action="{{ route('erp.orders.update-status', $order->id) }}" method="POST" class="d-inline">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="completed">
            <button type="submit" class="btn btn-success">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                Complete Order
            </button>
        </form>
        @endif
        
        @if($order->status == 'completed')
        <a href="{{ route('erp.invoices.create', ['order_id' => $order->id]) }}" class="btn btn-azure">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 7l1 0" /><path d="M9 13l6 0" /><path d="M13 17l2 0" /></svg>
            Generate Invoice
        </a>
        @endif

        <button type="button" class="btn btn-white ms-auto" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
            Print
        </button>
    </div>
</div>
