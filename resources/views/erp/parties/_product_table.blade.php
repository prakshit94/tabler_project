<div class="table-responsive">
    <table class="table table-vcenter card-table table-hover" id="product-catalog-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th class="text-center">Inventory</th>
                <th>Price</th>
                <th class="text-end">Add to Cart</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td>
                    <div class="d-flex py-1 align-items-center">
                        <span class="avatar me-2" style="background-image: url(/tabler/static/photos/product-{{ ($product->id % 5) + 1 }}.jpg)"></span>
                        <div class="flex-fill">
                            <div class="font-weight-medium text-primary">{{ $product->name }}</div>
                            <div class="text-secondary small">SKU: {{ $product->sku }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge bg-blue-lt">{{ $product->category->name ?? 'N/A' }}</span>
                </td>
                <td class="text-center">
                    @php 
                        $cart = session()->get("cart.{$party->id}", []);
                        $inCartQty = isset($cart[$product->id]) ? $cart[$product->id]['quantity'] : 0;
                        $totalStock = $product->stocks->sum('quantity') - $inCartQty; 
                    @endphp
                    <div class="font-weight-bold {{ $totalStock <= $product->min_stock_level ? 'text-danger' : 'text-success' }}">
                        <span class="stock-display" 
                              data-product-id="{{ $product->id }}" 
                              data-current-stock="{{ $totalStock }}" 
                              data-min-stock="{{ $product->min_stock_level }}">
                            {{ number_format($totalStock) }}
                        </span>
                        {{ $product->unit }}
                    </div>
                    <div class="text-secondary x-small">Min: {{ $product->min_stock_level }}</div>
                </td>
                <td>
                    <div class="font-weight-bold">₹ {{ number_format($product->selling_price, 2) }}</div>
                    <div class="text-secondary small text-decoration-line-through">₹ {{ number_format($product->mrp, 2) }}</div>
                </td>
                <td class="text-end">
                    <form action="{{ route('erp.parties.cart.add', $party->id) }}" method="POST" class="d-flex justify-content-end align-items-center cart-add-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="input-group input-group-flat w-auto">
                            <button type="button" class="btn btn-icon btn-sm btn-light border-end-0 qty-minus">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /></svg>
                            </button>
                            <input type="number" name="quantity" class="form-control form-control-sm text-center px-0 border-start-0 border-end-0 product-qty" value="0" min="0" style="width: 40px;">
                            <button type="button" class="btn btn-icon btn-sm btn-light border-start-0 qty-plus">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm ms-2" title="Add to Cart">
                                Add
                            </button>
                        </div>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-4">No products found matching your search.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($products->hasPages())
<div class="card-footer d-flex align-items-center" id="catalog-pagination">
    <p class="m-0 text-secondary d-none d-sm-block">Showing <span>{{ $products->firstItem() }}</span> to <span>{{ $products->lastItem() }}</span> of <span>{{ $products->total() }}</span> entries</p>
    <div class="ms-auto">
        {{ $products->appends(request()->except('products_page'))->fragment('v-pills-products')->links() }}
    </div>
</div>
@endif
