@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="mb-1">
          <span class="badge {{ $order->status_badge_class }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
        </div>
        <h2 class="page-title">Order #{{ $order->order_number }}</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          {{-- NEW WMS LIFECYCLE BUTTONS --}}
          @if($order->status == 'pending' || $order->status == 'draft')
          <form action="{{ route('erp.orders.confirm', $order->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary">Confirm & Reserve</button>
          </form>
          @endif

          @if($order->status == 'confirmed' || $order->status == 'backordered')
          <form action="{{ route('erp.orders.allocate', $order->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-cyan">Allocate Stock</button>
          </form>
          @endif

          @if($order->status == 'allocated')
          <form action="{{ route('erp.wms.pick-list.generate', $order->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-warning">Generate Pick List</button>
          </form>
          @endif

          @if($order->status == 'packed')
          <a href="{{ route('erp.shipments.create', $order->id) }}" class="btn btn-purple">Create Shipment</a>
          @endif

          @if($order->status == 'shipped' || $order->status == 'in_transit')
          <form action="{{ route('erp.orders.deliver', $order->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success">Mark Delivered</button>
          </form>
          @endif

          @if($order->status == 'delivered')
          <form action="{{ route('erp.orders.close', $order->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-dark">Close Order</button>
          </form>
          @endif

          {{-- EXISTING LEGACY BUTTONS --}}
          @if($order->status == 'pending')
          <form action="{{ route('erp.orders.update-status', $order->id) }}" method="POST" class="d-inline">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="completed">
            <button type="submit" class="btn btn-outline-success">Legacy Complete</button>
          </form>
          @endif
          
          @if(in_array($order->status, ['completed', 'delivered', 'closed']))
          <a href="{{ route('erp.invoices.create', ['order_id' => $order->id]) }}" class="btn btn-azure">Generate Invoice</a>
          <a href="{{ route('erp.returns.create', ['order_id' => $order->id]) }}" class="btn btn-warning">Process Return</a>
          @endif

          @if(in_array($order->status, ['pending', 'draft', 'confirmed', 'on_hold']))
          <form action="{{ route('erp.orders.cancel', $order->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-danger">Cancel Order</button>
          </form>
          @endif

          <button type="button" class="btn btn-primary" onclick="javascript:window.print();">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
            Print
          </button>
        </div>
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
            <p class="h3">From</p>
            <address>
              <strong>My Company</strong><br>
              Warehouse: {{ $order->warehouse->name }}<br>
              {{ $order->warehouse->state }}
            </address>
          </div>
          <div class="col-6 text-end">
            <p class="h3">{{ $order->type == 'sale' ? 'To' : 'From Vendor' }}</p>
            <address>
              <strong>{{ $order->party->name }}</strong><br>
              GSTIN: {{ $order->party->gstin }}<br>
              {{ $order->party->phone }}<br>
              {{ $order->party->email }}
            </address>
          </div>
          <div class="col-12 my-5">
            <h1>Order #{{ $order->order_number }}</h1>
            <p>Date: {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}</p>
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
          @foreach($order->items as $idx => $item)
          <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td>
              <p class="strong mb-1">{{ $item->product->name }}</p>
              <div class="text-secondary">{{ $item->product->sku }}</div>
            </td>
            <td class="text-center">{{ $item->quantity }}</td>
            <td class="text-end">₹ {{ number_format($item->unit_price, 2) }}</td>
            <td class="text-end">₹ {{ number_format($item->total_price, 2) }}</td>
          </tr>
          @endforeach
          <tr>
            <td colspan="4" class="strong text-end">Subtotal</td>
            <td class="text-end">₹ {{ number_format($order->sub_total, 2) }}</td>
          </tr>
          <tr>
            <td colspan="4" class="font-weight-bold text-uppercase text-end">Total Due</td>
            <td class="font-weight-bold text-end">₹ {{ number_format($order->total_amount, 2) }}</td>
          </tr>
        </table>
        <p class="text-secondary text-center mt-5">Thank you for your business!</p>
      </div>
    </div>

    {{-- WMS DATA SECTIONS --}}
    <div class="row row-cards mt-3">
      @if($order->allocations->isNotEmpty())
      <div class="col-12">
        <div class="card">
          <div class="card-header"><h3 class="card-title">Stock Allocations</h3></div>
          <div class="table-responsive">
            <table class="table table-vcenter card-table">
              <thead><tr><th>Product</th><th>Batch</th><th>Bin</th><th>Qty</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($order->allocations as $alloc)
                <tr>
                  <td>{{ $alloc->product->name }}</td>
                  <td>{{ $alloc->batch->batch_number ?? '-' }}</td>
                  <td>{{ $alloc->bin->name ?? '-' }}</td>
                  <td>{{ $alloc->quantity }}</td>
                  <td><span class="badge bg-{{ $alloc->status == 'allocated' ? 'blue' : 'green' }}">{{ $alloc->status }}</span></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
      @endif

      @if($order->pickLists->isNotEmpty())
      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><h3 class="card-title">Pick Lists</h3></div>
          <div class="list-group list-group-flush">
            @foreach($order->pickLists as $pl)
            <a href="{{ route('erp.wms.pick-list.show', $pl->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
              <span>{{ $pl->pick_list_number }}</span>
              <span class="badge bg-{{ $pl->status == 'completed' ? 'green' : 'yellow' }}">{{ $pl->status }}</span>
            </a>
            @endforeach
          </div>
        </div>
      </div>
      @endif

      @if($order->shipments->isNotEmpty())
      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><h3 class="card-title">Shipments</h3></div>
          <div class="list-group list-group-flush">
            @foreach($order->shipments as $sh)
            <a href="{{ route('erp.shipments.show', $sh->id) }}" class="list-group-item list-group-item-action">
              <div class="d-flex justify-content-between">
                <strong>{{ $sh->tracking_number ?? 'Shipment #'.$sh->id }}</strong>
                <span class="badge bg-purple">{{ $sh->status }}</span>
              </div>
              <small class="text-muted">{{ $sh->carrier_name }} — {{ $sh->shipped_at?->format('d M') }}</small>
            </a>
            @endforeach
          </div>
        </div>
      </div>
      @endif

      @if($order->backorders->isNotEmpty())
      <div class="col-12">
        <div class="card border-warning">
          <div class="card-header bg-warning-lt"><h3 class="card-title">Active Backorders</h3></div>
          <div class="table-responsive">
            <table class="table table-vcenter card-table">
              <thead><tr><th>Product</th><th>Pending</th><th>Fulfilled</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($order->backorders as $bo)
                <tr>
                  <td>{{ $bo->product->name }}</td>
                  <td>{{ $bo->pending_qty }}</td>
                  <td>{{ $bo->fulfilled_qty }}</td>
                  <td><span class="badge bg-warning">{{ $bo->status }}</span></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection

