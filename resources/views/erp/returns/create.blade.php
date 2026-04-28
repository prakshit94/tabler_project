@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Process Return for Order #{{ $order->order_number }}</h2>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <form action="{{ route('erp.returns.store') }}" method="POST">
      @csrf
      <input type="hidden" name="order_id" value="{{ $order->id }}">
      
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Return Date</label>
                <input type="date" name="return_date" class="form-control" value="{{ date('Y-m-d') }}" required>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Reason</label>
                <input type="text" name="reason" class="form-control" placeholder="e.g. Damaged, Wrong Item">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-header"><h3 class="card-title">Items to Return</h3></div>
        <div class="table-responsive">
          <table class="table card-table table-vcenter">
            <thead>
              <tr>
                <th>Product</th>
                <th>Ordered Qty</th>
                <th class="w-1">Return Qty</th>
              </tr>
            </thead>
            <tbody>
              @foreach($order->items as $idx => $item)
              <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }} {{ $item->product->unit }}</td>
                <td>
                  <input type="hidden" name="items[{{ $idx }}][product_id]" value="{{ $item->product_id }}">
                  <input type="number" name="items[{{ $idx }}][quantity]" class="form-control" step="0.01" min="0" max="{{ $item->quantity }}" value="0">
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="card-footer text-end">
          <a href="{{ route('erp.orders.show', $order->id) }}" class="btn btn-link">Cancel</a>
          <button type="submit" class="btn btn-primary">Process Return</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
