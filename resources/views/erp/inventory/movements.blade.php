@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Stock Movements Log</h2>
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
              <th>Warehouse</th>
              <th>Type</th>
              <th>Quantity</th>
              <th>Description</th>
            </tr>
          </thead>
          <tbody>
            @forelse($movements as $mov)
            <tr>
              <td>{{ $mov->created_at->format('d M Y H:i') }}</td>
              <td>{{ $mov->product->name }}</td>
              <td>{{ $mov->warehouse->name }}</td>
              <td>
                @if($mov->type == 'in')
                  <span class="badge bg-green-lt">Inbound</span>
                @elseif($mov->type == 'out')
                  <span class="badge bg-red-lt">Outbound</span>
                @else
                  <span class="badge bg-yellow-lt">Adjustment</span>
                @endif
              </td>
              <td>
                <strong>{{ ($mov->type == 'out' ? '-' : '+') . $mov->quantity }}</strong>
              </td>
              <td>{{ $mov->description ?? $mov->reference_type }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center text-secondary">No movements recorded</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="card-footer d-flex align-items-center">
        {{ $movements->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
