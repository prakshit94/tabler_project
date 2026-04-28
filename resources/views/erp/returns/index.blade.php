@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Order Returns</h2>
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
              <th>Return #</th>
              <th>Date</th>
              <th>Party</th>
              <th>Order #</th>
              <th>Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($returns as $return)
            <tr>
              <td><a href="{{ route('erp.returns.show', $return->id) }}">{{ $return->return_number }}</a></td>
              <td>{{ \Carbon\Carbon::parse($return->return_date)->format('d M Y') }}</td>
              <td>{{ $return->party->name }}</td>
              <td>{{ $return->order->order_number ?? '-' }}</td>
              <td><span class="badge bg-green-lt">{{ ucfirst($return->status) }}</span></td>
              <td class="text-end">
                <a href="{{ route('erp.returns.show', $return->id) }}" class="btn btn-sm btn-white">View</a>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center text-secondary">No returns found</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="card-footer d-flex align-items-center">
        {{ $returns->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
