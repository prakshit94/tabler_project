@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Generate Invoice from Order #{{ $order->order_number }}</h2>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="card">
      <form action="{{ route('erp.invoices.store') }}" method="POST">
        @csrf
        <input type="hidden" name="order_id" value="{{ $order->id }}">
        <div class="card-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Invoice Date</label>
                <input type="date" name="invoice_date" class="form-control" value="{{ date('Y-m-d') }}" required>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Due Date</label>
                <input type="date" name="due_date" class="form-control" value="{{ date('Y-m-d', strtotime('+15 days')) }}">
              </div>
            </div>
          </div>
          <div class="hr-text">Order Details</div>
          <address>
            <strong>Party:</strong> {{ $order->party->name }}<br>
            <strong>Warehouse:</strong> {{ $order->warehouse->name }}<br>
            <strong>Total Amount:</strong> ₹ {{ number_format($order->total_amount, 2) }}
          </address>
        </div>
        <div class="card-footer text-end">
          <a href="{{ route('erp.orders.show', $order->id) }}" class="btn btn-link">Back to Order</a>
          <button type="submit" class="btn btn-primary">Generate Invoice</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
