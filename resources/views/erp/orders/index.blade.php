@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">{{ ucfirst($type) }} Orders</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="{{ route('erp.orders.create', ['type' => $type]) }}" class="btn btn-primary d-none d-sm-inline-block">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
            New {{ ucfirst($type) }} Order
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="card">
      <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
          <li class="nav-item">
            <a href="{{ route('erp.orders.index', ['type' => 'sale']) }}" class="nav-link {{ $type === 'sale' ? 'active' : '' }}">Sales Orders</a>
          </li>
          <li class="nav-item">
            <a href="{{ route('erp.orders.index', ['type' => 'purchase']) }}" class="nav-link {{ $type === 'purchase' ? 'active' : '' }}">Purchase Orders</a>
          </li>
        </ul>
      </div>

      <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap">
          <thead>
            <tr>
              <th>Order #</th>
              <th>Date</th>
              <th>Party</th>
              <th>Warehouse</th>
              <th>Total Amount</th>
              <th>Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($orders as $order)
            <tr>
              <td><a href="{{ route('erp.orders.show', $order->id) }}">{{ $order->order_number }}</a></td>
              <td>{{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}</td>
              <td>{{ $order->party->name }}</td>
              <td>{{ $order->warehouse->name }}</td>
              <td>₹ {{ number_format($order->total_amount, 2) }}</td>
              <td>
                @php
                  $badgeClass = match($order->status) {
                    'pending' => 'bg-yellow-lt',
                    'completed' => 'bg-green-lt',
                    'cancelled' => 'bg-red-lt',
                    default => 'bg-secondary-lt'
                  };
                @endphp
                <span class="badge {{ $badgeClass }}">{{ ucfirst($order->status) }}</span>
              </td>
              <td class="text-end">
                <a href="{{ route('erp.orders.show', $order->id) }}" class="btn btn-sm btn-white">View</a>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center text-secondary">No orders found</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="card-footer d-flex align-items-center">
        {{ $orders->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
