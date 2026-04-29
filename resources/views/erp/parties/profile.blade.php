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
                            Account Dossier
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
                    <div class="row row-cards">
                        <!-- Identity Section -->
                        <div class="col-md-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light"><h3 class="card-title">Identity & Contacts</h3></div>
                                <div class="card-body">
                                    <div class="datagrid">
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Full Name</div>
                                            <div class="datagrid-content fw-bold text-primary">{{ $party->name }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Secondary Phone</div>
                                            <div class="datagrid-content">{{ $party->phone_number_2 ?? '---' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Relative Contact</div>
                                            <div class="datagrid-content">{{ $party->relative_phone ?? '---' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Email</div>
                                            <div class="datagrid-content">{{ $party->email ?? '---' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Aadhaar (L4)</div>
                                            <div class="datagrid-content">{{ $party->aadhaar_last4 ?? '---' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">KYC Status</div>
                                            <div class="datagrid-content">
                                                @if($party->kyc_completed)
                                                    <span class="badge bg-success-lt">Verified</span>
                                                @else
                                                    <span class="badge bg-warning-lt">Pending</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Source</div>
                                            <div class="datagrid-content">{{ $party->source ?? '---' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Referred By</div>
                                            <div class="datagrid-content">{{ $party->referred_by ?? '---' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Business Section -->
                        <div class="col-md-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light"><h3 class="card-title">Business & Financial Info</h3></div>
                                <div class="card-body">
                                    <div class="datagrid">
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Farm / Company</div>
                                            <div class="datagrid-content">{{ $party->company_name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">GSTIN / PAN</div>
                                            <div class="datagrid-content"><code>{{ $party->gstin ?? '---' }}</code> / {{ $party->pan_number ?? '---' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Credit Limit</div>
                                            <div class="datagrid-content">₹ {{ number_format($party->credit_limit, 2) }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Credit Valid Till</div>
                                            <div class="datagrid-content text-danger">{{ $party->credit_valid_till ? $party->credit_valid_till->format('d M Y') : 'Life-time' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Payment Terms</div>
                                            <div class="datagrid-content">{{ $party->payment_terms ?? 'Standard' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Ledger Group</div>
                                            <div class="datagrid-content">{{ $party->ledger_group ?? 'Sundry Debtors' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Banking Section -->
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-light"><h3 class="card-title">Banking Details</h3></div>
                                <div class="card-body">
                                    <div class="datagrid">
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Bank Name</div>
                                            <div class="datagrid-content fw-bold">{{ $party->bank_name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Account No</div>
                                            <div class="datagrid-content">{{ $party->account_number ?? 'N/A' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">IFSC / Branch</div>
                                            <div class="datagrid-content text-secondary">{{ $party->ifsc_code ?? '---' }} / {{ $party->branch_name ?? '---' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Agriculture Profile -->
                        @if($party->type == 'farmer')
                        <div class="col-md-6">
                            <div class="card bg-green-lt border-0 shadow-sm h-100">
                                <div class="card-header"><h3 class="card-title text-green">Agriculture Profile</h3></div>
                                <div class="card-body">
                                    <div class="datagrid">
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Land Area</div>
                                            <div class="datagrid-content fw-bold">{{ $party->land_area }} {{ ucfirst($party->land_unit) }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Irrigation</div>
                                            <div class="datagrid-content">{{ ucfirst($party->irrigation_type) ?? 'Rainfed' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Crops</div>
                                            <div class="datagrid-content">
                                                @if($party->crops)
                                                    @foreach($party->crops as $crop)
                                                        <span class="badge bg-white text-green border border-green">{{ $crop }}</span>
                                                    @endforeach
                                                @else
                                                    ---
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Addresses -->
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header">
                                    <h3 class="card-title">Verified Addresses</h3>
                                    <div class="card-actions">
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-add-address">Add New</button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row row-cards">
                                        @forelse($party->addresses as $address)
                                        <div class="col-md-6">
                                            <div class="card card-sm border shadow-none bg-light">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="subheader text-primary">{{ ucfirst($address->label ?? $address->type) }}</div>
                                                        @if($address->is_default)
                                                            <span class="badge bg-blue-lt ms-2">Default</span>
                                                        @endif
                                                    </div>
                                                    <address class="m-0 text-secondary">
                                                        {{ $address->address_line1 }}<br>
                                                        {{ $address->village ? $address->village.', ' : '' }}{{ $address->taluka ? $address->taluka.', ' : '' }}{{ $address->district }}<br>
                                                        {{ $address->state }} - {{ $address->pincode }}
                                                    </address>
                                                </div>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="col-12 text-center py-4 text-muted">No saved addresses found.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Internal Notes -->
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light"><h3 class="card-title">Internal Notes</h3></div>
                                <div class="card-body text-secondary">
                                    {{ $party->internal_notes ?? 'No internal notes registered for this account.' }}
                                </div>
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

@include('erp.parties._checkout_modal')

<div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="offcanvasCart" style="width: 400px;">
    <div class="offcanvas-header bg-dark text-white">
        <h2 class="offcanvas-title h3 m-0">Active Cart</h2>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0" id="cart-content-container">
        @include('erp.parties._cart_content')
    </div>
</div>

@endsection
