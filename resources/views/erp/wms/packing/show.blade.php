@extends('layouts.tabler')
@section('title', 'Packing — ' . $order->order_number)
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('erp.wms.dashboard') }}">WMS</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('erp.wms.packing.index') }}">Packing</a></li>
                    <li class="breadcrumb-item active">{{ $order->order_number }}</li>
                </ol>
                <h2 class="page-title">Pack Order: {{ $order->order_number }}</h2>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newPackageModal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                    New Package
                </button>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        @if(session('success'))<div class="alert alert-success alert-dismissible mb-3">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif

        <div class="row row-cards">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Order Items</h3></div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-sm">
                            <thead><tr><th>Product</th><th>Qty</th></tr></thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <div class="progress mb-1" style="height:8px;">
                            @php $pct = $summary['total_items'] > 0 ? round(($summary['packed_items']/$summary['total_items'])*100) : 0; @endphp
                            <div class="progress-bar bg-{{ $pct >= 100 ? 'green' : 'blue' }}" style="width:{{ $pct }}%"></div>
                        </div>
                        <small class="text-muted">{{ $summary['packed_items'] }} / {{ $summary['total_items'] }} packed ({{ $pct }}%)</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Packages ({{ $summary['packages']->count() }})</h3></div>
                    @forelse($summary['packages'] as $pkg)
                    <div class="card-body border-bottom">
                        <div class="d-flex align-items-center mb-2">
                            <h4 class="mb-0 me-2">{{ $pkg->package_number }}</h4>
                            @php $psc = match($pkg->status) { 'packed'=>'bg-green','packing'=>'bg-blue',default=>'bg-secondary' }; @endphp
                            <span class="badge {{ $psc }}">{{ ucfirst($pkg->status) }}</span>
                            <div class="ms-auto text-muted small">
                                @if($pkg->weight) {{ $pkg->weight }}kg @endif
                                @if($pkg->dimensions) · {{ $pkg->dimensions }} @endif
                            </div>
                        </div>
                        @if($pkg->items->isNotEmpty())
                        <table class="table table-sm mb-2">
                            <thead><tr><th>Product</th><th>Qty</th></tr></thead>
                            <tbody>
                                @foreach($pkg->items as $pi)
                                <tr><td>{{ $pi->product->name }}</td><td>{{ $pi->quantity }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p class="text-muted small mb-2">No items added yet</p>
                        @endif
                        @if($pkg->status === 'packing')
                        <form method="POST" action="{{ route('erp.wms.package.seal', $pkg) }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm btn-success">Seal Package</button>
                        </form>
                        @endif
                    </div>
                    @empty
                    <div class="card-body text-center text-muted py-4">No packages created yet. Click "New Package" to start.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- New Package Modal --}}
        <div class="modal fade" id="newPackageModal" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('erp.wms.package.create', $order) }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header"><h5 class="modal-title">Create New Package</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Weight (kg)</label>
                                <input type="number" name="weight" class="form-control" step="0.001" min="0" placeholder="0.000">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Dimensions (LxWxH cm)</label>
                                <input type="text" name="dimensions" class="form-control" placeholder="e.g. 30x20x15">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Create Package</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
