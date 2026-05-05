@extends('layouts.tabler')

@section('title', 'Shipment — ' . ($shipment->shipment_number ?? '-'))

@section('content')

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('erp.shipments.index') }}">Shipments</a></li>
                    <li class="breadcrumb-item active">{{ $shipment->shipment_number ?? '-' }}</li>
                </ol>
                <h2 class="page-title">{{ $shipment->shipment_number ?? '-' }}</h2>
            </div>
        <div class="col-auto d-flex gap-2">

            <button class="btn btn-outline-secondary d-none d-md-inline-block" onclick="copyTrackingUrl()">
                Copy Link
            </button>

            <button class="btn btn-outline-secondary d-none d-md-inline-block" onclick="window.print()">
                Label
            </button>

            @if(!in_array($shipment->status, ['delivered', 'returned']))
                <button class="btn btn-primary d-none d-md-inline-block"
                        data-bs-toggle="modal"
                        data-bs-target="#addEventModal">
                    Update Status
                </button>

                <div class="dropdown">
                    <button class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                        Complete
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form method="POST" action="{{ route('erp.shipments.deliver', $shipment) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="dropdown-item text-success">
                                    Mark Delivered
                                </button>
                            </form>
                        </li>

                        <li><hr class="dropdown-divider"></li>

                        <li>
                            <button type="button"
                                    class="dropdown-item text-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#returnModal">
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

@if(session('success'))

