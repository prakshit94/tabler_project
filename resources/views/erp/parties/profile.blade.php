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
                    <style>
                        #v-pills-tab .nav-link {
                            transition: all 0.2s ease;
                            border-radius: 0;
                            border-left: 3px solid transparent !important;
                        }
                        #v-pills-tab .nav-link.active {
                            background-color: var(--tblr-primary-lt) !important;
                            color: var(--tblr-primary) !important;
                            border-left-color: var(--tblr-primary) !important;
                            font-weight: 600;
                        }
                        #v-pills-tab .nav-link:hover:not(.active) {
                            background-color: var(--tblr-bg-surface-secondary);
                            border-left-color: var(--tblr-border-color);
                        }
                    </style>
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
                        <button class="nav-link text-start py-3 px-4 border-0 d-none" id="v-pills-checkout-tab" data-bs-toggle="pill" data-bs-target="#v-pills-checkout" type="button" role="tab">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
                            Review Order
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
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-md bg-primary-lt text-primary me-3">
                                    {{ strtoupper(substr($party->first_name ?? 'P', 0, 1)) }}
                                </div>
                                <div>
                                    <h3 class="card-title mb-0">Primary Identity & Contact</h3>
                                    <div class="text-secondary small">Basic information and contact details</div>
                                </div>
                            </div>
                            <button class="btn btn-outline-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modal-edit-profile">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                                Edit Profile
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="datagrid">
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Full Name</div>
                                    <div class="datagrid-content fw-bold">{{ $party->name }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Party Code</div>
                                    <div class="datagrid-content"><code>{{ $party->party_code }}</code></div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Primary Mobile</div>
                                    <div class="datagrid-content text-primary fw-bold">{{ $party->mobile }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Email Address</div>
                                    <div class="datagrid-content">{{ $party->email ?? 'Not Provided' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Account Type</div>
                                    <div class="datagrid-content">
                                        <span class="badge bg-blue-lt">{{ ucfirst($party->type) }}</span>
                                    </div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Category</div>
                                    <div class="datagrid-content">
                                        <span class="badge bg-purple-lt">{{ ucfirst($party->category) }}</span>
                                    </div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Secondary Phone</div>
                                    <div class="datagrid-content">{{ $party->phone_number_2 ?? '—' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Relative Phone</div>
                                    <div class="datagrid-content">{{ $party->relative_phone ?? '—' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Aadhaar (Last 4)</div>
                                    <div class="datagrid-content">{{ $party->aadhaar_last4 ?? '—' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Source / Referred By</div>
                                    <div class="datagrid-content small text-muted">{{ $party->source ?? 'Direct' }} {{ $party->referred_by ? '('.$party->referred_by.')' : '' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <!-- Agriculture Portfolio -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-green-lt py-2">
                                    <h4 class="card-title text-green"><svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>Agriculture Portfolio</h4>
                                </div>
                                <div class="card-body">
                                    <div class="datagrid">
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Land Area</div>
                                            <div class="datagrid-content fw-bold">{{ $party->land_area ?? '0' }} {{ ucfirst($party->land_unit ?? 'Acre') }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Irrigation</div>
                                            <div class="datagrid-content">{{ ucfirst($party->irrigation_type) ?? 'Rainfed' }}</div>
                                        </div>
                                        <div class="datagrid-item col-12">
                                            <div class="datagrid-title">Crops Portfolio</div>
                                            <div class="datagrid-content">
                                                @if($party->crops)
                                                    @foreach($party->crops as $crop)
                                                        <span class="badge bg-green-lt me-1">{{ $crop }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted small">No crops registered</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Financial & Status -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-yellow-lt py-2">
                                    <h4 class="card-title text-yellow"><svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg>Credit & Status</h4>
                                </div>
                                <div class="card-body">
                                    <div class="datagrid">
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Credit Limit</div>
                                            <div class="datagrid-content">₹ {{ number_format($party->credit_limit, 2) }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Opening Balance</div>
                                            <div class="datagrid-content">₹ {{ number_format($party->opening_balance, 2) }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Credit Valid Till</div>
                                            <div class="datagrid-content">{{ $party->credit_valid_till ? $party->credit_valid_till->format('d M Y') : 'Life-time' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">KYC Status</div>
                                            <div class="datagrid-content">
                                                @if($party->kyc_completed)
                                                    <span class="status status-green"><span class="status-dot status-dot-animated"></span> Verified</span>
                                                @else
                                                    <span class="status status-yellow">Pending Review</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($party->category == 'business' || $party->gstin || $party->company_name)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-azure-lt py-3">
                            <h3 class="card-title text-azure">Business & Banking Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-7 border-end">
                                    <div class="datagrid">
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Company / Farm Name</div>
                                            <div class="datagrid-content fw-bold">{{ $party->company_name ?? '—' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">GSTIN</div>
                                            <div class="datagrid-content font-monospace text-uppercase">{{ $party->gstin ?? '—' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">PAN Number</div>
                                            <div class="datagrid-content font-monospace text-uppercase">{{ $party->pan_number ?? '—' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Ledger Group</div>
                                            <div class="datagrid-content">{{ $party->ledger_group ?? 'Sundry Debtors' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Payment Terms</div>
                                            <div class="datagrid-content">{{ $party->payment_terms ?? 'Standard' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 ps-md-4">
                                    <div class="datagrid">
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Bank Name</div>
                                            <div class="datagrid-content">{{ $party->bank_name ?? '—' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Account Number</div>
                                            <div class="datagrid-content font-monospace">{{ $party->account_number ?? '—' }}</div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">IFSC / Branch</div>
                                            <div class="datagrid-content font-monospace small">
                                                {{ $party->ifsc_code ?? '—' }} <br>
                                                <span class="text-muted">{{ $party->branch_name ?? '' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-surface-secondary">
                            <h3 class="card-title">Registration Activity & Notes</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-8 border-end">
                                    <div class="subheader mb-2">Internal Notes</div>
                                    <div class="p-3 bg-surface-secondary rounded text-secondary italic">
                                        {{ $party->internal_notes ?? 'No internal registration notes found for this account.' }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="datagrid">
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Orders Count</div>
                                            <div class="datagrid-content"><span class="badge bg-blue text-blue-fg">{{ $party->orders_count }}</span></div>
                                        </div>
                                        <div class="datagrid-item">
                                            <div class="datagrid-title">Last Activity</div>
                                            <div class="datagrid-content small">{{ $party->last_purchase_at ? $party->last_purchase_at->format('d M Y') : 'No Transactions' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Saved Addresses</h3>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-add-address">Add New Address</button>
                        </div>
                        <div class="card-body bg-surface-secondary">
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
                                                        <button onclick='editAddress(@json($address))' class="btn btn-sm btn-ghost-primary border-0 p-1" title="Edit Address">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                                                        </button>
                                                        <form action="{{ route('erp.parties.addresses.destroy', [$party->id, $address->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this address?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-ghost-danger border-0 p-1" title="Delete Address">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                                            </button>
                                                        </form>
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
                                                @if($address->address_line1)<strong>Line 1:</strong> {{ $address->address_line1 }}<br>@endif
                                                @if($address->address_line2)<strong>Line 2:</strong> {{ $address->address_line2 }}<br>@endif
                                                @if($address->village)<strong>Village:</strong> {{ $address->village }}<br>@endif
                                                @if($address->taluka)<strong>Taluka:</strong> {{ $address->taluka }}<br>@endif
                                                @if($address->district)<strong>District:</strong> {{ $address->district }}<br>@endif
                                                @if($address->state)<strong>State:</strong> {{ $address->state }}<br>@endif
                                                @if($address->post_office)<strong>PO:</strong> {{ $address->post_office }}<br>@endif
                                                @if($address->pincode)<strong>Pincode:</strong> {{ $address->pincode }}<br>@endif
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
                                    <div class="empty bg-surface rounded shadow-sm border-0">
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

                <!-- Checkout Section -->
                <div class="tab-pane fade" id="v-pills-checkout" role="tabpanel">
                    @include('erp.parties._checkout_tab')
                </div>
            </div>
        </div>
    </div>
</div>

@include('erp.parties._address_modal')
@include('erp.parties._edit_address_modal')
@include('erp.parties._checkout_modal')
@include('erp.parties._edit_profile_modal')

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
    
    // Persist Tab State via URL Hash for refresh persistence
    const triggerTabList = [].slice.call(document.querySelectorAll('#v-pills-tab button[data-bs-toggle="pill"]'));
    triggerTabList.forEach(function (triggerEl) {
        triggerEl.addEventListener('shown.bs.tab', function (event) {
            const hash = event.target.getAttribute('data-bs-target');
            if (hash) {
                history.replaceState(null, null, hash);
            }
        });
    });
    
    // Priority: Session active_tab > URL Hash > Default (Profile)
    let activeTabId = @if(session('active_tab')) "{{ session('active_tab') }}" @else null @endif;
    
    if (!activeTabId && window.location.hash) {
        const hash = window.location.hash; // includes #
        const tabByHash = document.querySelector(`button[data-bs-target="${hash}"]`);
        if (tabByHash) activeTabId = tabByHash.id;
    }

    if (activeTabId) {
        const activeTab = document.getElementById(activeTabId);
        if (activeTab) {
            // Unhide checkout tab if it's the active one (e.g. after review click)
            if (activeTabId === 'v-pills-checkout-tab') {
                activeTab.classList.remove('d-none');
            }
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

        // Reset to first page when filtering
        url.searchParams.delete('products_page');
        
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

    // Custom trigger for Checkout Tab from Sidebar
    window.switchToCheckoutTab = function() {
        const checkoutTabBtn = document.getElementById('v-pills-checkout-tab');
        if (checkoutTabBtn) {
            // Reveal the tab if hidden
            checkoutTabBtn.classList.remove('d-none');
            
            // Persist the intent to open checkout after reload
            localStorage.setItem('activeProfileTab_' + window.location.pathname, checkoutTabBtn.id);
            
            // Close Offcanvas
            const offcanvasEl = document.getElementById('offcanvasCart');
            const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl);
            if (offcanvas) offcanvas.hide();

            // Refresh the page to ensure fresh cart data in the checkout tab
            window.location.reload();
        }
    };

    // Listen for cart changes and refresh checkout tab if active
    const observer = new MutationObserver(function(mutations) {
        const checkoutTab = document.getElementById('v-pills-checkout');
        if (checkoutTab && checkoutTab.classList.contains('active')) {
            // If the cart content in sidebar changed, and we are on checkout tab, 
            // we should probably refresh the page to keep totals in sync.
            // To avoid infinite loops, we only do this on specific actions.
        }
    });
    
    const cartContainer = document.getElementById('cart-content-container');
    if (cartContainer) observer.observe(cartContainer, { childList: true });

    // Handle AJAX form success globally for checkout tab sync
    const originalFetch = window.fetch;
    window.fetch = function() {
        const args = arguments;
        return originalFetch.apply(this, args).then(response => {
            // Check if it's a mutation-like request that might require a sync
            const options = args[1] || {};
            const method = (options.method || 'GET').toUpperCase();
            
            if (response.ok && ['POST', 'PUT', 'PATCH', 'DELETE'].includes(method)) {
                const checkoutTab = document.getElementById('v-pills-checkout');
                if (checkoutTab && checkoutTab.classList.contains('active')) {
                    // Only reload if we are on the checkout tab to keep it in sync
                    setTimeout(() => window.location.reload(), 500);
                }
            }
            return response;
        });
    };
});
</script>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
@endpush
