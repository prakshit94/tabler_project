@extends('layouts.tabler')
@section('title', 'Shipments')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Shipment Tracking</h2>
                <p class="page-subtitle">All outbound shipments</p>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        @if(session('success'))<div class="alert alert-success alert-dismissible mb-3">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif

        {{-- Filters --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search tracking number..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            @foreach(['dispatched','in_transit','out_for_delivery','delivered','returned','failed'] as $s)
                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('erp.shipments.index') }}" class="btn btn-ghost">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Shipment #</th><th>Order</th><th>Party</th><th>Carrier</th><th>Tracking #</th><th>Latest Event</th><th>Status</th><th>ETA</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments as $s)
                        <tr>
                            <td><a href="{{ route('erp.shipments.show', $s) }}" class="fw-medium text-decoration-none">{{ $s->shipment_number }}</a></td>
                            <td>{{ $s->order->order_number ?? '-' }}</td>
                            <td>{{ $s->order->party->name ?? '-' }}</td>
                            <td>{{ $s->carrier ?? '-' }}</td>
                            <td>
                                @if($s->tracking_url)
                                    <a href="{{ $s->tracking_url }}" target="_blank" class="text-decoration-none">{{ $s->tracking_number }}</a>
                                @else
                                    {{ $s->tracking_number ?? '-' }}
                                @endif
                            </td>
                            <td class="text-muted small">{{ $s->latestEvent?->description ?? '—' }}</td>
                            <td>
                                @php $cls = match($s->status) { 'delivered'=>'bg-green','in_transit'=>'bg-blue','dispatched'=>'bg-cyan','out_for_delivery'=>'bg-lime','failed'=>'bg-red',default=>'bg-secondary' }; @endphp
                                <span class="badge {{ $cls }}">{{ ucfirst(str_replace('_',' ',$s->status)) }}</span>
                            </td>
                            <td class="text-muted">{{ $s->estimated_delivery?->format('d M Y') ?? '-' }}</td>
                            <td>
                                <a href="{{ route('erp.shipments.show', $s) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">No shipments found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $shipments->links() }}</div>
        </div>
    </div>
</div>
@endsection
