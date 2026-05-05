<div class="table-responsive">
    <table class="table table-vcenter table-mobile-md card-table">
        <thead>
            <tr>
                <th class="w-1"><input type="checkbox" class="form-check-input" id="select-all"></th>
                <th>Task ID</th>
                <th>Order Details</th>
                <th>Warehouse</th>
                <th>Assignment</th>
                <th class="text-center">Items</th>
                <th>Status</th>
                <th>Timeline</th>
                <th class="w-1"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($pickLists as $pl)
            <tr>
                <td><input type="checkbox" class="form-check-input task-checkbox" name="ids[]" value="{{ $pl->id }}"></td>
            <td data-label="Task ID">
                <div class="font-weight-medium text-primary">{{ $pl->pick_list_number }}</div>
                <div class="text-secondary small">Ref: #{{ $pl->id }}</div>
            </td>

            <td data-label="Order Details">
                <div class="d-flex py-1 align-items-center">
                    <div class="flex-fill">
                        {{-- ✅ FIX: null-safe relation --}}
                        <div class="font-weight-medium">
                            {{ optional(optional($pl->order)->party)->name ?? 'N/A' }}
                        </div>

                        <div class="text-secondary">
                            <a href="{{ route('erp.orders.show', $pl->order_id) }}" class="text-reset">
                                {{-- ✅ FIX: null-safe --}}
                                Order: {{ optional($pl->order)->order_number ?? '-' }}
                            </a>
                        </div>
                    </div>
                </div>
            </td>

            <td data-label="Warehouse">
                <div class="badge bg-outline text-muted">
                    {{-- ✅ FIX: null-safe --}}
                    {{ optional($pl->warehouse)->name ?? '-' }}
                </div>
            </td>

            <td data-label="Assignment">
                @if($pl->assignedTo)
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-xs me-2 rounded text-uppercase bg-blue-lt">
                            {{ substr($pl->assignedTo->name, 0, 1) }}
                        </span>
                        {{ $pl->assignedTo->name }}
                    </div>
                @else
                    <span class="text-muted small italic">Not Assigned</span>
                @endif
            </td>

            <td class="text-center" data-label="Items">
                <span class="badge badge-pill bg-blue-lt">
                    {{-- ✅ FIX: avoid N+1 but keep fallback --}}
                    {{ $pl->items_count ?? ($pl->relationLoaded('items') ? $pl->items->count() : 0) }} SKU
                </span>
            </td>

            <td data-label="Status">
                @php
                    $badgeCls = match($pl->status) {
                        'completed'   => 'bg-green-lt',
                        'in_progress' => 'bg-blue-lt',
                        'pending'     => 'bg-yellow-lt',
                        'cancelled'   => 'bg-red-lt',
                        default       => 'bg-secondary-lt',
                    };
                @endphp
                <span class="badge {{ $badgeCls }} text-uppercase">
                    {{ str_replace('_',' ',$pl->status) }}
                </span>
            </td>

            <td data-label="Timeline" class="text-secondary">
                <div>{{ $pl->created_at->format('d M, Y') }}</div>
                <div class="small">{{ $pl->created_at->diffForHumans() }}</div>
            </td>

            <td>
                <div class="btn-list flex-nowrap">
                    <a href="{{ route('erp.wms.pick-list.show', $pl) }}" class="btn btn-white btn-sm">
                        Open Task
                    </a>
                </div>
            </td>
        </tr>

        @empty
        <tr>
            <td colspan="9">
                <div class="empty">
                    <div class="empty-img">
                        <img src="{{ asset('tabler/static/illustrations/light/printer.png') }}" height="128" alt="">
                    </div>
                    <p class="empty-title">No picking tasks found</p>
                    <p class="empty-subtitle text-secondary">
                        Try adjusting your search or filter to find what you're looking for.
                    </p>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

</div>

@if($pickLists->hasPages())

<div class="card-footer d-flex align-items-center border-top-0" id="pagination-links">
    <p class="m-0 text-secondary d-none d-sm-block">
        Showing <span>{{ $pickLists->firstItem() }}</span> 
        to <span>{{ $pickLists->lastItem() }}</span> 
        of <span>{{ $pickLists->total() }}</span> entries
    </p>
    <div class="ms-auto">
        {{ $pickLists->links('pagination::bootstrap-5') }}
    </div>
</div>
@endif
