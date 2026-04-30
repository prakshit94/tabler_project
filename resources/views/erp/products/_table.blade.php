<div class="table-responsive">
  <table class="table card-table table-vcenter text-nowrap datatable">
    <thead>
      <tr>
        <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" id="select-all"></th>
        <th class="w-1">ID</th>
        <th>Product</th>
        <th>SKU</th>
        <th>Category</th>
        <th>Price (S/P)</th>
        <th>Stock</th>
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($products as $product)
      <tr>
        <td><input class="form-check-input m-0 align-middle product-checkbox" type="checkbox" value="{{ $product->id }}"></td>
        <td><span class="text-secondary">{{ $product->id }}</span></td>
        <td>
           <div class="d-flex align-items-center">
              @php
                $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
              @endphp
              @if($primaryImage)
                <span class="avatar me-2" style="background-image: url({{ asset('storage/' . $primaryImage->image_path) }})"></span>
              @else
                <span class="avatar me-2"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12l0 9" /><path d="M12 12l-8 -4.5" /><path d="M16 5.25l-8 4.5" /></svg></span>
              @endif
              <div>
                <div>{{ $product->name }}</div>
                <div class="text-secondary small">{{ $product->brand->name ?? 'No Brand' }}</div>
              </div>
           </div>
        </td>
        <td><code>{{ $product->sku }}</code></td>
        <td>
          <div>{{ $product->category->name ?? '-' }}</div>
          <div class="text-secondary small">{{ $product->subCategory->name ?? '' }}</div>
        </td>
        <td>
          <div>{{ number_format($product->selling_price, 2) }}</div>
          <div class="text-secondary small">{{ number_format($product->purchase_price, 2) }}</div>
        </td>
        <td>
          @php 
            $onHand = $product->stock_count ?? 0;
            $reserved = $product->reserved_count ?? 0;
            $committed = $product->committed_count ?? 0;
            $inTransit = $product->in_transit_count ?? 0;
            $available = $onHand - $reserved - $committed;
          @endphp
          <div class="mb-1">
            <span class="badge bg-blue-lt" title="Total Physical Stock">On Hand: {{ number_format($onHand, 0) }}</span>
          </div>
          <div class="mb-1">
            <span class="badge {{ $available <= ($product->min_stock_level ?? 0) ? 'bg-red-lt' : 'bg-green-lt' }}" title="Quantity - Reserved - Committed">
              Available: {{ number_format($available, 0) }}
            </span>
          </div>
          <div class="small text-secondary" style="font-size: 0.7rem;">
            Alloc: {{ number_format($reserved, 0) }} | 
            Pack: {{ number_format($committed, 0) }} |
            Ship: {{ number_format($inTransit, 0) }}
          </div>
        </td>
        <td class="text-end">
          @if($view === 'active')
            <div class="btn-list flex-nowrap justify-content-end">
              <a href="#" class="btn btn-sm btn-white view-product-btn" data-product-id="{{ $product->id }}">
                View
              </a>
              <a href="{{ route('erp.products.edit', $product->id) }}" class="btn btn-sm btn-primary">
                Edit
              </a>
              <form action="{{ route('erp.products.destroy', $product->id) }}" method="POST" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-ghost-danger" onclick="return confirm('Move to trash?')">Delete</button>
              </form>
            </div>
          @else
            <form action="{{ route('erp.products.restore', $product->id) }}" method="POST" class="d-inline">
              @csrf @method('PATCH')
              <button type="submit" class="btn btn-sm btn-success">Restore</button>
            </form>
            <form action="{{ route('erp.products.force-delete', $product->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-dark" onclick="return confirm('Permanently delete?')">Force Delete</button>
            </form>
          @endif
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="8" class="text-center text-secondary">No products found</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="card-footer d-flex align-items-center" id="pagination-links">
  <p class="m-0 text-secondary">Showing <span>{{ $products->firstItem() ?? 0 }}</span> to <span>{{ $products->lastItem() ?? 0 }}</span> of <span>{{ $products->total() }}</span> entries</p>
  <div class="ms-auto">
    {{ $products->links('pagination::bootstrap-4') }}
  </div>
</div>
