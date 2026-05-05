@extends('layouts.tabler')

@section('title', 'Create Shipment')

@section('content')

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item">
                        <a href="{{ route('erp.shipments.index') }}">Shipments</a>
                    </li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>

            <h2 class="page-title">
                Create Shipment for {{ $order->order_number ?? '-' }}
            </h2>
        </div>
    </div>
</div>

</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card col-lg-7 mx-auto">
            <div class="card-header">
                <h3 class="card-title">Shipment Details</h3>
            </div>

        <form method="POST" action="{{ route('erp.shipments.store', $order ?? null) }}">
            @csrf

            <div class="card-body">

                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="mb-3">
                    <label class="form-label required">Carrier</label>
                    <input type="text"
                           name="carrier"
                           class="form-control"
                           value="{{ old('carrier') }}"
                           placeholder="DHL, FedEx, India Post..."
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tracking Number</label>
                    <input type="text"
                           name="tracking_number"
                           class="form-control"
                           value="{{ old('tracking_number') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Tracking URL</label>
                    <input type="url"
                           name="tracking_url"
                           class="form-control"
                           value="{{ old('tracking_url') }}"
                           placeholder="https://...">
                </div>

                <div class="mb-3">
                    <label class="form-label">Estimated Delivery</label>
                    <input type="date"
                           name="estimated_delivery"
                           class="form-control"
                           value="{{ old('estimated_delivery') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Shipping Cost (₹)</label>
                    <input type="number"
                           name="shipping_cost"
                           class="form-control"
                           value="{{ old('shipping_cost', 0) }}"
                           step="0.01"
                           min="0">
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes"
                              class="form-control"
                              rows="2">{{ old('notes') }}</textarea>
                </div>

            </div>

            <div class="card-footer d-flex">
                <a href="{{ route('erp.orders.show', $order ?? null) }}"
                   class="btn btn-link link-secondary">
                    Cancel
                </a>

                <button type="submit" class="btn btn-primary ms-auto">
                    Create Shipment & Ship Order
                </button>
            </div>

        </form>
    </div>
</div>

</div>
@endsection
