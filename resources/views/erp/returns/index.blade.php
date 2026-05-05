@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Returns</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="{{ route('erp.returns.export', request()->all()) }}" id="btn-export" class="btn btn-outline-secondary d-none d-sm-inline-block">
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
                <div class="font-weight-medium h3 mb-0">{{ $stats['total'] }}</div>
                <div class="text-secondary small">Total Returns</div>
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
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 14l6 0" /><path d="M9 17l6 0" /><path d="M9 11l2 0" /></svg>
                </span>
              </div>
              <div class="col">
                <div class="font-weight-medium h3 mb-0">{{ $stats['sale_returns'] }}</div>
                <div class="text-secondary small">Sale Returns</div>
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
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 14l6 0" /><path d="M9 17l6 0" /><path d="M9 11l2 0" /></svg>
                </span>
              </div>
              <div class="col">
                <div class="font-weight-medium h3 mb-0">{{ $stats['purchase_returns'] }}</div>
                <div class="text-secondary small">Purchase Returns</div>
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
                <span class="bg-red text-white avatar shadow-sm">
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
    </div>

    <div class="card">
      <div class="card-body border-bottom py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap g-3">
          <div class="d-flex align-items-center flex-wrap g-2">
            <form id="bulk-action-form" action="{{ route('erp.returns.bulk-action') }}" method="POST" class="d-flex align-items-center me-3" style="display: none !important;">
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
              <input type="text" id="ajax-search" class="form-control form-control-sm" placeholder="Search return #, order #, party..." aria-label="Search returns">
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
            <select id="status-filter" class="form-select form-select-sm w-auto">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
            </select>
          </div>
        </div>
      </div>
      <div id="table-container">
        @include('erp.returns._table')
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
    const dateRangeFilter = document.getElementById('date-range-filter');
    let searchTimeout = null;

    function fetchReturns(url = null) {
        if (!url) {
            url = new URL(window.location.href);
            url.searchParams.set('search', searchInput.value);
            url.searchParams.set('status', statusFilter.value);
            url.searchParams.set('date_range', dateRangeFilter.value);
            url.searchParams.delete('page');
        }

        // Update Export Link
        const exportBtn = document.getElementById('btn-export');
        if (exportBtn) {
            const exportUrl = new URL('{{ route("erp.returns.export") }}', window.location.origin);
            exportUrl.searchParams.set('search', searchInput.value);
            exportUrl.searchParams.set('status', statusFilter.value);
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
            searchTimeout = setTimeout(() => fetchReturns(), 500);
        });
    }

    if (statusFilter) statusFilter.addEventListener('change', () => fetchReturns());
    if (dateRangeFilter) dateRangeFilter.addEventListener('change', () => fetchReturns());

    // Select All functionality
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'select-all') {
            const isChecked = e.target.checked;
            tableContainer.querySelectorAll('.return-checkbox').forEach(cb => {
                cb.checked = isChecked;
            });
            toggleBulkForm();
        }
    });

    // Toggle bulk action form visibility
    function toggleBulkForm() {
        const selectedCheckboxes = tableContainer.querySelectorAll('.return-checkbox:checked');
        if (bulkForm) {
            if (selectedCheckboxes.length > 0) {
                bulkForm.style.setProperty('display', 'flex', 'important');
            } else {
                bulkForm.style.setProperty('display', 'none', 'important');
            }
        }
    }

    tableContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('return-checkbox')) {
            toggleBulkForm();
        }
    });

    const observer = new MutationObserver(toggleBulkForm);
    observer.observe(tableContainer, { childList: true, subtree: true });

    // Bulk Action Submission
    if (bulkForm) {
        bulkForm.addEventListener('submit', function (e) {
            const selectedCheckboxes = tableContainer.querySelectorAll('.return-checkbox:checked');
            const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

            if (selectedIds.length === 0) {
                e.preventDefault();
                alert('Please select at least one return.');
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
