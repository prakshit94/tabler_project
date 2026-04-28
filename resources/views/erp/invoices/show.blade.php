@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Invoice {{ $invoice->invoice_number }}</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        @if($invoice->status != 'paid')
        <a href="{{ route('erp.payments.create', ['invoice_id' => $invoice->id]) }}" class="btn btn-success">Record Payment</a>
        @endif
        <button type="button" class="btn btn-primary" onclick="javascript:window.print();">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
          Print Invoice
        </button>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="card card-lg">
      <div class="card-body">
        <div class="row">
          <div class="col-6">
            <p class="h3">Invoice From</p>
            <address>
              <strong>My Company</strong><br>
              Warehouse State: {{ $invoice->order->warehouse->state ?? '-' }}
            </address>
          </div>
          <div class="col-6 text-end">
            <p class="h3">Invoice To</p>
            <address>
              <strong>{{ $invoice->party->name }}</strong><br>
              GSTIN: {{ $invoice->party->gstin }}<br>
              {{ $invoice->party->email }}
            </address>
          </div>
          <div class="col-12 my-5">
            <h1>{{ $invoice->invoice_number }}</h1>
            <p>Invoice Date: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</p>
            <p>Due Date: {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') : 'N/A' }}</p>
          </div>
        </div>
        <table class="table table-transparent table-responsive">
          <thead>
            <tr>
              <th class="text-center" style="width: 1%"></th>
              <th>Product</th>
              <th class="text-center" style="width: 1%">Quantity</th>
              <th class="text-end" style="width: 1%">Unit Price</th>
              <th class="text-end" style="width: 1%">Amount</th>
            </tr>
          </thead>
          @foreach($invoice->items as $idx => $item)
          <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td>
              <p class="strong mb-1">{{ $item->product->name }}</p>
              <div class="text-secondary small">{{ $item->product->sku }}</div>
            </td>
            <td class="text-center">{{ $item->quantity }}</td>
            <td class="text-end">₹ {{ number_format($item->unit_price, 2) }}</td>
            <td class="text-end">₹ {{ number_format($item->total_price, 2) }}</td>
          </tr>
          @endforeach
          <tr>
            <td colspan="4" class="strong text-end">Subtotal</td>
            <td class="text-end">₹ {{ number_format($invoice->sub_total, 2) }}</td>
          </tr>
          <tr>
            <td colspan="4" class="font-weight-bold text-uppercase text-end">Total Due</td>
            <td class="font-weight-bold text-end">₹ {{ number_format($invoice->total_amount, 2) }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
