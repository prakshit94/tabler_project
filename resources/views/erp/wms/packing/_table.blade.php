<div class="table-responsive">
    <table class="table table-vcenter table-mobile-md card-table">
        <thead>
            <tr>
                <th class="w-1">
                    <input type="checkbox" class="form-check-input" id="select-all">
                </th>
                <th>Order Details</th>
                <th>Warehouse</th>
                <th class="text-center">Complexity</th>
                <th>Fulfillment</th>
                <th>Status</th>
                <th>Timeline</th>
                <th class="w-1"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>
                    <input type="checkbox" class="form-check-input order-checkbox" name="ids[]" value="{{ $order->id }}">
                </td>

            <td data-label="Order Details">
                <div class="d-flex py-1 align-items-center">
                    <div class="flex-fill">
                        <div class="font-weight-medium text-primary">
                            {{ $order->order_number }}
                        </div>
                        <div class="text-secondary small">
                            {{-- ✅ FIX: null-safe --}}
                            {{ optional($order->party)->name ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </td>

            <td data-label="Warehouse">
                <div class="badge bg-outline text-muted">
                    {{-- ✅ FIX: null-safe --}}
                    {{ optional($order->warehouse)->name ?? '-' }}
                </div>
            </td>

            <td class="text-center" data-label="Complexity">
                <span class="badge badge-pill bg-blue-lt">
                    {{-- ✅ FIX: avoid N+1 safely --}}
                    {{ $order->items_count ?? ($order->relationLoaded('items') ? $order->items->count() : 0) }} SKU
                </span>
                <div class="small text-secondary mt-1">
                    {{ $order->packages->count() }} pkg(s)
                </div>
            </td>

            <td data-label="Fulfillment">
                @php
                    $packedCount = $order->packages->where('status', 'packed')->count();
                    $totalPkg = $order->packages->count();
                    $percent = $totalPkg > 0 ? round(($packedCount / $totalPkg) * 100) : 0;
                @endphp

                <div class="d-flex align-items-center mb-1">
                    <div class="text-secondary small me-2">{{ $percent }}%</div>
                    <div class="progress progress-xs w-100">
                        <div class="progress-bar bg-primary"
                             style="width: {{ $percent }}%"
                             role="progressbar">
                        </div>
                    </div>
                </div>

                <div class="small text-muted">
                    {{ $packedCount }}/{{ $totalPkg }} sealed
                </div>
            </td>

            <td data-label="Status">
                @php
                    $badgeCls = match($order->status) {
                        'packed'  => 'bg-green-lt',
                        'packing' => 'bg-blue-lt',
                        'picked'  => 'bg-yellow-lt',
                        default   => 'bg-secondary-lt',
                    };
                @endphp
                <span class="badge {{ $badgeCls }} text-uppercase">
                    {{ str_replace('_',' ',$order->status) }}
                </span>
            </td>

            <td data-label="Timeline" class="text-secondary">
                <div>{{ $order->updated_at->format('d M, Y') }}</div>
                <div class="small">{{ $order->updated_at->diffForHumans() }}</div>
            </td>

            <td>
                <div class="btn-list flex-nowrap">
                    @if($order->status === 'picked')
                    <form method="POST" action="{{ route('erp.wms.packing.start', $order) }}">
                        @csrf
                        <button class="btn btn-warning btn-sm">
                            Start Packing
                        </button>
                    </form>
                    @else
                    <a href="{{ route('erp.wms.packing.show', $order) }}" class="btn btn-white btn-sm">
                        Open Station
                    </a>
                    @endif
                </div>
            </td>
        </tr>

        @empty
        <tr>
            <td colspan="8">
                <div class="empty">
                    <div class="empty-img">
                        <img src="{{ asset('tabler/static/illustrations/light/archive.png') }}" height="128" alt="">
                    </div>
                    <p class="empty-title">No orders in packing queue</p>
                    <p class="empty-subtitle text-secondary">
                        Try adjusting your search or warehouse filter.
                    </p>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

</div>

@if($orders->hasPages())

<div class="card-footer d-flex align-items-center border-top-0" id="pagination-links">
    <p class="m-0 text-secondary d-none d-sm-block">
        Showing 
        <span>{{ $orders->firstItem() }}</span> 
        to 
        <span>{{ $orders->lastItem() }}</span> 
        of 
        <span>{{ $orders->total() }}</span> entries
    </p>
    <div class="ms-auto">
        {{ $orders->links('pagination::bootstrap-5') }}
    </div>
</div>
@endif
