@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Product Pricing History</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-add-price">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
            Add New Price
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="card mb-3">
      <div class="card-body">
        <form action="{{ route('erp.product-prices.index') }}" method="GET" class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Filter by Product</label>
            <select name="product_id" class="form-select" onchange="this.form.submit()">
              <option value="">All Products</option>
              @foreach($products as $p)
              <option value="{{ $p->id }}" @selected($productId == $p->id)>{{ $p->name }} ({{ $p->sku }})</option>
              @endforeach
            </select>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap">
          <thead>
            <tr>
              <th>Product</th>
              <th>Type</th>
              <th>Price</th>
              <th>Effective From</th>
              <th>Created At</th>
            </tr>
          </thead>
          <tbody>
            @forelse($prices as $price)
            <tr>
              <td>{{ $price->product->name }}</td>
              <td><span class="badge {{ $price->type == 'selling' ? 'bg-blue-lt' : 'bg-purple-lt' }}">{{ ucfirst($price->type) }}</span></td>
              <td><strong>₹ {{ number_format($price->price, 2) }}</strong></td>
              <td>{{ \Carbon\Carbon::parse($price->effective_from)->format('d M Y') }}</td>
              <td>{{ $price->created_at->diffForHumans() }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-center text-secondary">No pricing history found</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="card-footer d-flex align-items-center">
        {{ $prices->links() }}
      </div>
    </div>
  </div>
</div>

<!-- Modal Add Price -->
<div class="modal modal-blur fade" id="modal-add-price" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form action="{{ route('erp.product-prices.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Add New Pricing Rule</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Product</label>
            <select name="product_id" class="form-select" required>
              @foreach($products as $p)
              <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>
              @endforeach
            </select>
          </div>
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Price Type</label>
                <select name="type" class="form-select" required>
                  <option value="selling">Selling Price</option>
                  <option value="purchase">Purchase Price</option>
                </select>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Price Amount</label>
                <input type="number" name="price" class="form-control" step="0.01" required>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Effective From</label>
            <input type="date" name="effective_from" class="form-control" value="{{ date('Y-m-d') }}" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary ms-auto">Save Price</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
