@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Invoices</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <button type="button" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-import-payments">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg>
            Import Bulk Payments
          </button>
          <a href="{{ route('erp.invoices.export', request()->all()) }}" id="btn-export" class="btn btn-outline-secondary d-none d-sm-inline-block">
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
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 14l6 0" /><path d="M9 17l6 0" /><path d="M9 11l2 0" /></svg>
                </span>
              </div>
              <div class="col">
                <div class="font-weight-medium h3 mb-0">{{ $stats['total'] }}</div>
                <div class="text-secondary small">Total Invoices</div>
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
                <span class="bg-red text-white avatar shadow-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M12 7l0 5l3 3" /></svg>
                </span>
              </div>
              <div class="col">
                <div class="font-weight-medium h3 mb-0">{{ $stats['unpaid_count'] }}</div>
                <div class="text-secondary small">Unpaid Invoices</div>
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
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M12 7l0 5l3 3" /></svg>
                </span>
              </div>
              <div class="col">
                <div class="font-weight-medium h3 mb-0">₹ {{ number_format($stats['unpaid_amount'], 2) }}</div>
                <div class="text-secondary small">Pending Amount</div>
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
            <form id="bulk-action-form" action="{{ route('erp.invoices.bulk-action') }}" method="POST" class="d-flex align-items-center me-3" style="display: none !important;">
              @csrf
              <select name="action" class="form-select form-select-sm w-auto me-2" id="bulk-action-select" required>
                <option value="">Bulk Actions</option>
                <option value="change-status">Update Status</option>
                <option value="delete">Move to Trash</option>
              </select>
              <select name="status" class="form-select form-select-sm w-auto me-2 d-none" id="bulk-status-select">
                <option value="">Select Status</option>
                <option value="unpaid">Unpaid</option>
                <option value="partial">Partial</option>
                <option value="paid">Paid</option>
              </select>
              <button type="submit" class="btn btn-sm btn-white">Apply</button>
            </form>
            
            <div class="input-icon">
              <span class="input-icon-addon">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
              </span>
              <input type="text" id="ajax-search" class="form-control form-control-sm" placeholder="Search invoice #, party..." aria-label="Search invoices">
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
                <option value="unpaid">Unpaid</option>
                <option value="partial">Partial</option>
                <option value="paid">Paid</option>
            </select>
          </div>
        </div>
      </div>
      <div id="table-container">
        @include('erp.invoices._table')
      </div>
    </div>
  </div>
</div>

