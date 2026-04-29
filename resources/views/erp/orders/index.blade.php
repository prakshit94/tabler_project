@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">{{ ucfirst($type) }} Orders</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <button type="button" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-search-customer">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
            New {{ ucfirst($type) }} Order
          </button>
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
            <form id="bulk-action-form" action="{{ route('erp.orders.bulk-action') }}" method="POST" class="d-flex align-items-center me-3">
              @csrf
              <select name="action" class="form-select form-select-sm w-auto me-2" id="bulk-action-select" required>
                <option value="">Bulk Actions</option>
                @if($view === 'active')
                  <option value="delete">Move to Trash</option>
                @else
                  <option value="restore">Restore Selected</option>
                  <option value="force-delete">Permanently Delete</option>
                @endif
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
            <select id="status-filter" class="form-select form-select-sm w-auto me-2">
                <option value="">All Statuses</option>
                @foreach(\App\Models\Order::STATUSES as $st)
                  <option value="{{ $st }}" @selected($status === $st)>{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
                @endforeach
            </select>
            <select id="warehouse-filter" class="form-select form-select-sm w-auto">
              <option value="">All Warehouses</option>
              @foreach($warehouses as $wh)
              <option value="{{ $wh->id }}">{{ $wh->name }}</option>
              @endforeach
            </select>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableContainer = document.getElementById('table-container');
    const bulkForm = document.getElementById('bulk-action-form');
    const searchInput = document.getElementById('ajax-search');
    const statusFilter = document.getElementById('status-filter');
    const warehouseFilter = document.getElementById('warehouse-filter');
    let searchTimeout = null;

    // AJAX Fetching
    function fetchOrders(url = null) {
        if (!url) {
            url = new URL(window.location.href);
            url.searchParams.set('search', searchInput.value);
            url.searchParams.set('status', statusFilter.value);
            url.searchParams.set('warehouse_id', warehouseFilter.value);
            url.searchParams.set('type', '{{ $type }}');
            url.searchParams.set('view', '{{ $view }}');
            url.searchParams.delete('page');
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
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

            if (!confirm('Are you sure you want to perform this bulk action?')) {
                e.preventDefault();
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
});
</script>
@endpush
