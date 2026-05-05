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
            <div class="card card-sm border-0 shadow-sm overflow-hidden">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <span class="bg-yellow-lt text-yellow avatar avatar-md shadow-sm border border-yellow-subtle">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg>
                        </span>
                        <div class="ms-3">
                            <div class="subheader mb-1">Pick Lists</div>
                            <div class="h2 mb-0 fw-bold">
                                {{ ($stats['pending_pick'] ?? 0) + ($stats['in_progress_pick'] ?? 0) }}
                            </div>
                            <div class="text-muted small">
                                {{ $stats['pending_pick'] ?? 0 }} pending
                            </div>
                        </div>
                    </div>
                </div>
                <div class="progress progress-xs">
                    <div class="progress-bar bg-yellow" style="width: 100%"></div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm border-0 shadow-sm overflow-hidden">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <span class="bg-cyan-lt text-cyan avatar avatar-md shadow-sm border border-cyan-subtle">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3l8 4.5v9L12 21l-8-4.5v-9L12 3z"/><line x1="12" y1="12" x2="20" y2="7.5"/><line x1="12" y1="12" x2="12" y2="21"/><line x1="12" y1="12" x2="4" y2="7.5"/></svg>
                        </span>
                        <div class="ms-3">
                            <div class="subheader mb-1">Packing Queue</div>
                            <div class="h2 mb-0 fw-bold text-cyan">
                                {{ $stats['pending_pack'] ?? 0 }}
                            </div>
                            <div class="text-muted small">Ready to pack</div>
                        </div>
                    </div>
                </div>
                <div class="progress progress-xs">
                    <div class="progress-bar bg-cyan" style="width: 100%"></div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm border-0 shadow-sm overflow-hidden">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <span class="bg-blue-lt text-blue avatar avatar-md shadow-sm border border-blue-subtle">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </span>
                        <div class="ms-3">
                            <div class="subheader mb-1">In Transit</div>
                            <div class="h2 mb-0 fw-bold text-blue">
                                {{ $stats['in_transit'] ?? 0 }}
                            </div>
                            <div class="text-muted small">Active shipments</div>
                        </div>
                    </div>
                </div>
                <div class="progress progress-xs">
                    <div class="progress-bar bg-blue" style="width: 100%"></div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm border-0 shadow-sm overflow-hidden">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <span class="bg-red-lt text-red avatar avatar-md shadow-sm border border-red-subtle">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4"/><path d="M12 17h.01"/><path d="M3.93 4.93L3 12l9 9 9-9-.93-7.07A2 2 0 0 0 18.07 3H5.93A2 2 0 0 0 3.93 4.93z"/></svg>
                        </span>
                        <div class="ms-3">
                            <div class="subheader mb-1">Inventory Alerts</div>
                            <div class="h2 mb-0 fw-bold text-red">
                                {{ ($stats['backorders'] ?? 0) + ($stats['low_stock'] ?? 0) }}
                            </div>
                            <div class="text-muted small">
                                {{ $stats['low_stock'] ?? 0 }} items low stock
                            </div>
                        </div>
                    </div>
                </div>
                <div class="progress progress-xs">
                    <div class="progress-bar bg-red" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">

        {{-- Recent Pick Lists --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Pick Lists</h3>
                    <div class="card-options">
                        <a href="{{ route('erp.wms.pick-lists') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr><th>Pick List</th><th>Order</th><th>Warehouse</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @forelse($recentPickLists as $pl)
                            <tr>
                                <td>
                                    <a href="{{ route('erp.wms.pick-list.show', $pl) }}" class="text-decoration-none fw-medium">
                                        {{ $pl->pick_list_number }}
                                    </a>
                                </td>
                                <td>{{ optional($pl->order)->order_number ?? '-' }}</td>
                                <td>{{ optional($pl->warehouse)->name ?? '-' }}</td>
                                <td>
                                    @php
                                        $cls = match($pl->status) {
                                            'completed'=>'bg-green',
                                            'in_progress'=>'bg-blue',
                                            'pending'=>'bg-yellow',
                                            default=>'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $cls }}">
                                        {{ ucfirst(str_replace('_',' ',$pl->status)) }}
                                    </span>
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
                    <h3 class="card-title">Active Shipments</h3>
                    <div class="card-options">
                        <a href="{{ route('erp.shipments.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr><th>Shipment</th><th>Party</th><th>Carrier</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @forelse($recentShipments as $s)
                            <tr>
                                <td>
                                    <a href="{{ route('erp.shipments.show', $s) }}" class="fw-medium text-decoration-none">
                                        {{ $s->shipment_number }}
                                    </a>
                                </td>
                                <td>{{ optional(optional($s->order)->party)->name ?? '-' }}</td>
                                <td>{{ $s->carrier ?? '-' }}</td>
                                <td>
                                    @php
                                        $cls = match($s->status) {
                                            'delivered'=>'bg-green',
                                            'in_transit'=>'bg-blue',
                                            'dispatched'=>'bg-cyan',
                                            default=>'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $cls }}">
                                        {{ ucfirst(str_replace('_',' ',$s->status)) }}
                                    </span>
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
                        Pending Backorders ({{ $pendingBackorders->count() }})
                    </h3>
                    <div class="card-options">
                        <a href="{{ route('erp.backorders.index') }}" class="btn btn-sm btn-outline-danger">View All</a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr><th>Backorder #</th><th>Order</th><th>Product</th><th>Pending Qty</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            @foreach($pendingBackorders as $bo)
                            <tr>
                                <td class="fw-medium">{{ $bo->backorder_number }}</td>
                                <td>{{ optional($bo->order)->order_number ?? '-' }}</td>
                                <td>{{ optional($bo->product)->name ?? '-' }}</td>
                                <td><span class="badge bg-red">{{ $bo->pending_qty }}</span></td>
                                <td>
                                    <a href="{{ route('erp.backorders.show', $bo) }}" class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                </td>
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
