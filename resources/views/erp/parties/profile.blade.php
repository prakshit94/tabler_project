@extends('layouts.tabler')

@section('content')
<div class="container-xl">
    <!-- Page Header -->
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle">ERP System / Farmers</div>
                <h2 class="page-title">{{ $party->name }} 
                    <span class="badge bg-muted-lt ms-2">{{ $party->party_code }}</span>
                    <span class="badge {{ $party->type == 'vendor' ? 'bg-purple-lt' : 'bg-blue-lt' }} ms-1">{{ ucfirst($party->type) }}</span>
                    @if($party->tags)
                        @foreach($party->tags as $tag)
                            <span class="badge bg-info-lt ms-1">{{ $tag }}</span>
                        @endforeach
                    @endif
                </h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <div class="d-none d-md-flex align-items-center me-3 border-end pe-3">
                        <div class="text-end me-2">
                            <div class="font-weight-bold" id="header-cart-count">{{ count($cart) }} Items</div>
                            <div class="text-secondary small" id="header-cart-total">₹ {{ number_format($cartTotal, 2) }}</div>
                        </div>
                        <a class="btn btn-primary btn-icon" data-bs-toggle="offcanvas" href="#offcanvasCart">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="nav flex-column nav-pills nav-tabs-vertical" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active text-start py-3 px-4 border-0" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                            Profile
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
                            Back to List
                        </a>
                    </div>
                </div>
            </div>

            <div class="card card-sm bg-primary-lt border-0 shadow-sm">
                <div class="card-body">
                    <div class="subheader opacity-75">Mobile Primary</div>
                    <div class="h3 mb-3">{{ $party->mobile }}</div>
                    <div class="subheader opacity-75">Outstanding</div>
                    <div class="h3 mb-3 {{ $party->outstanding_balance > 0 ? 'text-danger' : 'text-success' }}">₹ {{ number_format($party->outstanding_balance, 2) }}</div>
                    <div class="subheader opacity-75">Member Since</div>
                    <div class="h3 mb-0">{{ $party->created_at->format('d M, Y') }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="tab-content" id="v-pills-tabContent">
                <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Profile Overview</h3>
                        </div>
                        <div class="card-body">
                            <div class="datagrid">
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Full Name</div>
                                    <div class="datagrid-content">{{ $party->name }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Company / Farm</div>
                                    <div class="datagrid-content">{{ $party->company_name ?? ucfirst($party->type) }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Primary Mobile</div>
                                    <div class="datagrid-content">{{ $party->mobile }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Email</div>
                                    <div class="datagrid-content">{{ $party->email ?? 'N/A' }}</div>
                                </div>
                                
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Secondary Contact</div>
                                    <div class="datagrid-content">{{ $party->phone_number_2 ?? 'N/A' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Aadhaar (Last 4)</div>
                                    <div class="datagrid-content">{{ $party->aadhaar_last4 ?? 'N/A' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">GSTIN</div>
                                    <div class="datagrid-content font-monospace">{{ $party->gstin ?? 'N/A' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">PAN Number</div>
                                    <div class="datagrid-content font-monospace">{{ $party->pan_number ?? 'N/A' }}</div>
                                </div>

                                <div class="datagrid-item">
                                    <div class="datagrid-title">Credit Limit</div>
                                    <div class="datagrid-content">₹ {{ number_format($party->credit_limit, 2) }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Credit Valid Till</div>
                                    <div class="datagrid-content">{{ $party->credit_valid_till ? $party->credit_valid_till->format('d M Y') : 'Life-time' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Payment Terms</div>
                                    <div class="datagrid-content">{{ $party->payment_terms ?? 'Standard' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Ledger Group</div>
                                    <div class="datagrid-content">{{ $party->ledger_group ?? 'Sundry Debtors' }}</div>
                                </div>

                                <div class="datagrid-item">
                                    <div class="datagrid-title">Bank Name</div>
                                    <div class="datagrid-content">{{ $party->bank_name ?? 'N/A' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Account Number</div>
                                    <div class="datagrid-content">{{ $party->account_number ?? 'N/A' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">IFSC Code</div>
                                    <div class="datagrid-content font-monospace">{{ $party->ifsc_code ?? 'N/A' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">KYC Status</div>
                                    <div class="datagrid-content">
                                        @if($party->kyc_completed)
                                            <span class="status status-green">Verified</span>
                                        @else
                                            <span class="status status-yellow">Pending</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($party->type == 'farmer')
                            <h4 class="mt-4 mb-3 text-green">Agriculture Profile</h4>
                            <div class="datagrid">
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Land Area</div>
                                    <div class="datagrid-content">{{ $party->land_area }} {{ ucfirst($party->land_unit) }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Irrigation Type</div>
                                    <div class="datagrid-content">{{ ucfirst($party->irrigation_type) ?? 'Rainfed' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Primary Crops</div>
                                    <div class="datagrid-content">{{ $party->crops ? implode(', ', $party->crops) : 'N/A' }}</div>
                                </div>
                            </div>
                            @endif

                            <h4 class="mt-4 mb-2">Internal Notes</h4>
                            <p class="text-secondary mb-0">{{ $party->internal_notes ?? 'No internal notes registered for this account.' }}</p>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Saved Addresses</h3>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-add-address">Add New Address</button>
                        </div>
                        <div class="card-body bg-light">
                            <div class="row g-3">
                                @forelse($party->addresses as $address)
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm h-100">
                                        @if($address->is_default)
                                        <div class="card-status-start bg-primary"></div>
                                        @endif
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h4 class="card-title m-0">
                                                        {{ ucfirst($address->label ?? $address->type) }}
                                                        @if($address->is_default)
                                                            <span class="badge bg-primary ms-1">Default</span>
                                                        @endif
                                                    </h4>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-blue-lt mb-1">{{ ucfirst($address->type) }}</span>
                                                    <div>
                                                        <button onclick='editAddress(@json($address))' class="btn btn-sm btn-ghost-primary border-0 p-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            @if($address->contact_name || $address->contact_phone)
                                            <div class="mb-2 text-secondary d-flex align-items-center small">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                                                <strong>{{ $address->contact_name ?? 'N/A' }}</strong>
                                                @if($address->contact_phone)
                                                    <span class="ms-2"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" /></svg>{{ $address->contact_phone }}</span>
                                                @endif
                                            </div>
                                            @endif

                                            <address class="mb-0 text-muted small">
                                                @if($address->address_line1){{ $address->address_line1 }}<br>@endif
                                                @if($address->address_line2){{ $address->address_line2 }}<br>@endif
                                                {{ collect([$address->village, $address->taluka, $address->district])->filter()->join(', ') }}<br>
                                                {{ collect([$address->state, $address->country, $address->pincode])->filter()->join(', ') }}
                                                @if($address->post_office)<br>PO: {{ $address->post_office }}@endif
                                            </address>

                                            @if($address->latitude && $address->longitude)
                                            <div class="mt-2 text-secondary small d-flex align-items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" /></svg>
                                                Geo: {{ $address->latitude }}, {{ $address->longitude }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12">
                                    <div class="empty bg-white rounded shadow-sm border-0">
                                        <div class="empty-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" /></svg>
                                        </div>
                                        <p class="empty-title">No addresses found</p>
                                        <p class="empty-subtitle text-secondary">
                                            Try adding a new address for this account.
                                        </p>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders Section -->
                <div class="tab-pane fade" id="v-pills-orders" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header"><h3 class="card-title">Transaction History</h3></div>
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap datatable">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $order)
                                    <tr>
                                        <td><span class="font-weight-bold text-azure">{{ $order->order_number }}</span></td>
                                        <td>{{ $order->order_date->format('d M Y') }}</td>
                                        <td>₹ {{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status == 'completed' ? 'green' : ($order->status == 'pending' ? 'yellow' : 'blue') }}-lt">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('erp.orders.show', $order->id) }}" class="btn btn-sm btn-ghost-primary">View</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No transaction records found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Product Catalog -->
                <div class="tab-pane fade" id="v-pills-products" role="tabpanel">
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body border-bottom py-3">
                            <div class="row g-3 align-items-center">
                                <div class="col-md-5">
                                    <div class="input-icon">
                                        <span class="input-icon-addon"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg></span>
                                        <input type="text" id="catalog-search" class="form-control" placeholder="Search product or SKU...">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <select id="catalog-category" class="form-select">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 text-end">
                                    <button type="button" id="reset-catalog-filters" class="btn btn-ghost-secondary w-100">Reset Filters</button>
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

@include('erp.parties._address_modal')
@include('erp.parties._edit_address_modal')
@include('erp.parties._checkout_modal')

<div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="offcanvasCart" style="width: 400px;">
    <div class="offcanvas-header bg-dark text-white">
        <h2 class="offcanvas-title h3 m-0">Active Cart</h2>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0 d-flex flex-column" id="cart-content-container">
        @include('erp.parties._cart_content')
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const profileContainer = document.getElementById('v-pills-products');
    
    // Persist Tab State
    const triggerTabList = [].slice.call(document.querySelectorAll('#v-pills-tab button[data-bs-toggle="pill"]'));
    triggerTabList.forEach(function (triggerEl) {
        triggerEl.addEventListener('shown.bs.tab', function (event) {
            localStorage.setItem('activeProfileTab_' + window.location.pathname, event.target.id);
        });
    });
    
    const activeTabId = localStorage.getItem('activeProfileTab_' + window.location.pathname);
    if (activeTabId) {
        const activeTab = document.getElementById(activeTabId);
        if (activeTab) {
            const tab = new bootstrap.Tab(activeTab);
            tab.show();
        }
    }
    
    // Quantity Plus/Minus Buttons (for Catalog and Cart)
    document.body.addEventListener('click', function (e) {
        if (e.target.closest('.qty-plus') || e.target.closest('.cart-qty-plus')) {
            const btn = e.target.closest('.qty-plus') || e.target.closest('.cart-qty-plus');
            const input = btn.previousElementSibling;
            input.value = parseInt(input.value || 0) + 1;
            
            // If in cart, auto-submit the form
            if (btn.classList.contains('cart-qty-plus')) {
                btn.closest('form').dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
            }
        } else if (e.target.closest('.qty-minus') || e.target.closest('.cart-qty-minus')) {
            const btn = e.target.closest('.qty-minus') || e.target.closest('.cart-qty-minus');
            const input = btn.nextElementSibling;
            if (parseInt(input.value || 0) > 1) {
                input.value = parseInt(input.value) - 1;
                
                // If in cart, auto-submit the form
                if (btn.classList.contains('cart-qty-minus')) {
                    btn.closest('form').dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                }
            }
        }
    });

    // Add to Cart AJAX
    document.body.addEventListener('submit', function (e) {
        if (e.target.classList.contains('cart-add-form')) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const actionUrl = form.getAttribute('action');
            
            fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('cart-content-container').innerHTML = data.cartHtml;
                    document.getElementById('header-cart-count').innerText = data.cartCount + ' Items';
                    document.getElementById('header-cart-total').innerText = '₹ ' + data.cartTotal;
                    
                    // Reduce catalog quantity dynamically
                    const productId = form.querySelector('input[name="product_id"]').value;
                    const addedQty = parseInt(formData.get('quantity'));
                    const stockDisplay = document.querySelector(`.stock-display[data-product-id="${productId}"]`);
                    
                    if (stockDisplay) {
                        let currentStock = parseInt(stockDisplay.getAttribute('data-current-stock')) || 0;
                        currentStock -= addedQty;
                        stockDisplay.setAttribute('data-current-stock', currentStock);
                        stockDisplay.innerText = currentStock.toLocaleString();
                        
                        const minStock = parseInt(stockDisplay.getAttribute('data-min-stock')) || 0;
                        const stockContainer = stockDisplay.closest('.font-weight-bold');
                        if (currentStock <= minStock) {
                            stockContainer.classList.remove('text-success');
                            stockContainer.classList.add('text-danger');
                        } else {
                            stockContainer.classList.remove('text-danger');
                            stockContainer.classList.add('text-success');
                        }
                    }
                    
                    // Reset quantity input
                    form.querySelector('.product-qty').value = 1;
                } else {
                    alert(data.message || 'Error adding to cart');
                }
            })
            .catch(error => console.error('Error:', error));
        }
        
        // Remove from Cart AJAX
        if (e.target.classList.contains('cart-remove-form')) {
            e.preventDefault();
            const form = e.target;
            const actionUrl = form.getAttribute('action');
            
            fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: new FormData(form)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('cart-content-container').innerHTML = data.cartHtml;
                    document.getElementById('header-cart-count').innerText = data.cartCount + ' Items';
                    document.getElementById('header-cart-total').innerText = '₹ ' + data.cartTotal;
                    
                    // Restore catalog quantity dynamically
                    const formData = new FormData(form);
                    const productId = formData.get('product_id');
                    const removedQty = parseInt(formData.get('quantity'));
                    const stockDisplay = document.querySelector(`.stock-display[data-product-id="${productId}"]`);
                    
                    if (stockDisplay) {
                        let currentStock = parseInt(stockDisplay.getAttribute('data-current-stock')) || 0;
                        currentStock += removedQty;
                        stockDisplay.setAttribute('data-current-stock', currentStock);
                        stockDisplay.innerText = currentStock.toLocaleString();
                        
                        const minStock = parseInt(stockDisplay.getAttribute('data-min-stock')) || 0;
                        const stockContainer = stockDisplay.closest('.font-weight-bold');
                        if (currentStock <= minStock) {
                            stockContainer.classList.remove('text-success');
                            stockContainer.classList.add('text-danger');
                        } else {
                            stockContainer.classList.remove('text-danger');
                            stockContainer.classList.add('text-success');
                        }
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Update Cart AJAX
        if (e.target.classList.contains('cart-update-form')) {
            e.preventDefault();
            const form = e.target;
            const actionUrl = form.getAttribute('action');
            
            fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: new FormData(form)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('cart-content-container').innerHTML = data.cartHtml;
                    document.getElementById('header-cart-count').innerText = data.cartCount + ' Items';
                    document.getElementById('header-cart-total').innerText = '₹ ' + data.cartTotal;
                    // Trigger catalog refresh for accuracy since we don't know the delta easily here
                    if(typeof updateCatalog === 'function') updateCatalog();
                } else {
                    alert(data.message || 'Error updating cart');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });

    // Catalog AJAX Filtering
    const searchInput = document.getElementById('catalog-search');
    const categorySelect = document.getElementById('catalog-category');
    const resetBtn = document.getElementById('reset-catalog-filters');
    const catalogContainer = document.getElementById('catalog-table-container');

    function fetchCatalog(url) {
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            catalogContainer.innerHTML = html;
        })
        .catch(error => console.error('Error fetching catalog:', error));
    }

    function updateCatalog() {
        const search = searchInput ? searchInput.value : '';
        const category = categorySelect ? categorySelect.value : '';
        
        const url = new URL(window.location.href);
        if(search) url.searchParams.set('search', search);
        else url.searchParams.delete('search');
        
        if(category) url.searchParams.set('category', category);
        else url.searchParams.delete('category');
        
        fetchCatalog(url.toString());
    }

    if (searchInput) {
        let debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(updateCatalog, 400);
        });
    }

    if (categorySelect) {
        categorySelect.addEventListener('change', updateCatalog);
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            searchInput.value = '';
            categorySelect.value = '';
            updateCatalog();
        });
    }

    // Catalog Pagination AJAX
    document.body.addEventListener('click', function(e) {
        const target = e.target.closest('#catalog-pagination a');
        if (target) {
            e.preventDefault();
            fetchCatalog(target.href);
        }
    });
});
</script>
@endpush
