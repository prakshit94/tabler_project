<div class="table-responsive">
    <table class="table card-table table-vcenter text-nowrap">
        <thead>
            <tr>
                <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" id="select-all"></th>
                <th>Payment #</th>
                <th>Date</th>
                <th>Party</th>
                <th>Invoice #</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Reference #</th>
                <th>Notes</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
            <tr>
                <td><input class="form-check-input m-0 align-middle payment-checkbox" type="checkbox" value="{{ $payment->id }}"></td>
                <td>
                    <span class="font-weight-bold">{{ $payment->payment_number }}</span>
                </td>
                <td>
                    <div class="text-secondary">
                        {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                    </div>
                </td>
                <td>{{ $payment->party->name ?? $payment->invoice->party->name ?? '-' }}</td>
                <td>{{ $payment->invoice->invoice_number ?? '-' }}</td>
                <td>
                    <span class="font-weight-bold text-green">₹ {{ number_format($payment->amount, 2) }}</span>
                </td>
                <td>
                    <span class="badge bg-blue-lt">{{ ucfirst($payment->payment_method) }}</span>
                </td>
                <td>
                    <span class="text-muted small">{{ $payment->reference_number ?? '-' }}</span>
                </td>
                <td>
                    <span class="text-muted small" title="{{ $payment->notes }}">
                        {{ Str::limit($payment->notes, 20) }}
                    </span>
                </td>
                <td class="text-end">
                    <button class="btn btn-sm btn-white">View</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center py-5">
                    <div class="empty">
                        <p class="empty-title">No payments found</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="card-footer d-flex align-items-center" id="pagination-links">
    <div class="text-secondary small">
        Showing {{ $payments->firstItem() ?? 0 }} to {{ $payments->lastItem() ?? 0 }} of {{ $payments->total() }} entries
    </div>
    <div class="ms-auto">
        {{ $payments->links() }}
    </div>
</div>
