<div class="modal-header">
    <div class="d-flex align-items-center">
        @php
            $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
        @endphp
        @if($primaryImage)
            <span class="avatar avatar-lg me-3" style="background-image: url({{ asset('storage/' . $primaryImage->image_path) }})"></span>
        @else
            <span class="avatar avatar-lg bg-blue-lt me-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12l0 9" /><path d="M12 12l-8 -4.5" /><path d="M16 5.25l-8 4.5" /></svg>
            </span>
        @endif
        <div>
            <h5 class="modal-title h2 mb-0">{{ $product->name }}</h5>
            <div class="text-secondary small">SKU: <span class="badge bg-blue-lt">{{ $product->sku }}</span></div>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body p-0">
    <div class="row g-0">
        <div class="col-md-7 border-end">
            <div class="p-4">
                <div class="hr-text hr-text-left mt-0">General Information</div>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-secondary mb-1 small uppercase font-weight-bold">Category</div>
                        <div>{{ $product->category->name ?? 'N/A' }} / {{ $product->subCategory->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-secondary mb-1 small uppercase font-weight-bold">Brand</div>
                        <div>{{ $product->brand->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-secondary mb-1 small uppercase font-weight-bold">Unit</div>
                        <div>{{ $product->unit }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-secondary mb-1 small uppercase font-weight-bold">HSN Code</div>
                        <div>{{ $product->hsnCode->code ?? 'N/A' }}</div>
                    </div>
                </div>

                <div class="hr-text hr-text-left">Pricing & Tax</div>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-secondary mb-1 small uppercase font-weight-bold">Purchase Price</div>
                        <div class="h3 mb-0 text-danger">₹ {{ number_format($product->purchase_price, 2) }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-secondary mb-1 small uppercase font-weight-bold">Selling Price</div>
                        <div class="h3 mb-0 text-success">₹ {{ number_format($product->selling_price, 2) }}</div>
                    </div>
                    <div class="col-12">
                        <div class="text-secondary mb-1 small uppercase font-weight-bold">Tax Rate</div>
                        <div>{{ $product->taxRate->name ?? 'None' }} ({{ $product->taxRate->rate ?? 0 }}%)</div>
                    </div>
                </div>

                <div class="hr-text hr-text-left">Description</div>
                <div class="text-secondary italic">
                    {{ $product->description ?: 'No description provided.' }}
                </div>
            </div>
        </div>
        <div class="col-md-5 bg-surface-secondary">
            <div class="p-4">
                <div class="hr-text hr-text-left mt-0">Stock Overview</div>
                <div class="card shadow-none border-0 mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-3">
                            <span class="avatar avatar-md bg-primary-lt me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12l0 9" /><path d="M12 12l-8 -4.5" /><path d="M16 5.25l-8 4.5" /></svg>
                            </span>
                            <div>
                                <div class="text-secondary small">Total Inventory</div>
                                <div class="h2 mb-0">{{ $product->stocks->sum('quantity') }} <small class="text-muted">{{ $product->unit }}</small></div>
                            </div>
                        </div>
                        <div class="progress progress-sm">
                            @php 
                                $total = $product->stocks->sum('quantity');
                                $min = $product->min_stock_level ?: 10;
                                $percent = $total > 0 ? min(($total / $min) * 100, 100) : 0;
                                $color = $total < $min ? 'bg-danger' : 'bg-success';
                            @endphp
                            <div class="progress-bar {{ $color }}" style="width: {{ $percent }}%" role="progressbar"></div>
                        </div>
                        <div class="text-secondary small mt-1">Min Stock Level: {{ $product->min_stock_level ?: 0 }}</div>
                    </div>
                </div>

                <div class="text-secondary mb-2 small uppercase font-weight-bold">Stock per Warehouse</div>
                <div class="list-group list-group-flush list-group-hoverable border-0 bg-transparent">
                    @forelse($product->stocks as $stock)
                    <div class="list-group-item bg-transparent px-0 border-bottom-dashed">
                        <div class="row align-items-center">
                            <div class="col text-truncate">
                                <span class="text-body d-block font-weight-bold">{{ $stock->warehouse->name }}</span>
                                <small class="d-block text-secondary text-truncate mt-n1">{{ $stock->warehouse->state }}</small>
                            </div>
                            <div class="col-auto">
                                <span class="badge {{ $stock->quantity < ($product->min_stock_level / 2) ? 'bg-red' : 'bg-azure' }}">{{ $stock->quantity }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-secondary italic">
                        No stock records found.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Close</button>
    <a href="{{ route('erp.products.edit', $product->id) }}" class="btn btn-primary ms-auto">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
        Edit Product
    </a>
</div>