<div class="alert alert-success alert-dismissible mb-3">
    {{ session('success') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row row-cards">

{{-- LEFT SIDE --}}

<div class="col-lg-5">

<div class="card mb-3">
<div class="card-header"><h3 class="card-title">Shipment Info</h3></div>
<div class="card-body">
<dl class="row mb-0">

<dt class="col-5">Order</dt>
<dd class="col-7">
<a href="{{ route('erp.orders.show', $shipment->order_id) }}">
{{ optional($shipment->order)->order_number ?? '-' }}
</a>
</dd>

<dt class="col-5">Party</dt>
<dd class="col-7">
{{ optional(optional($shipment->order)->party)->name ?? '-' }}
</dd>

<dt class="col-5">Carrier</dt>
<dd class="col-7">{{ $shipment->carrier ?? '-' }}</dd>

<dt class="col-5">Tracking #</dt>
<dd class="col-7">
@if($shipment->tracking_url)
<a href="{{ $shipment->tracking_url }}" target="_blank" class="fw-bold">
{{ $shipment->tracking_number ?? '-' }}
</a>
@else
{{ $shipment->tracking_number ?? '-' }}
@endif
</dd>

<dt class="col-5">ETA</dt>
<dd class="col-7 text-primary">
{{ optional($shipment->estimated_delivery)->format('d M Y') ?? '-' }}
</dd>

<dt class="col-5">Shipped At</dt>
<dd class="col-7">
{{ optional($shipment->shipped_at)->format('d M Y H:i') ?? '-' }}
</dd>

<dt class="col-5">Status</dt>
<dd class="col-7">
@php
$cls = match($shipment->status) {
'delivered' => 'bg-green-lt text-green',
'returned' => 'bg-red-lt text-red',
'in_transit' => 'bg-blue-lt text-blue',
'dispatched' => 'bg-cyan-lt text-cyan',
default => 'bg-secondary-lt text-secondary'
};
@endphp

<span class="badge {{ $cls }}">
{{ ucfirst(str_replace('_',' ',$shipment->status)) }}
</span>
</dd>

</dl>
</div>
</div>

{{-- ADDRESS --}}

<div class="card mb-3">
<div class="card-header"><h3 class="card-title">Shipping Address</h3></div>
<div class="card-body">

@php
$party = optional($shipment->order)->party;
$addr = $party?->addresses?->where('type','shipping')->first()
?? $party?->addresses?->first();
@endphp

@if($addr)

<address class="mb-0">
<div class="fw-bold mb-1">{{ $party->name ?? '-' }}</div>

{{ $addr->address_line1 ?? '' }}<br>
@if($addr->address_line2) {{ $addr->address_line2 }}<br> @endif

<div class="small text-secondary mt-1">
@if($addr->village) <div><strong>Village:</strong> {{ $addr->village }}</div> @endif
@if($addr->taluka) <div><strong>Taluka:</strong> {{ $addr->taluka }}</div> @endif
@if($addr->post_office) <div><strong>Post Office:</strong> {{ $addr->post_office }}</div> @endif
@if($addr->district) <div><strong>District:</strong> {{ $addr->district }}</div> @endif
</div>

<div class="mt-1">
{{ $addr->district ?? '-' }},
{{ $addr->state ?? '-' }} - {{ $addr->pincode ?? '-' }}
</div>

<div class="mt-1">
Phone: {{ $party->mobile ?? '-' }}
</div>
</address>
@else
<p class="text-muted small mb-0">No shipping address found</p>
@endif

</div>
</div>

{{-- PACKAGES --}}

<div class="card mb-3">
<div class="card-header">
<h3 class="card-title">
Packages ({{ optional($shipment->order)->packages?->count() ?? 0 }})
</h3>
</div>

<div class="table-responsive">
<table class="table table-sm">
<thead>
<tr><th>Pkg #</th><th>Weight</th><th>Dim.</th></tr>
</thead>
<tbody>

@foreach(optional($shipment->order)->packages ?? [] as $pkg)

<tr>
<td>{{ $pkg->package_number }}</td>
<td>{{ $pkg->weight ?? '-' }} kg</td>
<td>{{ $pkg->dimensions ?? '-' }}</td>
</tr>
@endforeach

</tbody>
</table>
</div>
</div>

{{-- ITEMS --}}

<div class="card">
<div class="card-header"><h3 class="card-title">Shipment Items</h3></div>

<div class="table-responsive">
<table class="table table-sm">
<thead><tr><th>Product</th><th>Qty</th></tr></thead>
<tbody>

@foreach(optional($shipment->order)->items ?? [] as $item)

<tr>
<td>{{ optional($item->product)->name ?? '-' }}</td>
<td>{{ (int)$item->quantity }}</td>
</tr>
@endforeach

</tbody>
</table>
</div>
</div>

</div>

{{-- RIGHT SIDE --}}

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

<div class="timeline-event-icon bg-{{ $event->status === 'delivered' ? 'green' : 'blue' }}"></div>

<div class="card timeline-event-card">
<div class="card-body">

<div class="text-muted float-end small">
{{ optional($event->event_at)->format('d M Y H:i') }}
</div>

<h4 class="mb-1">
{{ ucfirst(str_replace('_',' ',$event->status)) }}
</h4>

@if($event->location)

<p class="text-muted mb-1">{{ $event->location }}</p>
@endif

@if($event->description)

<p class="text-muted mb-0 small">{{ $event->description }}</p>
@endif

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
</div>
</div>

{{-- MODALS --}}
@if(!in_array($shipment->status, ['delivered', 'returned']))
<div class="modal modal-blur fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('erp.shipments.add-event', $shipment) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Tracking Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="dispatched">Dispatched</option>
                            <option value="in_transit" selected>In Transit</option>
                            <option value="out_for_delivery">Out for Delivery</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Location</label>
                        <input type="text" name="location" class="form-control" placeholder="e.g. Sorting Hub, Mumbai" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="e.g. Package arrived at facility" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Event</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal modal-blur fade" id="returnModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form method="POST" action="{{ route('erp.shipments.return', $shipment) }}">
            @csrf
            @method('PATCH')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">Mark as Returned</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" /></svg>
                    <h3>Are you sure?</h3>
                    <div class="text-muted">Do you really want to mark this shipment as Returned? This action cannot be undone.</div>
                    <div class="mt-3 text-start">
                        <label class="form-label">Return Reason / Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Customer refused delivery"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn btn-link link-secondary w-100" data-bs-dismiss="modal">Cancel</button>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-danger w-100">Confirm Return</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

{{-- SCRIPT --}}
@push('scripts')

<script>
function copyTrackingUrl() {
    const url = "{{ $shipment->tracking_url ?? route('erp.shipments.show', $shipment) }}";

    if (navigator.clipboard) {
        navigator.clipboard.writeText(url);
        alert('Tracking link copied!');
    } else {
        prompt("Copy this link:", url);
    }
}
</script>

@endpush

@endsection
