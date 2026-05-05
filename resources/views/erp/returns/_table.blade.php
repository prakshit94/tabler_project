<div class="table-responsive">
    <table class="table card-table table-vcenter text-nowrap">
        <thead>
            <tr>
                <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" id="select-all"></th>
                <th>Return #</th>
                <th>Date</th>
                <th>Party</th>
                <th>Order #</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($returns as $return)
            <tr>
                <td><input class="form-check-input m-0 align-middle return-checkbox" type="checkbox" value="{{ $return->id }}"></td>
                <td>
                    <a href="{{ route('erp.returns.show', $return->id) }}" class="font-weight-bold">
                        {{ $return->return_number }}
                    </a>
                </td>
                <td>
                    <div class="text-secondary">
                        {{ \Carbon\Carbon::parse($return->return_date)->format('d M Y') }}
                    </div>
                </td>
                <td>{{ $return->party->name }}</td>
                <td>{{ $return->order->order_number ?? '-' }}</td>
                <td>
                    <span class="badge bg-green-lt">{{ ucfirst($return->status) }}</span>
                </td>
                <td class="text-end">
                    <a href="{{ route('erp.returns.show', $return->id) }}" class="btn btn-sm btn-white">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-5">
                    <div class="empty">
                        <p class="empty-title">No returns found</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="card-footer d-flex align-items-center" id="pagination-links">
    <div class="text-secondary small">
        Showing {{ $returns->firstItem() ?? 0 }} to {{ $returns->lastItem() ?? 0 }} of {{ $returns->total() }} entries
    </div>
    <div class="ms-auto">
        {{ $returns->links() }}
    </div>
</div>
