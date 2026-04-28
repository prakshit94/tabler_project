@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">New Stock Transfer</h2>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="card">
      <form action="{{ route('erp.stock-transfers.store') }}" method="POST">
        @csrf
        <div class="card-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">From Warehouse</label>
                <select name="from_warehouse_id" class="form-select" required>
                  <option value="">Select Source</option>
                  @foreach($warehouses as $wh)
                  <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">To Warehouse</label>
                <select name="to_warehouse_id" class="form-select" required>
                  <option value="">Select Destination</option>
                  @foreach($warehouses as $wh)
                  <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-8">
              <div class="mb-3">
                <label class="form-label">Product</label>
                <select name="product_id" class="form-select" required>
                  <option value="">Select Product</option>
                  @foreach($products as $prod)
                  <option value="{{ $prod->id }}">{{ $prod->name }} ({{ $prod->sku }})</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity" class="form-control" step="0.01" min="0.01" required>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Transfer Date</label>
            <input type="date" name="transfer_date" class="form-control" value="{{ date('Y-m-d') }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="card-footer text-end">
          <a href="{{ route('erp.stock-transfers.index') }}" class="btn btn-link">Cancel</a>
          <button type="submit" class="btn btn-primary">Complete Transfer</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
