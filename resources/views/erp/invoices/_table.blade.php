<div class="table-responsive">
    <table class="table card-table table-vcenter text-nowrap">
        <thead>
            <tr>
                <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" id="select-all"></th>
                <th>Invoice #</th>
                <th>Date / Due</th>
                <th>Party</th>
                <th>Order #</th>
                <th>Subtotal</th>
                <th>Tax</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Pending</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
            <tr>
                <td><input class="form-check-input m-0 align-middle invoice-checkbox" type="checkbox" value="{{ $invoice->id }}"></td>
                <td>
                    <a href="{{ route('erp.invoices.show', $invoice->id) }}" class="font-weight-bold">
                        {{ $invoice->invoice_number }}
                    </a>
                </td>
                <td>
                    <div>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</div>
                    @if($invoice->due_date)
                        <div class="small text-muted">Due: {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</div>
                    @endif
                </td>
                <td>{{ $invoice->party->name }}</td>
                <td>{{ $invoice->order->order_number ?? '-' }}</td>
                <td>₹ {{ number_format($invoice->sub_total, 2) }}</td>
                <td>₹ {{ number_format($invoice->tax_amount, 2) }}</td>
                <td>
                    <span class="font-weight-bold">₹ {{ number_format($invoice->total_amount, 2) }}</span>
                </td>
                <td>
                    <span class="text-green">₹ {{ number_format($invoice->payments_sum_amount ?? 0, 2) }}</span>
                </td>
                <td>
                    @php
                        $pending = $invoice->total_amount - ($invoice->payments_sum_amount ?? 0);
                    @endphp
                    <span class="{{ $pending > 0 ? 'text-danger font-weight-bold' : 'text-secondary' }}">
                        ₹ {{ number_format($pending, 2) }}
                    </span>
                </td>
                <td>
                    @php
                        $badgeClass = match($invoice->status) {
                            'unpaid' => 'bg-yellow-lt',
                            'paid' => 'bg-green-lt',
                            'partial' => 'bg-blue-lt',
                            default => 'bg-secondary-lt'
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ ucfirst($invoice->status) }}</span>
                </td>
                <td class="text-end">
                    <a href="{{ route('erp.invoices.show', $invoice->id) }}" class="btn btn-sm btn-white">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="12" class="text-center py-5">
                    <div class="empty">
                        <p class="empty-title">No invoices found</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="card-footer d-flex align-items-center" id="pagination-links">
    <div class="text-secondary small">
        Showing {{ $invoices->firstItem() ?? 0 }} to {{ $invoices->lastItem() ?? 0 }} of {{ $invoices->total() }} entries
    </div>
    <div class="ms-auto">
        {{ $invoices->links() }}
    </div>
</div>
