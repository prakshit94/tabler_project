@extends('layouts.tabler')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    .ts-control { border: 1px solid #dce1e7 !important; padding: 0.35rem 0.5rem !important; border-radius: 4px !important; min-height: 36px !important; }
    .ts-wrapper.multi.has-items .ts-control { padding-left: 8px !important; }
    .ts-dropdown { z-index: 1050 !important; }
    .ts-wrapper .ts-control .item { display: none !important; } /* Hide selected chips */
    .ts-wrapper.multi .ts-control > input { display: inline-block !important; opacity: 1 !important; position: relative !important; }
    
    /* CSS Checkbox Ticking */
    .ts-dropdown .option .form-check-input { pointer-events: none; }
    .ts-dropdown .option.selected .form-check-input {
        background-color: #066fd1 !important;
        border-color: #066fd1 !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='m6 10 3 3 6-6'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: center !important;
        background-size: 100% 100% !important;
    }
</style>
@endpush

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">{{ ucfirst($type) }} Orders</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          @if($type === 'purchase')
            <a href="{{ route('erp.orders.create', ['type' => 'purchase']) }}" class="btn btn-primary d-none d-sm-inline-block">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
              New Purchase Order
            </a>
          @else
            <button type="button" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-search-customer">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
              New {{ ucfirst($type) }} Order
            </button>
          @endif
          <a href="{{ route('erp.orders.export', request()->all()) }}" id="btn-export" class="btn btn-outline-secondary d-none d-sm-inline-block">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>
            Export CSV
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Search Customer Modal -->
<div class="modal modal-blur fade" id="modal-search-customer" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-body p-4 text-center">
        <div class="avatar avatar-xl bg-primary-lt text-primary mb-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
        </div>
        <h3>Find {{ $type === 'sale' ? 'Customer' : 'Vendor' }}</h3>
        <div class="text-secondary mb-4">Enter mobile number to start the {{ $type }} order.</div>
        <form action="{{ route('erp.parties.search-by-mobile') }}" method="GET">
          <input type="hidden" name="type" value="{{ $type }}">
          <div class="mb-3">
            <input type="text" name="mobile" class="form-control form-control-lg text-center font-weight-bold" placeholder="10-digit mobile" maxlength="10" required autofocus autocomplete="off">
          </div>
          <div class="row g-2">
            <div class="col">
              <button type="button" class="btn btn-link link-secondary w-100" data-bs-dismiss="modal">Cancel</button>
            </div>
            <div class="col">
              <button type="submit" class="btn btn-primary w-100">Next Step</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <!-- Dashboard Stats -->
    <div class="row row-cards mb-4">
      <div class="col-sm-6 col-lg-3">
        <div class="card card-sm shadow-sm border-0">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-auto">
                <span class="bg-primary text-white avatar shadow-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
                </span>
              </div>
              <div class="col">
                <div class="font-weight-medium h3 mb-0">{{ $stats['total'] }}</div>
                <div class="text-secondary small">Total {{ ucfirst($type) }} Orders</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card card-sm shadow-sm border-0">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-auto">
                <span class="bg-green text-white avatar shadow-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" /><path d="M12 3v3m0 12v3" /></svg>
                </span>
              </div>
              <div class="col">
                <div class="font-weight-medium h3 mb-0">₹ {{ number_format($stats['revenue'], 2) }}</div>
                <div class="text-secondary small">Total Revenue</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card card-sm shadow-sm border-0">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-auto">
                <span class="bg-orange text-white avatar shadow-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M12 7l0 5l3 3" /></svg>
                </span>
              </div>
              <div class="col">
                <div class="font-weight-medium h3 mb-0">{{ $stats['pending'] }}</div>
                <div class="text-secondary small">Pending Actions</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card card-sm shadow-sm border-0">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-auto">
                <span class="bg-azure text-white avatar shadow-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" /></svg>
                </span>
              </div>
              <div class="col">
                <div class="font-weight-medium h3 mb-0">{{ $stats['shipped'] }}</div>
                <div class="text-secondary small">Fulfilled Orders</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <div class="container-xl">

    <div class="card">
      <div class="card-header border-bottom-0 pb-0">
        <div class="d-flex justify-content-between align-items-center w-100">
            <ul class="nav nav-tabs card-header-tabs">
              <li class="nav-item">
                <a href="{{ route('erp.orders.index', ['type' => 'sale']) }}" class="nav-link {{ $type === 'sale' ? 'active' : '' }}">Sales Orders</a>
              </li>
              <li class="nav-item">
                <a href="{{ route('erp.orders.index', ['type' => 'purchase']) }}" class="nav-link {{ $type === 'purchase' ? 'active' : '' }}">Purchase Orders</a>
              </li>
            </ul>
            <ul class="nav nav-pills ms-auto mb-2">
                <li class="nav-item">
                    <a href="{{ route('erp.orders.index', ['type' => $type, 'view' => 'active']) }}" class="nav-link py-1 px-3 {{ $view === 'active' ? 'active' : '' }}">Active</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('erp.orders.index', ['type' => $type, 'view' => 'trash']) }}" class="nav-link py-1 px-3 {{ $view === 'trash' ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                        Trash
                    </a>
                </li>
            </ul>
        </div>
      </div>

      <div class="card-body border-bottom py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap g-3">
          <div class="d-flex align-items-center flex-wrap g-2">
            <form id="bulk-action-form" action="{{ route('erp.orders.bulk-action') }}" method="POST" class="d-flex align-items-center me-3" style="display: none !important;">
              @csrf
              <select name="action" class="form-select form-select-sm w-auto me-2" id="bulk-action-select" required>
                <option value="">Bulk Actions</option>
                @if($view === 'active')
                   <option value="change-status">Update Status</option>
                   <option value="delete">Move to Trash</option>
                   <option value="bulk-print-invoice">Bulk Print Invoices</option>
                   <option value="bulk-print-cod">Bulk Print COD</option>
                @else
                  <option value="restore">Restore Selected</option>
                  <option value="force-delete">Permanently Delete</option>
                @endif
              </select>
              <select name="status" class="form-select form-select-sm w-auto me-2 d-none" id="bulk-status-select">
                <option value="">Select Status</option>
                @foreach(\App\Models\Order::STATUSES as $st)
                  <option value="{{ $st }}">{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
                @endforeach
              </select>
              <button type="submit" class="btn btn-sm btn-white">Apply</button>
            </form>
            
            <div class="input-icon">
              <span class="input-icon-addon">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
              </span>
              <input type="text" id="ajax-search" class="form-control form-control-sm" placeholder="Search order #, party..." aria-label="Search orders">
            </div>
          </div>
          
          <div class="d-flex align-items-center flex-wrap g-2">
            <select id="date-range-filter" class="form-select form-select-sm w-auto me-2">
                <option value="">All Time</option>
                <option value="today" @selected($dateRange === 'today')>Today</option>
                <option value="yesterday" @selected($dateRange === 'yesterday')>Yesterday</option>
                <option value="this_week" @selected($dateRange === 'this_week')>This Week</option>
                <option value="this_month" @selected($dateRange === 'this_month')>This Month</option>
                <option value="this_year" @selected($dateRange === 'this_year')>This Year</option>
            </select>
            <select id="status-filter" class="form-select form-select-sm w-auto me-2">
                <option value="">All Statuses</option>
                @foreach(\App\Models\Order::STATUSES as $st)
                  <option value="{{ $st }}" @selected($status === $st)>{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
                @endforeach
            </select>
            <select id="warehouse-filter" class="form-select form-select-sm w-auto me-2">
              <option value="">All Warehouses</option>
              @foreach($warehouses as $wh)
              <option value="{{ $wh->id }}">{{ $wh->name }}</option>
              @endforeach
            </select>
          </div>
          
          <div class="row g-2 mt-2 w-100 align-items-end">
            <div class="col-md-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label mb-0 small text-secondary">States</label>
                <div class="d-flex gap-2">
                    <a href="javascript:void(0)" class="small text-primary" onclick="toggleAll('state', true)">Select All</a>
                    <a href="javascript:void(0)" class="small text-danger" onclick="toggleAll('state', false)">None</a>
                </div>
              </div>
              <select id="state-filter" class="form-select form-select-sm" multiple placeholder="States">
                @foreach($availableStates as $state)
                  <option value="{{ $state }}" @selected(in_array($state, $selectedStates))>{{ $state }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label mb-0 small text-secondary">Districts</label>
                <div class="d-flex gap-2">
                    <a href="javascript:void(0)" class="small text-primary" onclick="toggleAll('district', true)">Select All</a>
                    <a href="javascript:void(0)" class="small text-danger" onclick="toggleAll('district', false)">None</a>
                </div>
              </div>
              <select id="district-filter" class="form-select form-select-sm" multiple placeholder="Districts">
                @foreach($availableDistricts as $dist)
                  <option value="{{ $dist }}" @selected(in_array($dist, $selectedDistricts))>{{ $dist }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label mb-0 small text-secondary">Talukas</label>
                <div class="d-flex gap-2">
                    <a href="javascript:void(0)" class="small text-primary" onclick="toggleAll('taluka', true)">Select All</a>
                    <a href="javascript:void(0)" class="small text-danger" onclick="toggleAll('taluka', false)">None</a>
                </div>
              </div>
              <select id="taluka-filter" class="form-select form-select-sm" multiple placeholder="Talukas">
                @foreach($availableTalukas as $taluka)
                  <option value="{{ $taluka }}" @selected(in_array($taluka, $selectedTalukas))>{{ $taluka }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-sm btn-ghost-danger w-100" id="clear-location-filters">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M10 10l4 4m0 -4l-4 4" /></svg>
                    Reset All
                </button>
            </div>
          </div>
        </div>
      </div>
      <div id="table-container">
        @include('erp.orders._table')
      </div>
    </div>
  </div>
</div>

<!-- Order Details Modal -->
<div class="modal modal-blur fade" id="modal-order-details" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0" id="order-details-content">
        <div class="modal-body text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <div class="text-secondary mt-2">Loading order details...</div>
        </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableContainer = document.getElementById('table-container');
    const bulkForm = document.getElementById('bulk-action-form');
    const searchInput = document.getElementById('ajax-search');
    const statusFilter = document.getElementById('status-filter');
    const warehouseFilter = document.getElementById('warehouse-filter');
    const dateRangeFilter = document.getElementById('date-range-filter');
    let searchTimeout = null;

    // AJAX Fetching
    function fetchOrders(url) {
        const currentUrl = new URL(url || window.location.href);
        const params = new URLSearchParams(currentUrl.search);
        
        params.set('search', searchInput.value);
        params.set('status', statusFilter.value);
        params.set('warehouse_id', warehouseFilter.value);
        params.set('date_range', dateRangeFilter.value);
        params.set('type', '{{ $type }}');
        params.set('view', '{{ $view }}');
        
        // Add location filters
        params.delete('states[]');
        params.delete('districts[]');
        params.delete('talukas[]');
        
        tomSelectStates.getValue().forEach(s => params.append('states[]', s));
        tomSelectDistricts.getValue().forEach(d => params.append('districts[]', d));
        tomSelectTalukas.getValue().forEach(t => params.append('talukas[]', t));

        const fetchUrl = `${currentUrl.origin}${currentUrl.pathname}?${params.toString()}`;
        
        // Update browser URL without refreshing
        window.history.pushState({}, '', fetchUrl);

        // Update Export Link
        const exportBtn = document.getElementById('btn-export');
        if (exportBtn) {
            const exportUrl = new URL('{{ route("erp.orders.export") }}', window.location.origin);
            exportUrl.searchParams.set('search', searchInput.value);
            exportUrl.searchParams.set('status', statusFilter.value);
            exportUrl.searchParams.set('warehouse_id', warehouseFilter.value);
            exportUrl.searchParams.set('date_range', dateRangeFilter.value);
            exportUrl.searchParams.set('type', '{{ $type }}');
            exportUrl.searchParams.set('view', '{{ $view }}');
            exportBtn.href = exportUrl.toString();
        }

        fetch(fetchUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
            .then(res => res.text())
            .then(html => {
                tableContainer.innerHTML = html;
            });
    }

    // AJAX Search
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetchOrders();
            }, 500);
        });
    }

    // Warehouse & Status Filters
    if (warehouseFilter) {
        warehouseFilter.addEventListener('change', () => fetchOrders());
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', () => fetchOrders());
    }
    if (dateRangeFilter) {
        dateRangeFilter.addEventListener('change', () => fetchOrders());
    }

    // Order Details Modal Loading
    const orderDetailsModalEl = document.getElementById('modal-order-details');
    const orderDetailsContent = document.getElementById('order-details-content');
    let orderModalInstance = null;

    if (orderDetailsModalEl) {
        orderModalInstance = new bootstrap.Modal(orderDetailsModalEl);
    }

    tableContainer.addEventListener('click', function(e) {
        const viewBtn = e.target.closest('.view-order-btn');
        if (viewBtn) {
            e.preventDefault();
            const orderId = viewBtn.dataset.orderId;
            const url = `/erp/orders/${orderId}`;

            // Show loading state
            orderDetailsContent.innerHTML = `
                <div class="modal-body text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <div class="text-secondary">Fetching order details...</div>
                </div>
            `;

            if (orderModalInstance) {
                orderModalInstance.show();
            }

            fetch(url, { 
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                } 
            })
            .then(async res => {
                if (!res.ok) {
                    const errorText = await res.text();
                    // Try to find an error message in the response, otherwise use statusText
                    throw new Error(`Server Error ${res.status}: ${res.statusText}`);
                }
                return res.text();
            })
            .then(html => {
                orderDetailsContent.innerHTML = html;
            })
            .catch(err => {
                console.error('Error fetching order details:', err);
                orderDetailsContent.innerHTML = `
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">Fetch Error</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center py-5 text-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 17h.01" /></svg>
                        <div class="fw-bold mb-1">Failed to load order details</div>
                        <div class="text-secondary small">${err.message}</div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">Reload Page</button>
                        </div>
                    </div>
                `;
            });
        }
    });

    // Event Delegation for Pagination
    tableContainer.addEventListener('click', function (e) {
        const link = e.target.closest('#pagination-links a');
        if (link) {
            e.preventDefault();
            fetchOrders(link.href);
        }
    });

    // Select All functionality
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'select-all') {
            const isChecked = e.target.checked;
            tableContainer.querySelectorAll('.order-checkbox').forEach(cb => {
                cb.checked = isChecked;
            });
            toggleBulkForm();
        }
    });

    // Bulk Action Submission
    if (bulkForm) {
        bulkForm.addEventListener('submit', function (e) {
            const selectedCheckboxes = tableContainer.querySelectorAll('.order-checkbox:checked');
            const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

            if (selectedIds.length === 0) {
                e.preventDefault();
                alert('Please select at least one order.');
                return;
            }

            const actionSelect = document.getElementById('bulk-action-select');
            if (actionSelect.value === 'change-status') {
                const statusSelect = document.getElementById('bulk-status-select');
                if (!statusSelect.value) {
                    e.preventDefault();
                    alert('Please select a status to apply.');
                    return;
                }
            }

            if (!confirm('Are you sure you want to perform this bulk action?')) {
                e.preventDefault();
                return;
            }

            if (actionSelect.value === 'bulk-print-invoice') {
                e.preventDefault();
                window.open('{{ route("erp.invoices.bulk-print") }}?ids=' + selectedIds.join(','), '_blank');
                return;
            }
            if (actionSelect.value === 'bulk-print-cod') {
                e.preventDefault();
                window.open('{{ route("erp.orders.bulk-print-cod") }}?ids=' + selectedIds.join(','), '_blank');
                return;
            }

            bulkForm.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                bulkForm.appendChild(input);
            });
        });
    }

    // Toggle bulk status select visibility
    const bulkActionSelect = document.getElementById('bulk-action-select');
    const bulkStatusSelect = document.getElementById('bulk-status-select');
    if (bulkActionSelect && bulkStatusSelect) {
        bulkActionSelect.addEventListener('change', function() {
            if (this.value === 'change-status') {
                bulkStatusSelect.classList.remove('d-none');
                bulkStatusSelect.required = true;
            } else {
                bulkStatusSelect.classList.add('d-none');
                bulkStatusSelect.required = false;
            }
        });
    }

    // Toggle bulk action form visibility based on selection
    function toggleBulkForm() {
        const selectedCheckboxes = tableContainer.querySelectorAll('.order-checkbox:checked');
        if (bulkForm) {
            if (selectedCheckboxes.length > 0) {
                bulkForm.style.setProperty('display', 'flex', 'important');
            } else {
                bulkForm.style.setProperty('display', 'none', 'important');
            }
        }
    }

    tableContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('order-checkbox') || e.target.id === 'select-all') {
            toggleBulkForm();
        }
    });

    // Tom Select - Robust Toggling
    const getTsConfig = (placeholder) => ({
        plugins: ['remove_button'],
        maxOptions: 500,
        placeholder: placeholder,
        closeAfterSelect: false,
        hideSelected: false,
        onItemAdd: function() {
            this.setTextboxValue('');
            this.refreshOptions(false);
        },
        onOptionSelect: function(value, data) {
            if (this.items.includes(value)) {
                this.removeItem(value);
                this.refreshOptions(false);
                return false;
            }
        },
        render: {
            option: function(data, escape) {
                return `<div class="d-flex align-items-center py-1">
                    <input type="checkbox" class="form-check-input me-2 shadow-none" style="pointer-events: none;">
                    <span class="text-dark">${escape(data.text)}</span>
                </div>`;
            }
        },
        onInitialize: function() {
            const self = this;
            const countEl = document.createElement('div');
            countEl.className = 'ts-count small text-primary fw-bold';
            countEl.style.cssText = 'position:absolute; right:30px; top:50%; transform:translateY(-50%); pointer-events:none; z-index:5;';
            this.control.appendChild(countEl);

            const updateUI = () => {
                const count = self.getValue().length;
                countEl.innerText = count > 0 ? `${count} selected` : '';
                this.control_input.placeholder = count > 0 ? '' : placeholder;
            };

            this.on('change', updateUI);
            updateUI();
        }
    });

    const tomSelectStates = new TomSelect('#state-filter', getTsConfig('States'));
    const tomSelectDistricts = new TomSelect('#district-filter', getTsConfig('Districts'));
    const tomSelectTalukas = new TomSelect('#taluka-filter', getTsConfig('Talukas'));

    window.toggleAll = function(field, isSelectAll) {
        let ts = null;
        if (field === 'state') ts = tomSelectStates;
        if (field === 'district') ts = tomSelectDistricts;
        if (field === 'taluka') ts = tomSelectTalukas;
        
        if (ts) {
            if (isSelectAll) {
                ts.setValue(Object.keys(ts.options));
            } else {
                ts.clear();
            }
            // Trigger filter once
            fetchOrders();
        }
    };

    document.getElementById('clear-location-filters').addEventListener('click', function() {
        tomSelectStates.clear(true);
        tomSelectDistricts.clear(true);
        tomSelectTalukas.clear(true);
        fetchOrders();
    });

    async function updateLocationFilters(triggeringField) {
        const states = tomSelectStates.getValue();
        const districts = tomSelectDistricts.getValue();
        const talukas = tomSelectTalukas.getValue();

        const queryParams = new URLSearchParams();
        states.forEach(s => queryParams.append('states[]', s));
        districts.forEach(d => queryParams.append('districts[]', d));

        try {
            const response = await fetch(`{{ route('erp.orders.filter-locations') }}?${queryParams.toString()}`);
            const data = await response.json();

            if (triggeringField === 'state') {
                tomSelectDistricts.clearOptions();
                data.districts.forEach(d => tomSelectDistricts.addOption({value: d, text: d}));
                // Keep values that are still available
                const validDistricts = districts.filter(d => data.districts.includes(d));
                tomSelectDistricts.setValue(validDistricts, true);
            }

            if (triggeringField === 'state' || triggeringField === 'district') {
                tomSelectTalukas.clearOptions();
                data.talukas.forEach(t => tomSelectTalukas.addOption({value: t, text: t}));
                const validTalukas = talukas.filter(t => data.talukas.includes(t));
                tomSelectTalukas.setValue(validTalukas, true);
            }
        } catch (err) {
            console.error('Error updating locations:', err);
        }
        
        fetchOrders();
    }

    tomSelectStates.on('change', () => updateLocationFilters('state'));
    tomSelectDistricts.on('change', () => updateLocationFilters('district'));
    tomSelectTalukas.on('change', () => fetchOrders());

    // Also run after AJAX fetch to reset if needed
    const observer = new MutationObserver(toggleBulkForm);
    observer.observe(tableContainer, { childList: true, subtree: true });
});
</script>
@endpush
