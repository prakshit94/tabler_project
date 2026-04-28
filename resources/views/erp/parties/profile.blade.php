@extends('layouts.tabler')

@section('content')
<div class="container-xl">
    <!-- Page Header -->
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle">ERP System / Parties</div>
                <h2 class="page-title">{{ $party->name }} <span class="badge bg-blue-lt ms-2">{{ ucfirst($party->type) }}</span></h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <!-- Cart Summary in Header -->
                    <div class="d-none d-md-flex align-items-center me-3 border-end pe-3">
                        <div class="text-end me-2">
                            <div class="font-weight-bold">{{ count($cart) }} Items</div>
                            <div class="text-secondary small">₹ {{ number_format($cartTotal, 2) }}</div>
                        </div>
                        <a class="btn btn-primary btn-icon" data-bs-toggle="offcanvas" href="#offcanvasCart">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
                        </a>
                    </div>
                    

                </div>
            </div>
        </div>
    </div>

    <!-- Main Content with Vertical Tabs -->
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body p-0">
                    <div class="nav flex-column nav-pills nav-tabs-vertical" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active text-start py-3 px-4 border-0" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                            Customer Profile
                        </button>
                        <button class="nav-link text-start py-3 px-4 border-0" id="v-pills-orders-tab" data-bs-toggle="pill" data-bs-target="#v-pills-orders" type="button" role="tab">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M12 11l0 8" /><path d="M12 11l4.5 -4.5" /><path d="M12 11l-4.5 -4.5" /><path d="M4.5 14.5l15 0" /></svg>
                            Order History
                        </button>
                        <button class="nav-link text-start py-3 px-4 border-0" id="v-pills-products-tab" data-bs-toggle="pill" data-bs-target="#v-pills-products" type="button" role="tab">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12l0 9" /><path d="M12 12l-8 -4.5" /></svg>
                            Product Catalog
                        </button>
                        <a href="{{ route('erp.parties.index') }}" class="nav-link text-start py-3 px-4 border-0 text-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 14l2 -2l2 2" /><path d="M12 12l0 -9" /><path d="M12 12l-2 2" /><path d="M5 12a7 7 0 1 0 14 0a7 7 0 0 0 -14 0" /></svg>
                            Tag & Close Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="tab-content" id="v-pills-tabContent">
                <!-- Profile Section -->
                <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel">
                    <!-- Quick Stats Inside Tab -->
                    <div class="row row-cards mb-3">
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm bg-primary-lt">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-primary text-white avatar avatar-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M12 11l0 8" /><path d="M12 11l4.5 -4.5" /><path d="M12 11l-4.5 -4.5" /><path d="M4.5 14.5l15 0" /></svg>
                                            </span>
                                        </div>
                                        <div class="col text-truncate">
                                            <div class="font-weight-medium">{{ $orders->total() }} Orders</div>
                                            <div class="text-secondary small">Lifetime Purchase</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-green text-white avatar avatar-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.7 8a3 3 0 1 0 -2.7 -2" /><path d="M12 8a3 3 0 1 0 -2.7 -2" /><path d="M7.3 8a3 3 0 1 0 -2.7 -2" /><path d="M3 13h18l-2 7h-14l-2 -7z" /></svg>
                                            </span>
                                        </div>
                                        <div class="col text-truncate">
                                            <div class="font-weight-medium">₹ {{ number_format($party->credit_limit, 2) }}</div>
                                            <div class="text-secondary small">Credit Limit</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-info text-white avatar avatar-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 12l3 2" /><path d="M12 7v5" /></svg>
                                            </span>
                                        </div>
                                        <div class="col text-truncate">
                                            <div class="font-weight-medium">{{ $party->payment_terms ?? 'N/A' }}</div>
                                            <div class="text-secondary small">Payment Terms</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-yellow text-white avatar avatar-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                                            </span>
                                        </div>
                                        <div class="col text-truncate">
                                            <div class="font-weight-medium">{{ $party->created_at->format('M Y') }}</div>
                                            <div class="text-secondary small">Customer Since</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Detailed Customer Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="datagrid">
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Full Name</div>
                                    <div class="datagrid-content">{{ $party->name }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">GSTIN</div>
                                    <div class="datagrid-content"><code>{{ $party->gstin ?? 'N/A' }}</code></div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Phone</div>
                                    <div class="datagrid-content">{{ $party->phone ?? 'N/A' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Email</div>
                                    <div class="datagrid-content">{{ $party->email ?? 'N/A' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">State</div>
                                    <div class="datagrid-content">{{ $party->state ?? 'N/A' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Status</div>
                                    <div class="datagrid-content">
                                        <span class="status status-green">Active</span>
                                    </div>
                                </div>
                            </div>

                            <h4 class="mt-4 mb-3">Addresses</h4>
                            <div class="row row-cards">
                                @forelse($party->addresses as $address)
                                <div class="col-md-6">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="subheader mb-2">{{ ucfirst($address->type) }} Address</div>
                                            <address class="m-0 text-secondary">
                                                {{ $address->address }}<br>
                                                {{ $address->city }}, {{ $address->state }} - {{ $address->pincode }}
                                            </address>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12">
                                    <div class="text-muted italic">No addresses registered.</div>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders Section -->
                <div class="tab-pane fade" id="v-pills-orders" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Transaction History</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Warehouse</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $order)
                                    <tr>
                                        <td><span class="font-weight-bold">{{ $order->order_number }}</span></td>
                                        <td>{{ $order->order_date->format('d M Y') }}</td>
                                        <td>{{ $order->warehouse->name ?? 'N/A' }}</td>
                                        <td>₹ {{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status == 'completed' ? 'green' : ($order->status == 'pending' ? 'yellow' : 'blue') }}-lt">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('erp.orders.show', $order->id) }}" class="btn btn-sm btn-white">View Details</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">No past orders found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($orders->hasPages())
                        <div class="card-footer">
                            {{ $orders->fragment('v-pills-orders')->links() }}
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Products Tab -->
                <div class="tab-pane fade" id="v-pills-products" role="tabpanel">
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body border-bottom py-3">
                            <div class="row g-3 align-items-center">
                                <div class="col-md-4">
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                                        </span>
                                        <input type="text" id="catalog-search" class="form-control" placeholder="Search name or SKU..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select id="catalog-category" class="form-select">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select id="catalog-sort" class="form-select">
                                        <option value="created_at|desc">Latest First</option>
                                        <option value="name|asc">Name (A-Z)</option>
                                        <option value="name|desc">Name (Z-A)</option>
                                        <option value="selling_price|asc">Price (Low-High)</option>
                                        <option value="selling_price|desc">Price (High-Low)</option>
                                    </select>
                                </div>
                                <div class="col-md-2 text-end">
                                    <button type="button" id="reset-catalog-filters" class="btn btn-ghost-secondary w-100">Reset</button>
                                </div>
                            </div>
                        </div>
                        
                        <div id="catalog-table-container">
                            @include('erp.parties._product_table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Premium Checkout Modal -->
<div class="modal modal-blur fade" id="modal-checkout" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content shadow-lg border-0">
            <form action="{{ route('erp.parties.place-order', $party->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title h2 m-0">Finalize Order Placement</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-7">
                            <h3 class="card-title mb-3">Order Configuration</h3>
                            <div class="mb-3">
                                <label class="form-label required">Select Shipping Warehouse</label>
                                <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                                    @foreach($warehouses as $warehouse)
                                    <label class="form-selectgroup-item flex-fill mb-2">
                                        <input type="radio" name="warehouse_id" value="{{ $warehouse->id }}" class="form-selectgroup-input" {{ $loop->first ? 'checked' : '' }}>
                                        <div class="form-selectgroup-label d-flex align-items-center p-3">
                                            <div class="me-3">
                                                <span class="form-selectgroup-check"></span>
                                            </div>
                                            <div class="form-selectgroup-label-content">
                                                <div class="font-weight-bold">{{ $warehouse->name }}</div>
                                                <div class="text-secondary small">{{ $warehouse->location ?? 'Default Distribution Center' }}</div>
                                            </div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-selectgroup-item w-100">
                                            <input type="radio" name="payment_method" value="Cash" class="form-selectgroup-input" checked>
                                            <div class="form-selectgroup-label text-center py-2">Cash</div>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-selectgroup-item w-100">
                                            <input type="radio" name="payment_method" value="Bank Transfer" class="form-selectgroup-input">
                                            <div class="form-selectgroup-label text-center py-2">Bank</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-5 border-start-md">
                            <h3 class="card-title mb-3">Order Summary</h3>
                            <div class="card bg-light border-0">
                                <div class="card-body p-3">
                                    @php 
                                        $subTotal = array_reduce($cart, function($carry, $item) { return $carry + ($item['price'] * $item['quantity']); }, 0);
                                        $taxTotal = array_reduce($cart, function($carry, $item) { return $carry + (($item['tax'] ?? 0) * $item['quantity']); }, 0);
                                    @endphp
                                    <div class="list-group list-group-flush bg-transparent">
                                        @foreach($cart as $item)
                                        <div class="list-group-item bg-transparent px-0 py-2 border-0">
                                            <div class="d-flex justify-content-between small">
                                                <span class="text-truncate" style="max-width: 120px;">{{ $item['name'] }} x {{ $item['quantity'] }}</span>
                                                <span class="font-weight-bold">₹ {{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <hr class="my-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-secondary">Subtotal:</span>
                                        <span>₹ {{ number_format($subTotal, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-secondary">GST (18%):</span>
                                        <span class="text-success">+ ₹ {{ number_format($taxTotal, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between h2 mb-0 mt-3 pt-3 border-top">
                                        <span class="font-weight-bold">Total:</span>
                                        <span class="text-primary font-weight-bold">₹ {{ number_format($subTotal + $taxTotal, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 text-secondary small">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></svg>
                                Final order value may vary based on exact shipping costs.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                        Confirm & Place Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Premium Offcanvas Cart -->
<div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="offcanvasCart" aria-labelledby="offcanvasCartLabel" style="width: 400px;">
    <div class="offcanvas-header bg-dark text-white py-3">
        <div class="d-flex align-items-center">
            <span class="avatar avatar-sm bg-primary-lt text-primary me-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
            </span>
            <div>
                <h2 class="offcanvas-title h3 mb-0" id="offcanvasCartLabel">Active Cart</h2>
                <div class="small text-muted-active">{{ count($cart) }} Items for {{ Str::limit($party->name, 20) }}</div>
            </div>
        </div>
        <div class="ms-auto d-flex align-items-center">
            @if(count($cart) > 0)
            <form action="{{ route('erp.parties.cart.clear', $party->id) }}" method="POST" onsubmit="return confirm('Clear entire cart?');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-ghost-light btn-sm btn-icon" title="Clear Cart">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                </button>
            </form>
            @endif
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
    </div>
    <div class="offcanvas-body p-0 d-flex flex-column">
        @if(count($cart) > 0)
        <div class="flex-fill scroll-y p-3">
            <div class="list-group list-group-flush list-group-hoverable">
                @foreach($cart as $id => $item)
                <div class="list-group-item py-3 px-0 border-0 border-bottom">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-md rounded" style="background-image: url(/tabler/static/photos/product-{{ ($item['id'] % 5) + 1 }}.jpg)"></span>
                        </div>
                        <div class="col text-truncate">
                            <a href="#" class="text-reset d-block font-weight-bold">{{ $item['name'] }}</a>
                            <div class="d-block text-secondary text-truncate mt-n1 small">
                                SKU: {{ $item['sku'] }} | Qty: {{ $item['quantity'] }}
                            </div>
                        </div>
                        <div class="col-auto text-end">
                            <div class="font-weight-bold text-primary">₹ {{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                            <form action="{{ route('erp.parties.cart.remove', [$party->id, $id]) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-ghost-danger btn-sm border-0 p-0 mt-1">Remove</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Sticky Bottom Summary -->
        <div class="mt-auto p-4 bg-primary-lt border-top shadow-sm">
            @php 
                $subTotal = array_reduce($cart, function($carry, $item) { return $carry + ($item['price'] * $item['quantity']); }, 0);
                $taxTotal = array_reduce($cart, function($carry, $item) { return $carry + (($item['tax'] ?? 0) * $item['quantity']); }, 0);
                $discountTotal = array_reduce($cart, function($carry, $item) { return $carry + (($item['discount'] ?? 0) * $item['quantity']); }, 0);
            @endphp
            
            <div class="card mb-3 border-0 shadow-none bg-transparent">
                <div class="card-body p-0">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Subtotal</span>
                        <span class="h4 m-0">₹ {{ number_format($subTotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">GST (Estimated)</span>
                        <span class="text-success h4 m-0">+ ₹ {{ number_format($taxTotal, 2) }}</span>
                    </div>
                    @if($discountTotal > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Total Savings</span>
                        <span class="text-danger h4 m-0">- ₹ {{ number_format($discountTotal, 2) }}</span>
                    </div>
                    @endif
                    <hr class="my-3 opacity-20">
                    <div class="d-flex justify-content-between align-items-center mb-0">
                        <span class="h3 m-0">Grand Total</span>
                        <span class="h2 m-0 text-primary font-weight-bold">₹ {{ number_format($subTotal + $taxTotal - $discountTotal, 2) }}</span>
                    </div>
                </div>
            </div>
            
            <div class="row g-2">
                <div class="col">
                    <button class="btn btn-primary w-100 py-2 d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#modal-checkout" onclick="bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasCart')).hide();">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
                        Place Order
                    </button>
                </div>
            </div>
        </div>
        @else
        <div class="flex-fill d-flex flex-column align-items-center justify-content-center text-center p-5">
            <div class="avatar avatar-xl bg-light text-secondary mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
            </div>
            <h2 class="h3">Cart is Empty</h2>
            <p class="text-secondary max-w-250">Browse the catalog and add products to start a new order for this customer.</p>
            <button class="btn btn-primary mt-4" data-bs-dismiss="offcanvas">Start Browsing</button>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Tab persistence logic
        var hash = window.location.hash;
        if (hash) {
            var triggerEl = document.querySelector('button[data-bs-target="' + hash + '"]');
            if (triggerEl) {
                bootstrap.Tab.getInstance(triggerEl) ? bootstrap.Tab.getInstance(triggerEl).show() : new bootstrap.Tab(triggerEl).show();
            }
        }

        var tabEls = document.querySelectorAll('button[data-bs-toggle="pill"]');
        tabEls.forEach(function (tabEl) {
            tabEl.addEventListener('shown.bs.tab', function (event) {
                window.location.hash = event.target.getAttribute('data-bs-target');
            });
        });
        // Auto-open offcanvas if an item was recently added or removed
        @if(session('cart_open'))
            var offcanvasCartEl = document.getElementById('offcanvasCart');
            if (offcanvasCartEl) {
                var offcanvasCart = new bootstrap.Offcanvas(offcanvasCartEl);
                offcanvasCart.show();
            }
        @endif

        // Catalog AJAX Filtering
        const catalogTableContainer = document.getElementById('catalog-table-container');
        const catalogSearch = document.getElementById('catalog-search');
        const catalogCategory = document.getElementById('catalog-category');
        const catalogSort = document.getElementById('catalog-sort');
        const resetFiltersBtn = document.getElementById('reset-catalog-filters');
        let catalogTimeout = null;

        function fetchCatalog(url = null) {
            if (!url) {
                // Use the base path to avoid accumulating query parameters
                url = new URL(window.location.origin + window.location.pathname);
                url.searchParams.set('search', catalogSearch.value);
                url.searchParams.set('category', catalogCategory.value);
                url.searchParams.set('sort', catalogSort.value);
            }

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    catalogTableContainer.innerHTML = html;
                    // Re-bind quantity buttons after AJAX update
                    bindQuantityButtons();
                });
        }

        if (catalogSearch) catalogSearch.addEventListener('input', () => {
            clearTimeout(catalogTimeout);
            catalogTimeout = setTimeout(() => fetchCatalog(), 500);
        });

        if (catalogCategory) catalogCategory.addEventListener('change', () => fetchCatalog());
        if (catalogSort) catalogSort.addEventListener('change', () => fetchCatalog());
        
        if (resetFiltersBtn) resetFiltersBtn.addEventListener('click', () => {
            catalogSearch.value = '';
            catalogCategory.value = '';
            catalogSort.value = 'created_at|desc';
            fetchCatalog();
        });

        // Event Delegation for Pagination inside Catalog
        document.addEventListener('click', function(e) {
            const link = e.target.closest('#catalog-pagination .pagination a');
            if (link) {
                e.preventDefault();
                fetchCatalog(link.href);
                // Scroll to top of catalog
                document.getElementById('v-pills-products').scrollIntoView({ behavior: 'smooth' });
            }
        });

        function bindQuantityButtons() {
            document.querySelectorAll('.qty-plus').forEach(function(button) {
                button.onclick = function() {
                    var input = this.closest('.input-group').querySelector('.product-qty');
                    input.value = parseInt(input.value) + 1;
                };
            });

            document.querySelectorAll('.qty-minus').forEach(function(button) {
                button.onclick = function() {
                    var input = this.closest('.input-group').querySelector('.product-qty');
                    if (parseInt(input.value) > 1) {
                        input.value = parseInt(input.value) - 1;
                    }
                };
            });
        }

        bindQuantityButtons();
    });
</script>
@endpush
@endsection
