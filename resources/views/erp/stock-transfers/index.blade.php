@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Stock Transfers</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="{{ route('erp.stock-transfers.create') }}" class="btn btn-primary d-none d-sm-inline-block">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
            New Transfer
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="card">
      <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap">
          <thead>
            <tr>
              <th>Date</th>
              <th>Product</th>
              <th>From Warehouse</th>
              <th>To Warehouse</th>
              <th>Quantity</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($transfers as $transfer)
            <tr>
              <td>{{ \Carbon\Carbon::parse($transfer->transfer_date)->format('d M Y') }}</td>
              <td>{{ $transfer->product->name }}</td>
              <td>{{ $transfer->fromWarehouse->name }}</td>
              <td>{{ $transfer->toWarehouse->name }}</td>
              <td><strong>{{ $transfer->quantity }} {{ $transfer->product->unit }}</strong></td>
              <td><span class="badge bg-green-lt">{{ ucfirst($transfer->status) }}</span></td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center text-secondary">No transfers found</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="card-footer d-flex align-items-center">
        {{ $transfers->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
