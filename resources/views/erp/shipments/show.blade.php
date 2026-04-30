@extends('layouts.tabler')
@section('title', 'Shipment — ' . $shipment->shipment_number)
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('erp.shipments.index') }}">Shipments</a></li>
                    <li class="breadcrumb-item active">{{ $shipment->shipment_number }}</li>
                </ol>
                <h2 class="page-title">{{ $shipment->shipment_number }}</h2>
            </div>
            <div class="col-auto d-flex gap-2">
                <button class="btn btn-outline-secondary d-none d-md-inline-block" onclick="copyTrackingUrl()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 7m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" /><path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.412 .412 1.737 1.012" /></svg>
                    Copy Link
                </button>
                <button class="btn btn-outline-secondary d-none d-md-inline-block" onclick="window.print()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                    Label
                </button>
                @if(!in_array($shipment->status, ['delivered', 'returned']))
                <button class="btn btn-primary d-none d-md-inline-block" data-bs-toggle="modal" data-bs-target="#addEventModal">Update Status</button>
                <div class="dropdown">
                    <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Complete
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li>
                            <form method="POST" action="{{ route('erp.shipments.deliver', $shipment) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="dropdown-item text-success" onclick="return confirm('Mark as delivered?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12l5 5l10 -10" /></svg>
                                    Mark Delivered
                                </button>
                            </form>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#returnModal">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1" /></svg>
                                Mark Returned (RTO)
                            </button>
                        </li>
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        @if(session('success'))<div class="alert alert-success alert-dismissible mb-3">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif

        <div class="row row-cards">
            <div class="col-lg-5">
                <div class="card mb-3">
                    <div class="card-header"><h3 class="card-title">Shipment Info</h3></div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-5">Order</dt><dd class="col-7"><a href="{{ route('erp.orders.show', $shipment->order_id) }}">{{ $shipment->order->order_number }}</a></dd>
                            <dt class="col-5">Party</dt><dd class="col-7">{{ $shipment->order->party->name ?? '-' }}</dd>
                            <dt class="col-5">Carrier</dt><dd class="col-7">{{ $shipment->carrier ?? '-' }}</dd>
                            <dt class="col-5">Tracking #</dt>
                            <dd class="col-7">
                                @if($shipment->tracking_url)
                                    <a href="{{ $shipment->tracking_url }}" target="_blank" class="font-weight-bold">{{ $shipment->tracking_number }}</a>
                                @else
                                    {{ $shipment->tracking_number ?? '-' }}
                                @endif
                            </dd>
                            <dt class="col-5">ETA</dt><dd class="col-7 text-primary">{{ $shipment->estimated_delivery?->format('d M Y') ?? '-' }}</dd>
                            <dt class="col-5">Shipped At</dt><dd class="col-7">{{ $shipment->shipped_at?->format('d M Y H:i') ?? '-' }}</dd>
                            <dt class="col-5">Status</dt>
                            <dd class="col-7">
                                @php $cls = match($shipment->status) { 
                                    'delivered' => 'bg-green-lt text-green',
                                    'returned' => 'bg-red-lt text-red',
                                    'in_transit' => 'bg-blue-lt text-blue',
                                    'dispatched' => 'bg-cyan-lt text-cyan',
                                    default => 'bg-secondary-lt text-secondary' 
                                }; @endphp
                                <span class="badge {{ $cls }}">{{ ucfirst(str_replace('_',' ',$shipment->status)) }}</span>
                            </dd>
                        </dl>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Shipping Address</h3>
                    </div>
                    <div class="card-body">
                        @php $addr = $shipment->order->party->addresses->where('type', 'shipping')->first() ?? $shipment->order->party->addresses->first(); @endphp
                        @if($addr)
                            <address class="mb-0">
                                <div class="font-weight-bold mb-1">{{ $shipment->order->party->name }}</div>
                                {{ $addr->address_line1 }}<br>
                                @if($addr->address_line2) {{ $addr->address_line2 }}<br> @endif
                                <div class="small text-secondary mt-1">
                                    @if($addr->village) <div><strong>Village:</strong> {{ $addr->village }}</div> @endif
                                    @if($addr->taluka) <div><strong>Taluka:</strong> {{ $addr->taluka }}</div> @endif
                                    @if($addr->post_office) <div><strong>Post Office:</strong> {{ $addr->post_office }}</div> @endif
                                    @if($addr->district) <div><strong>District:</strong> {{ $addr->district }}</div> @endif
                                </div>
                                <div class="mt-1">
                                    {{ $addr->district ?? '-' }}, {{ $addr->state }} - {{ $addr->pincode }}
                                </div>
                                <div class="mt-1">Phone: {{ $shipment->order->party->mobile }}</div>
                            </address>
                        @else
                            <p class="text-muted mb-0 small">No shipping address found</p>
                        @endif
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header"><h3 class="card-title">Packages ({{ $shipment->order->packages->count() }})</h3></div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-sm">
                            <thead><tr><th>Pkg #</th><th>Weight</th><th>Dim.</th></tr></thead>
                            <tbody>
                                @foreach($shipment->order->packages as $pkg)
                                <tr>
                                    <td><span class="text-secondary small">{{ $pkg->package_number }}</span></td>
                                    <td>{{ $pkg->weight }} kg</td>
                                    <td>{{ $pkg->dimensions }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h3 class="card-title">Shipment Items</h3></div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-sm">
                            <thead><tr><th>Product</th><th>Qty</th></tr></thead>
                            <tbody>
                                @foreach($shipment->order->items as $item)
                                <tr><td>{{ $item->product->name }}</td><td>{{ (int)$item->quantity }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Tracking Timeline</h3></div>
                    <div class="card-body">
                        @if($shipment->trackingEvents->isEmpty())
                        <p class="text-muted text-center py-3">No tracking events yet</p>
                        @else
                        <ul class="timeline">
                            @foreach($shipment->trackingEvents as $event)
                            <li class="timeline-event">
                                <div class="timeline-event-icon bg-{{ $event->status === 'delivered' ? 'green' : 'blue' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/></svg>
                                </div>
                                <div class="card timeline-event-card">
                                    <div class="card-body">
                                        <div class="text-muted float-end small">{{ $event->event_at->format('d M Y H:i') }}</div>
                                        <h4 class="mb-1">{{ ucfirst(str_replace('_',' ',$event->status)) }}</h4>
                                        @if($event->location)<p class="text-muted mb-1"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a7 7 0 0 1 7 7c0 5.25-7 13-7 13S5 14.25 5 9a7 7 0 0 1 7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>{{ $event->location }}</p>@endif
                                        @if($event->description)<p class="text-muted mb-0 small">{{ $event->description }}</p>@endif
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Return Modal --}}
        <div class="modal modal-blur fade" id="returnModal" tabindex="-1">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <form method="POST" action="{{ route('erp.shipments.return', $shipment) }}">
                    @csrf @method('PATCH')
                    <div class="modal-content shadow-lg border-0">
                        <div class="modal-header">
                            <h5 class="modal-title text-danger">Mark as Returned (RTO)</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body py-4">
                            <div class="text-center mb-3">
                                <div class="avatar avatar-md bg-danger-lt text-danger mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1" /></svg>
                                </div>
                                <p class="text-muted small">This will restock all items into inventory and mark the order as returned.</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Reason for Return</label>
                                <textarea name="reason" class="form-control" rows="2" placeholder="e.g. Customer not available, RTO"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Confirm Return</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Add Event Modal --}}
        <div class="modal modal-blur fade" id="addEventModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form method="POST" action="{{ route('erp.shipments.add-event', $shipment) }}">
                    @csrf
                    <div class="modal-content shadow-sm border-0">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Tracking Event</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label required">Status</label>
                                <select name="status" class="form-select" required>
                                    @foreach(['in_transit','out_for_delivery','delivered','exception','returned'] as $s)
                                    <option value="{{ $s }}">{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-control" placeholder="e.g. Mumbai Hub">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description/Note</label>
                                <textarea name="description" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Event</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
function copyTrackingUrl() {
    const url = "{{ $shipment->tracking_url ?? route('erp.shipments.show', $shipment) }}";
    navigator.clipboard.writeText(url).then(() => {
        alert('Tracking link copied to clipboard!');
    });
}
</script>
@endpush
@endsection
