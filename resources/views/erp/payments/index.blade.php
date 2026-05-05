@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Payments</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="{{ route('erp.payments.export', request()->all()) }}" id="btn-export" class="btn btn-outline-secondary d-none d-sm-inline-block">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>
            Export CSV
          </a>
        </div>
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
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 8l0 4l2 2" /></svg>
                </span>
              </div>
              <div class="col">
                <div class="font-weight-medium h3 mb-0">{{ $stats['total_count'] }}</div>
                <div class="text-secondary small">Total Payments</div>
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
                <div class="font-weight-medium h3 mb-0">₹ {{ number_format($stats['total_amount'], 2) }}</div>
                <div class="text-secondary small">Total Amount</div>
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
                <span class="bg-blue text-white avatar shadow-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M3 10l18 0" /><path d="M5 6l7 -3l7 3" /><path d="M4 10l0 11" /><path d="M20 10l0 11" /><path d="M8 14l0 3" /><path d="M12 14l0 3" /><path d="M16 14l0 3" /></svg>
                </span>
              </div>
              <div class="col">
                <div class="font-weight-medium h3 mb-0">₹ {{ number_format($stats['bank_amount'], 2) }}</div>
                <div class="text-secondary small">Bank / Online</div>
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
                <span class="bg-yellow text-white avatar shadow-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12l4 0" /><path d="M20 16l4 0" /><path d="M11 12l0 4" /><path d="M8 14l6 0" /></svg>
                </span>
              </div>
              <div class="col">
                <div class="font-weight-medium h3 mb-0">₹ {{ number_format($stats['cash_amount'], 2) }}</div>
                <div class="text-secondary small">Cash Payments</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body border-bottom py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap g-3">
          <div class="d-flex align-items-center flex-wrap g-2">
            <form id="bulk-action-form" action="{{ route('erp.payments.bulk-action') }}" method="POST" class="d-flex align-items-center me-3" style="display: none !important;">
              @csrf
              <select name="action" class="form-select form-select-sm w-auto me-2" id="bulk-action-select" required>
                <option value="">Bulk Actions</option>
                <option value="delete">Move to Trash</option>
              </select>
              <button type="submit" class="btn btn-sm btn-white">Apply</button>
            </form>
            
            <div class="input-icon">
              <span class="input-icon-addon">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
              </span>
              <input type="text" id="ajax-search" class="form-control form-control-sm" placeholder="Search payment #, invoice #, party..." aria-label="Search payments">
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
            <select id="method-filter" class="form-select form-select-sm w-auto">
                <option value="">All Methods</option>
                <option value="cash">Cash</option>
                <option value="bank">Bank</option>
                <option value="online">Online</option>
                <option value="cheque">Cheque</option>
            </select>
          </div>
        </div>
      </div>
      <div id="table-container">
        @include('erp.payments._table')
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
    const methodFilter = document.getElementById('method-filter');
    const dateRangeFilter = document.getElementById('date-range-filter');
    let searchTimeout = null;

    function fetchPayments(url = null) {
        if (!url) {
            url = new URL(window.location.href);
            url.searchParams.set('search', searchInput.value);
            url.searchParams.set('payment_method', methodFilter.value);
            url.searchParams.set('date_range', dateRangeFilter.value);
            url.searchParams.delete('page');
        }

        // Update Export Link
        const exportBtn = document.getElementById('btn-export');
        if (exportBtn) {
            const exportUrl = new URL('{{ route("erp.payments.export") }}', window.location.origin);
            exportUrl.searchParams.set('search', searchInput.value);
            exportUrl.searchParams.set('payment_method', methodFilter.value);
            exportUrl.searchParams.set('date_range', dateRangeFilter.value);
            exportBtn.href = exportUrl.toString();
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
            .then(res => res.text())
            .then(html => {
                tableContainer.innerHTML = html;
            });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => fetchPayments(), 500);
        });
    }

    if (methodFilter) methodFilter.addEventListener('change', () => fetchPayments());
    if (dateRangeFilter) dateRangeFilter.addEventListener('change', () => fetchPayments());

    // Select All functionality
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'select-all') {
            const isChecked = e.target.checked;
            tableContainer.querySelectorAll('.payment-checkbox').forEach(cb => {
                cb.checked = isChecked;
            });
            toggleBulkForm();
        }
    });

    // Toggle bulk action form visibility
    function toggleBulkForm() {
        const selectedCheckboxes = tableContainer.querySelectorAll('.payment-checkbox:checked');
        if (bulkForm) {
            if (selectedCheckboxes.length > 0) {
                bulkForm.style.setProperty('display', 'flex', 'important');
            } else {
                bulkForm.style.setProperty('display', 'none', 'important');
            }
        }
    }

    tableContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('payment-checkbox')) {
            toggleBulkForm();
        }
    });

    const observer = new MutationObserver(toggleBulkForm);
    observer.observe(tableContainer, { childList: true, subtree: true });

    // Bulk Action Submission
    if (bulkForm) {
        bulkForm.addEventListener('submit', function (e) {
            const selectedCheckboxes = tableContainer.querySelectorAll('.payment-checkbox:checked');
            const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

            if (selectedIds.length === 0) {
                e.preventDefault();
                alert('Please select at least one payment.');
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
