@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Return #{{ $return->return_number }}</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <button type="button" class="btn btn-primary" onclick="javascript:window.print();">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
          Print Return Note
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
            <p class="h3">Return From</p>
            <address>
              <strong>{{ $return->party->name }}</strong><br>
              GSTIN: {{ $return->party->gstin }}
            </address>
          </div>
          <div class="col-6 text-end">
            <p class="h3">Order Info</p>
            <address>
              <strong>Order #{{ $return->order->order_number }}</strong><br>
              Date: {{ \Carbon\Carbon::parse($return->order->order_date)->format('d M Y') }}
            </address>
          </div>
          <div class="col-12 my-5">
            <h1>Return #{{ $return->return_number }}</h1>
            <p>Return Date: {{ \Carbon\Carbon::parse($return->return_date)->format('d M Y') }}</p>
            <p>Reason: {{ $return->reason ?? '-' }}</p>
          </div>
        </div>
        <table class="table table-transparent table-responsive">
          <thead>
            <tr>
              <th class="text-center" style="width: 1%"></th>
              <th>Product</th>
              <th class="text-center" style="width: 1%">Quantity Returned</th>
            </tr>
          </thead>
          @foreach($return->items as $idx => $item)
          <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td>
              <p class="strong mb-1">{{ $item->product->name }}</p>
              <div class="text-secondary small">{{ $item->product->sku }}</div>
            </td>
            <td class="text-center">{{ $item->quantity }} {{ $item->product->unit }}</td>
          </tr>
          @endforeach
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
