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
          @php $stock = $product->stock_count ?? 0; @endphp
          <span class="badge {{ $stock <= ($product->min_stock_level ?? 0) ? 'bg-red-lt' : 'bg-green-lt' }}">
            {{ $stock }} {{ $product->unit }}
          </span>
        </td>
        <td class="text-end">
          @if($view === 'active')
            <a href="{{ route('erp.products.edit', $product->id) }}" class="btn btn-sm btn-primary">Edit</a>
            <form action="{{ route('erp.products.destroy', $product->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Move to trash?')">Delete</button>
            </form>
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
