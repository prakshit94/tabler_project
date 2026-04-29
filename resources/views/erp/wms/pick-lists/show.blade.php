@extends('layouts.tabler')

@section('title', 'Picking Task — ' . $pickList->pick_list_number)

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Warehouse Operations</div>
                <h2 class="page-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11l3 3l8 -8" /><path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9" /></svg>
                    Task: {{ $pickList->pick_list_number }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('erp.wms.pick-lists') }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1" /></svg>
                        Back to List
                    </a>
                    @if($pickList->status === 'pending')
                    <form method="POST" action="{{ route('erp.wms.pick-list.start', $pickList) }}">
                        @csrf
                        <button class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 4v16l13 -8z" /></svg>
                            Start Picking
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-important alert-success alert-dismissible" role="alert">
                <div class="d-flex">
                    <div><svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg></div>
                    <div>{{ session('success') }}</div>
                </div>
                <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <div class="row row-cards">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Task Information</h3></div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Order Number</div>
                                <div class="datagrid-content font-weight-medium">
                                    <a href="{{ route('erp.orders.show', $pickList->order_id) }}">{{ $pickList->order->order_number }}</a>
                                </div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Customer / Party</div>
                                <div class="datagrid-content">{{ $pickList->order->party->name ?? '-' }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Warehouse</div>
                                <div class="datagrid-content">{{ $pickList->warehouse->name ?? '-' }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Assigned Operator</div>
                                <div class="datagrid-content">
                                    @if($pickList->assignedTo)
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-xs me-2 rounded bg-blue-lt text-uppercase">{{ substr($pickList->assignedTo->name, 0, 1) }}</span>
                                            {{ $pickList->assignedTo->name }}
                                        </div>
                                    @else
                                        <span class="text-muted">Unassigned</span>
                                    @endif
                                </div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Current Status</div>
                                <div class="datagrid-content">
                                    @php $cls = match($pickList->status) { 'completed'=>'bg-green-lt','in_progress'=>'bg-blue-lt','pending'=>'bg-yellow-lt',default=>'bg-secondary-lt' }; @endphp
                                    <span class="badge {{ $cls }} text-uppercase">{{ str_replace('_',' ',$pickList->status) }}</span>
                                </div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Started At</div>
                                <div class="datagrid-content">{{ $pickList->started_at?->format('d M Y, H:i') ?? '—' }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Completed At</div>
                                <div class="datagrid-content">{{ $pickList->completed_at?->format('d M Y, H:i') ?? '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Items to Pick</h3>
                        <div class="card-actions">
                            <span class="badge bg-blue-lt">{{ $pickList->items->count() }} Items</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th>Product Details</th>
                                    <th>Location</th>
                                    <th class="text-center">Required</th>
                                    <th class="text-center">Picked</th>
                                    <th>Status</th>
                                    <th class="w-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pickList->items as $item)
                                <tr>
                                    <td>
                                        <div class="font-weight-medium">{{ $item->product->name }}</div>
                                        <div class="text-muted small">Batch: {{ $item->batch->batch_no ?? '—' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge badge-outline text-blue border-blue-subtle">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xs me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7l6 -3l6 3l6 -3v13l-6 3l-6 -3l-6 3v-13" /><path d="M9 4v13" /><path d="M15 7v13" /></svg>
                                            {{ $item->bin_location ?? 'Shelf' }}
                                        </span>
                                    </td>
                                    <td class="text-center font-weight-bold">{{ $item->requested_qty }}</td>
                                    <td class="text-center">{{ $item->picked_qty }}</td>
                                    <td>
                                        @php $sc = match($item->status) { 'picked'=>'bg-green-lt','partial'=>'bg-yellow-lt','pending'=>'bg-secondary-lt',default=>'bg-secondary-lt' }; @endphp
                                        <span class="badge {{ $sc }}">{{ ucfirst($item->status) }}</span>
                                    </td>
                                    <td>
                                        @if($item->status !== 'picked' && $pickList->status === 'in_progress')
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#pickModal{{ $item->id }}">
                                            Pick
                                        </button>
                                        
                                        {{-- Pick Modal --}}
                                        <div class="modal modal-blur fade" id="pickModal{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                                <form method="POST" action="{{ route('erp.wms.pick-list.record-pick', $item) }}">
                                                    @csrf @method('PATCH')
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Record Picking</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Product</label>
                                                                <div class="form-control-plaintext">{{ $item->product->name }}</div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Picked Quantity</label>
                                                                <input type="number" name="picked_qty" class="form-control" value="{{ $item->requested_qty - $item->picked_qty }}" min="0" max="{{ $item->requested_qty - $item->picked_qty }}" step="0.01" required>
                                                                <div class="form-hint">Max allowed: {{ $item->requested_qty - $item->picked_qty }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Confirm Pick</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

