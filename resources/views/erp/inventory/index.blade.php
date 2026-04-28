@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Inventory Stock</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-adjust-stock">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
            Adjust Stock
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="card">
      <div class="card-body border-bottom py-3">
        <form action="{{ route('erp.inventory.index') }}" method="GET" class="row g-3">
          <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search product..." value="{{ request('search') }}">
          </div>
          <div class="col-md-3">
            <select name="warehouse_id" class="form-select">
              <option value="">All Warehouses</option>
              @foreach($warehouses as $wh)
              <option value="{{ $wh->id }}" @selected(request('warehouse_id') == $wh->id)>{{ $wh->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
          </div>
        </form>
      </div>

      <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap">
          <thead>
            <tr>
              <th>Product</th>
              <th>SKU</th>
              <th>Warehouse</th>
              <th>Current Stock</th>
              <th>Last Updated</th>
            </tr>
          </thead>
          <tbody>
            @forelse($stocks as $stock)
            <tr>
              <td>{{ $stock->product->name }}</td>
              <td><code>{{ $stock->product->sku }}</code></td>
              <td>{{ $stock->warehouse->name }}</td>
              <td>
                <span class="badge {{ $stock->quantity <= ($stock->product->min_stock_level ?? 0) ? 'bg-red-lt' : 'bg-green-lt' }}">
                  {{ $stock->quantity }} {{ $stock->product->unit }}
                </span>
              </td>
              <td>{{ $stock->updated_at->diffForHumans() }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-center text-secondary">No stock records found</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="card-footer d-flex align-items-center">
        {{ $stocks->links() }}
      </div>
    </div>
  </div>
</div>

<!-- Modal Adjust Stock -->
<div class="modal modal-blur fade" id="modal-adjust-stock" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form action="{{ route('erp.inventory.adjust') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Adjust Stock Levels</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Product</label>
            <select name="product_id" class="form-select" required>
               <option value="">Select Product</option>
               @foreach(\App\Models\Product::all() as $prod)
               <option value="{{ $prod->id }}">{{ $prod->name }} ({{ $prod->sku }})</option>
               @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Warehouse</label>
            <select name="warehouse_id" class="form-select" required>
               @foreach($warehouses as $wh)
               <option value="{{ $wh->id }}">{{ $wh->name }}</option>
               @endforeach
            </select>
          </div>
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select" required>
                  <option value="in">Stock In (+)</option>
                  <option value="out">Stock Out (-)</option>
                  <option value="adjustment">Set Absolute Value</option>
                </select>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity" class="form-control" step="0.01" required>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Reason / Reference</label>
            <textarea name="reason" class="form-control" placeholder="e.g. Physical audit, correction"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary ms-auto">Adjust Stock</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
