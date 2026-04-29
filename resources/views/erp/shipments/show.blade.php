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
                @if($shipment->status !== 'delivered')
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">Add Tracking Event</button>
                <form method="POST" action="{{ route('erp.shipments.deliver', $shipment) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-success" onclick="return confirm('Mark as delivered?')">Mark Delivered</button>
                </form>
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
                                    <a href="{{ $shipment->tracking_url }}" target="_blank">{{ $shipment->tracking_number }}</a>
                                @else
                                    {{ $shipment->tracking_number ?? '-' }}
                                @endif
                            </dd>
                            <dt class="col-5">ETA</dt><dd class="col-7">{{ $shipment->estimated_delivery?->format('d M Y') ?? '-' }}</dd>
                            <dt class="col-5">Shipped At</dt><dd class="col-7">{{ $shipment->shipped_at?->format('d M Y H:i') ?? '-' }}</dd>
                            <dt class="col-5">Delivered At</dt><dd class="col-7">{{ $shipment->delivered_at?->format('d M Y H:i') ?? '-' }}</dd>
                            <dt class="col-5">Status</dt>
                            <dd class="col-7">
                                @php $cls = match($shipment->status) { 'delivered'=>'bg-green','in_transit'=>'bg-blue','dispatched'=>'bg-cyan',default=>'bg-secondary' }; @endphp
                                <span class="badge {{ $cls }}">{{ ucfirst(str_replace('_',' ',$shipment->status)) }}</span>
                            </dd>
                        </dl>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h3 class="card-title">Order Items</h3></div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-sm">
                            <thead><tr><th>Product</th><th>Qty</th></tr></thead>
                            <tbody>
                                @foreach($shipment->order->items as $item)
                                <tr><td>{{ $item->product->name }}</td><td>{{ $item->quantity }}</td></tr>
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

        {{-- Add Event Modal --}}
        <div class="modal fade" id="addEventModal" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('erp.shipments.add-event', $shipment) }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header"><h5 class="modal-title">Add Tracking Event</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
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
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Event</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
