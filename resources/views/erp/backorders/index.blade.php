@extends('layouts.tabler')
@section('title', 'Backorders')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Backorder Management</h2>
                <p class="page-subtitle">Track and fulfill pending backorders</p>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        @if(session('success'))<div class="alert alert-success alert-dismissible mb-3">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif

        {{-- Status Tabs --}}
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    @foreach(['pending'=>'Pending','waiting_stock'=>'Waiting Stock','allocated'=>'Allocated','fulfilled'=>'Fulfilled','cancelled'=>'Cancelled'] as $s => $label)
                    <li class="nav-item">
                        <a href="{{ route('erp.backorders.index', ['status' => $s]) }}"
                           class="nav-link {{ $status === $s ? 'active' : '' }}">{{ $label }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr><th>Backorder #</th><th>Order</th><th>Party</th><th>Product</th><th>Warehouse</th><th>Pending</th><th>Fulfilled</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($backorders as $bo)
                        <tr>
                            <td class="fw-medium">{{ $bo->backorder_number }}</td>
                            <td><a href="{{ route('erp.orders.show', $bo->order_id) }}">{{ $bo->order->order_number }}</a></td>
                            <td>{{ $bo->order->party->name ?? '-' }}</td>
                            <td>{{ $bo->product->name }}</td>
                            <td>{{ $bo->warehouse->name ?? '-' }}</td>
                            <td><span class="badge bg-red">{{ $bo->pending_qty }}</span></td>
                            <td><span class="badge bg-green">{{ $bo->fulfilled_qty }}</span></td>
                            <td>
                                @php $cls = match($bo->status) { 'fulfilled'=>'bg-green','waiting_stock'=>'bg-yellow','pending'=>'bg-red','cancelled'=>'bg-secondary',default=>'bg-secondary' }; @endphp
                                <span class="badge {{ $cls }}">{{ ucfirst(str_replace('_',' ',$bo->status)) }}</span>
                            </td>
                            <td class="d-flex gap-1">
                                @if(in_array($bo->status, ['pending','waiting_stock']))
                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#fulfillModal{{ $bo->id }}">Fulfill</button>
                                <form method="POST" action="{{ route('erp.backorders.cancel', $bo) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel backorder?')">Cancel</button>
                                </form>
                                @endif
                                <a href="{{ route('erp.backorders.show', $bo) }}" class="btn btn-sm btn-outline-secondary">View</a>
                            </td>
                        </tr>
                        {{-- Fulfill Modal --}}
                        <div class="modal fade" id="fulfillModal{{ $bo->id }}" tabindex="-1">
                            <div class="modal-dialog modal-sm">
                                <form method="POST" action="{{ route('erp.backorders.fulfill', $bo) }}">
                                    @csrf @method('PATCH')
                                    <div class="modal-content">
                                        <div class="modal-header"><h5 class="modal-title">Fulfill Backorder</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                        <div class="modal-body">
                                            <p class="text-muted mb-2">{{ $bo->product->name }}</p>
                                            <label class="form-label">Fulfilled Qty (remaining: {{ $bo->pending_qty - $bo->fulfilled_qty }})</label>
                                            <input type="number" name="fulfilled_qty" class="form-control" min="0.01" max="{{ $bo->pending_qty - $bo->fulfilled_qty }}" step="0.01" required>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success w-100">Confirm</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">No backorders found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $backorders->links() }}</div>
        </div>
    </div>
</div>
@endsection
