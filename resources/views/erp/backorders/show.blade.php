@extends('layouts.tabler')
@section('title', 'Backorder — ' . $backorder->backorder_number)
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('erp.backorders.index') }}">Backorders</a></li>
                    <li class="breadcrumb-item active">{{ $backorder->backorder_number }}</li>
                </ol>
                <h2 class="page-title">{{ $backorder->backorder_number }}</h2>
            </div>
            <div class="col-auto d-flex gap-2">
                @if(in_array($backorder->status, ['pending','waiting_stock']))
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#fulfillModal">Fulfill</button>
                <form method="POST" action="{{ route('erp.backorders.cancel', $backorder) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-outline-danger" onclick="return confirm('Cancel this backorder?')">Cancel</button>
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
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Backorder Info</h3></div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-5">Order</dt>
                            <dd class="col-7"><a href="{{ route('erp.orders.show', $backorder->order_id) }}">{{ $backorder->order->order_number }}</a></dd>
                            <dt class="col-5">Party</dt>
                            <dd class="col-7">{{ $backorder->order->party->name ?? '-' }}</dd>
                            <dt class="col-5">Product</dt>
                            <dd class="col-7 fw-medium">{{ $backorder->product->name }}</dd>
                            <dt class="col-5">Warehouse</dt>
                            <dd class="col-7">{{ $backorder->warehouse->name ?? '-' }}</dd>
                            <dt class="col-5">Pending Qty</dt>
                            <dd class="col-7"><span class="badge bg-red fs-5">{{ $backorder->pending_qty }}</span></dd>
                            <dt class="col-5">Fulfilled</dt>
                            <dd class="col-7"><span class="badge bg-green fs-5">{{ $backorder->fulfilled_qty }}</span></dd>
                            <dt class="col-5">Remaining</dt>
                            <dd class="col-7"><span class="badge bg-yellow fs-5">{{ $backorder->remaining_qty }}</span></dd>
                            <dt class="col-5">Status</dt>
                            <dd class="col-7">
                                @php $cls = match($backorder->status) {'fulfilled'=>'bg-green','waiting_stock'=>'bg-yellow','pending'=>'bg-red','cancelled'=>'bg-secondary',default=>'bg-secondary'}; @endphp
                                <span class="badge {{ $cls }}">{{ ucfirst(str_replace('_',' ',$backorder->status)) }}</span>
                            </dd>
                        </dl>
                    </div>
                </div>

                @if($stockLevel)
                <div class="card mt-3">
                    <div class="card-header"><h3 class="card-title">Current Stock Level</h3></div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-3"><div class="h3 text-blue mb-0">{{ $stockLevel->quantity }}</div><small class="text-muted">On Hand</small></div>
                            <div class="col-3"><div class="h3 text-yellow mb-0">{{ $stockLevel->reserved_qty }}</div><small class="text-muted">Reserved</small></div>
                            <div class="col-3"><div class="h3 text-orange mb-0">{{ $stockLevel->committed_qty }}</div><small class="text-muted">Committed</small></div>
                            <div class="col-3"><div class="h3 text-green mb-0">{{ max(0, $stockLevel->quantity - $stockLevel->reserved_qty - $stockLevel->committed_qty) }}</div><small class="text-muted">Available</small></div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Fulfill Modal --}}
        <div class="modal fade" id="fulfillModal" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <form method="POST" action="{{ route('erp.backorders.fulfill', $backorder) }}">
                    @csrf @method('PATCH')
                    <div class="modal-content">
                        <div class="modal-header"><h5 class="modal-title">Fulfill Backorder</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body">
                            <label class="form-label">Fulfilled Qty (remaining: {{ $backorder->remaining_qty }})</label>
                            <input type="number" name="fulfilled_qty" class="form-control" min="0.01" max="{{ $backorder->remaining_qty }}" step="0.01" required>
                        </div>
                        <div class="modal-footer"><button type="submit" class="btn btn-success w-100">Confirm</button></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
