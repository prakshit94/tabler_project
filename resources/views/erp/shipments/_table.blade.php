<div class="table-responsive">
    <table class="table table-vcenter card-table table-striped">
        <thead>
            <tr>
                <th>Shipment #</th>
                <th>Order / Party</th>
                <th>Logistics</th>
                <th>Status</th>
                <th>Latest Update</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($shipments as $s)
            <tr>
            {{-- Shipment --}}
            <td>
                <div class="font-weight-bold">
                    <a href="{{ route('erp.shipments.show', $s) }}" class="text-reset">
                        {{ $s->shipment_number }}
                    </a>
                </div>
                <div class="text-muted small">
                    Created: {{ optional($s->created_at)->format('d M Y') }}
                </div>
            </td>

            {{-- Order / Party --}}
            <td>
                <div>
                    <a href="{{ route('erp.orders.show', $s->order_id) }}" class="text-reset fw-bold">
                        {{ optional($s->order)->order_number ?? '-' }}
                    </a>
                </div>
                <div class="text-muted small">
                    {{ optional(optional($s->order)->party)->name ?? 'Unknown Customer' }}
                </div>
            </td>

            {{-- Logistics --}}
            <td>
                <div class="fw-bold text-primary">
                    {{ $s->carrier ?? 'Self' }}
                </div>

                @if($s->tracking_number)
                    <div class="small">
                        @if($s->tracking_url)
                            <a href="{{ $s->tracking_url }}" target="_blank" class="text-decoration-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="24" height="24">
                                    <path d="M10 14l2 -2l2 2" />
                                </svg>
                                {{ $s->tracking_number }}
                            </a>
                        @else
                            <span class="text-secondary">
                                {{ $s->tracking_number }}
                            </span>
                        @endif
                    </div>
                @endif
            </td>

            {{-- Status --}}
            <td>
                @php 
                    $cls = match($s->status) { 
                        'delivered' => 'bg-green-lt text-green',
                        'in_transit' => 'bg-blue-lt text-blue',
                        'out_for_delivery' => 'bg-lime-lt text-lime',
                        'dispatched' => 'bg-cyan-lt text-cyan',
                        'failed' => 'bg-red-lt text-red',
                        'returned' => 'bg-purple-lt text-purple',
                        default => 'bg-secondary-lt text-secondary' 
                    }; 
                @endphp

                <span class="badge {{ $cls }} px-2 py-1">
                    {{ ucfirst(str_replace('_',' ',$s->status)) }}
                </span>

                @if($s->estimated_delivery && $s->status !== 'delivered')
                    <div class="text-muted extra-small mt-1">
                        ETA: {{ optional($s->estimated_delivery)->format('d M') }}
                    </div>
                @endif
            </td>

            {{-- Latest Event --}}
            <td>
                <div class="text-truncate"
                     style="max-width: 150px;"
                     title="{{ $s->latestEvent?->description }}">
                    {{ $s->latestEvent?->description ?? 'No events recorded' }}
                </div>

                @if($s->latestEvent)
                    <div class="text-muted extra-small">
                        {{ optional($s->latestEvent->event_at)->diffForHumans() }}
                    </div>
                @endif
            </td>

            {{-- Actions --}}
            <td class="text-end">
                <div class="btn-list justify-content-end">
                    <a href="{{ route('erp.shipments.show', $s) }}" class="btn btn-sm btn-white">
                        View
                    </a>
                </div>
            </td>

        </tr>

        @empty
        <tr>
            <td colspan="6" class="text-center py-5">
                <div class="empty">
                    <div class="empty-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24">
                            <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
                        </svg>
                    </div>
                    <p class="empty-title">No shipments found</p>
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

@if($shipments->hasPages())

<div class="card-footer d-flex align-items-center" id="pagination-links">
    <p class="m-0 text-muted d-none d-md-block">
        Showing 
        <span>{{ $shipments->firstItem() }}</span> 
        to 
        <span>{{ $shipments->lastItem() }}</span> 
        of 
        <span>{{ $shipments->total() }}</span> entries
    </p>
    <div class="ms-auto">
        {{ $shipments->links() }}
    </div>
</div>
@endif
