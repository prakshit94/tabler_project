@extends('layouts.tabler')

@section('title', 'WMS Dashboard')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-primary" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l0 -4"/><path d="M3 13l0 -4"/><path d="M3 5l0 0" /><path d="M7 5l10 0"/><path d="M7 13l10 0"/><path d="M7 21l10 0"/><path d="M17 5l0 0"/><path d="M17 13l0 0"/><path d="M17 21l0 0"/></svg>
                    Warehouse Management System
                </h2>
                <p class="page-subtitle">Real-time operations center</p>
            </div>
            <div class="col-auto">
                <span class="badge bg-green text-green-fg">
                    <span class="status-dot status-dot-animated bg-green d-inline-block me-1"></span>
                    Live
                </span>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        {{-- Stats Row --}}
        <div class="row row-cards mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm border-start border-warning border-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-yellow text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="12" rx="1"/><line x1="3" y1="20" x2="21" y2="20"/></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Pending Pick Lists</div>
                                <div class="text-secondary">{{ $stats['pending_pick'] }} pending · {{ $stats['in_progress_pick'] }} in progress</div>
                            </div>
                            <div class="col-auto">
                                <div class="h1 mb-0 text-warning fw-bold">{{ $stats['pending_pick'] + $stats['in_progress_pick'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm border-start border-info border-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-cyan text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3l8 4.5v9L12 21l-8-4.5v-9L12 3z"/><line x1="12" y1="12" x2="20" y2="7.5"/><line x1="12" y1="12" x2="12" y2="21"/><line x1="12" y1="12" x2="4" y2="7.5"/></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Packing Queue</div>
                                <div class="text-secondary">Orders awaiting packing</div>
                            </div>
                            <div class="col-auto">
                                <div class="h1 mb-0 text-cyan fw-bold">{{ $stats['pending_pack'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm border-start border-primary border-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-blue text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">In Transit</div>
                                <div class="text-secondary">Active shipments</div>
                            </div>
                            <div class="col-auto">
                                <div class="h1 mb-0 text-blue fw-bold">{{ $stats['in_transit'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm border-start border-danger border-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-red text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4"/><path d="M12 17h.01"/><path d="M3.93 4.93L3 12l9 9 9-9-.93-7.07A2 2 0 0 0 18.07 3H5.93A2 2 0 0 0 3.93 4.93z"/></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Alerts</div>
                                <div class="text-secondary">{{ $stats['backorders'] }} backorders · {{ $stats['low_stock'] }} low stock</div>
                            </div>
                            <div class="col-auto">
                                <div class="h1 mb-0 text-red fw-bold">{{ $stats['backorders'] + $stats['low_stock'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-cards">
            {{-- Recent Pick Lists --}}
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1 text-warning" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg>
                            Recent Pick Lists
                        </h3>
                        <div class="card-options">
                            <a href="{{ route('erp.wms.pick-lists') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead><tr><th>Pick List</th><th>Order</th><th>Warehouse</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse($recentPickLists as $pl)
                                <tr>
                                    <td><a href="{{ route('erp.wms.pick-list.show', $pl) }}" class="text-decoration-none fw-medium">{{ $pl->pick_list_number }}</a></td>
                                    <td><span class="text-muted">{{ $pl->order->order_number }}</span></td>
                                    <td>{{ $pl->warehouse->name ?? '-' }}</td>
                                    <td>
                                        @php
                                            $cls = match($pl->status) {
                                                'completed'   => 'bg-green',
                                                'in_progress' => 'bg-blue',
                                                'pending'     => 'bg-yellow',
                                                default       => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $cls }}">{{ ucfirst(str_replace('_',' ',$pl->status)) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">No pick lists yet</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Recent Shipments --}}
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1 text-blue" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                            Active Shipments
                        </h3>
                        <div class="card-options">
                            <a href="{{ route('erp.shipments.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead><tr><th>Shipment</th><th>Party</th><th>Carrier</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse($recentShipments as $s)
                                <tr>
                                    <td><a href="{{ route('erp.shipments.show', $s) }}" class="text-decoration-none fw-medium">{{ $s->shipment_number }}</a></td>
                                    <td>{{ $s->order->party->name ?? '-' }}</td>
                                    <td>{{ $s->carrier ?? '-' }}</td>
                                    <td>
                                        @php
                                            $cls = match($s->status) {
                                                'delivered'   => 'bg-green',
                                                'in_transit'  => 'bg-blue',
                                                'dispatched'  => 'bg-cyan',
                                                default       => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $cls }}">{{ ucfirst(str_replace('_',' ',$s->status)) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">No active shipments</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Pending Backorders --}}
            @if($pendingBackorders->isNotEmpty())
            <div class="col-12">
                <div class="card border-danger">
                    <div class="card-header bg-danger-lt">
                        <h3 class="card-title text-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4"/><path d="M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg>
                            Pending Backorders ({{ $pendingBackorders->count() }})
                        </h3>
                        <div class="card-options">
                            <a href="{{ route('erp.backorders.index') }}" class="btn btn-sm btn-outline-danger">View All</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead><tr><th>Backorder #</th><th>Order</th><th>Product</th><th>Pending Qty</th><th>Actions</th></tr></thead>
                            <tbody>
                                @foreach($pendingBackorders as $bo)
                                <tr>
                                    <td class="fw-medium">{{ $bo->backorder_number }}</td>
                                    <td>{{ $bo->order->order_number }}</td>
                                    <td>{{ $bo->product->name }}</td>
                                    <td><span class="badge bg-red">{{ $bo->pending_qty }}</span></td>
                                    <td><a href="{{ route('erp.backorders.show', $bo) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