<!-- Import Payments Modal -->
<div class="modal modal-blur fade" id="modal-import-payments" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header">
        <h5 class="modal-title">Import Bulk Payments (CSV)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Upload CSV File</label>
          <input type="file" id="csv-file-input" class="form-control" accept=".csv">
          <div class="small text-muted mt-1">
            CSV Format: <code>invoice_number, amount, date(YYYY-MM-DD), method(cash/bank/online), reference</code>
          </div>
        </div>
        
        <div id="csv-preview-container" style="display: none;">
          <div class="table-responsive" style="max-height: 400px;">
            <table class="table table-vcenter card-table" id="preview-table">
              <thead>
                <tr>
                  <th>Invoice #</th>
                  <th>Party</th>
                  <th>Amount</th>
                  <th>Date</th>
                  <th>Method</th>
                  <th>Reference</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody id="preview-body"></tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <a href="{{ route('erp.invoices.import-payments-template') }}" class="btn btn-outline-info me-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>
            Download Sample Template
        </a>
        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="btn-preview-csv" class="btn btn-primary">Preview Data</button>
        <button type="button" id="btn-process-import" class="btn btn-success d-none">Process Payments</button>
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

    function fetchInvoices(url = null) {
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
            const exportUrl = new URL('{{ route("erp.invoices.export") }}', window.location.origin);
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
            searchTimeout = setTimeout(() => fetchInvoices(), 500);
        });
    }

    if (statusFilter) statusFilter.addEventListener('change', () => fetchInvoices());
    if (dateRangeFilter) dateRangeFilter.addEventListener('change', () => fetchInvoices());

    // Select All functionality
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'select-all') {
            const isChecked = e.target.checked;
            tableContainer.querySelectorAll('.invoice-checkbox').forEach(cb => {
                cb.checked = isChecked;
            });
            toggleBulkForm();
        }
    });

    // Toggle bulk action form visibility
    function toggleBulkForm() {
        const selectedCheckboxes = tableContainer.querySelectorAll('.invoice-checkbox:checked');
        if (bulkForm) {
            if (selectedCheckboxes.length > 0) {
                bulkForm.style.setProperty('display', 'flex', 'important');
            } else {
                bulkForm.style.setProperty('display', 'none', 'important');
            }
        }
    }

    tableContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('invoice-checkbox')) {
            toggleBulkForm();
        }
    });

    const observer = new MutationObserver(toggleBulkForm);
    observer.observe(tableContainer, { childList: true, subtree: true });

    // Bulk Action Toggle Status Select
    const bulkActionSelect = document.getElementById('bulk-action-select');
    const bulkStatusSelect = document.getElementById('bulk-status-select');
    if (bulkActionSelect && bulkStatusSelect) {
        bulkActionSelect.addEventListener('change', function() {
            if (this.value === 'change-status') {
                bulkStatusSelect.classList.remove('d-none');
            } else {
                bulkStatusSelect.classList.add('d-none');
            }
        });
    }

    // Bulk Action Submission
    if (bulkForm) {
        bulkForm.addEventListener('submit', function (e) {
            const selectedCheckboxes = tableContainer.querySelectorAll('.invoice-checkbox:checked');
            const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

            if (selectedIds.length === 0) {
                e.preventDefault();
                alert('Please select at least one invoice.');
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

    // Import Payments Logic
    const importModal = document.getElementById('modal-import-payments');
    const csvFileInput = document.getElementById('csv-file-input');
    const previewBtn = document.getElementById('btn-preview-csv');
    const processBtn = document.getElementById('btn-process-import');
    const previewContainer = document.getElementById('csv-preview-container');
    const previewBody = document.getElementById('preview-body');
    let importedPaymentsData = [];

    if (previewBtn) {
        previewBtn.addEventListener('click', function() {
            const file = csvFileInput.files[0];
            if (!file) {
                alert('Please select a CSV file first.');
                return;
            }

            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');

            previewBtn.disabled = true;
            previewBtn.innerText = 'Analyzing...';

            fetch('{{ route("erp.invoices.import-payments-preview") }}', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                importedPaymentsData = data.data;
                renderPreview(data.data);
                previewBtn.innerText = 'Preview Data';
                previewBtn.disabled = false;
                processBtn.classList.remove('d-none');
                previewContainer.style.display = 'block';
            })
            .catch(err => {
                console.error(err);
                alert('Error parsing CSV file.');
                previewBtn.innerText = 'Preview Data';
                previewBtn.disabled = false;
            });
        });
    }

    function renderPreview(data) {
        previewBody.innerHTML = '';
        data.forEach(row => {
            const tr = document.createElement('tr');
            const statusBadge = row.status === 'valid' ? 'bg-green-lt' : (row.status === 'warning' ? 'bg-yellow-lt' : 'bg-red-lt');
            tr.innerHTML = `
                <td>${row.invoice_number}</td>
                <td>${row.party}</td>
                <td>₹ ${row.amount}</td>
                <td>${row.date}</td>
                <td><span class="badge bg-blue-lt">${row.method}</span></td>
                <td>${row.reference}</td>
                <td><span class="badge ${statusBadge}">${row.message}</span></td>
            `;
            previewBody.appendChild(tr);
        });
    }

    if (processBtn) {
        processBtn.addEventListener('click', function() {
            if (!confirm('Are you sure you want to process these payments? This will update invoice statuses and create ledger entries.')) return;

            const validPayments = importedPaymentsData.filter(p => p.status !== 'error');
            if (validPayments.length === 0) {
                alert('No valid payments found to process.');
                return;
            }

            processBtn.disabled = true;
            processBtn.innerText = 'Processing...';

            fetch('{{ route("erp.invoices.import-payments-process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ payments: validPayments })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                location.reload();
            })
            .catch(err => {
                console.error(err);
                alert('Error processing payments.');
                processBtn.innerText = 'Process Payments';
                processBtn.disabled = false;
            });
        });
    }
});
</script>
@endpush
