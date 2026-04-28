@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Party Management (Customers & Vendors)</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-create-party">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
            Create new party
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="card">
      <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
          <li class="nav-item">
            <a href="{{ route('erp.parties.index', ['view' => 'active']) }}" class="nav-link {{ $view === 'active' ? 'active' : '' }}">Active Parties</a>
          </li>
          <li class="nav-item">
            <a href="{{ route('erp.parties.index', ['view' => 'trash']) }}" class="nav-link {{ $view === 'trash' ? 'active' : '' }}">
              Trash
            </a>
          </li>
        </ul>
      </div>
      
      <div class="card-body border-bottom py-3">
        <div class="d-flex">
          <div class="text-secondary">
            <form id="bulk-action-form" action="{{ route('erp.parties.bulk-action') }}" method="POST" class="d-flex align-items-center">
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
          </div>
          <div class="ms-auto text-secondary d-flex">
            <select id="type-filter" class="form-select form-select-sm w-auto me-2">
              <option value="">All Types</option>
              <option value="customer">Customer</option>
              <option value="vendor">Vendor</option>
            </select>
            <div class="input-icon">
              <span class="input-icon-addon">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
              </span>
              <input type="text" id="ajax-search" class="form-control form-control-sm" placeholder="Search..." aria-label="Search parties">
            </div>
          </div>
        </div>
      </div>

      <div id="table-container">
        @include('erp.parties._table')
      </div>
    </div>
  </div>
</div>

<!-- Modal Create Party -->
<div class="modal modal-blur fade" id="modal-create-party" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <form action="{{ route('erp.parties.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">New Party</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-8">
              <div class="mb-3">
                <label class="form-label">Party Name</label>
                <input type="text" name="name" class="form-control" placeholder="Enter name" required>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="mb-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select" required>
                  <option value="customer">Customer</option>
                  <option value="vendor">Vendor</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">GSTIN</label>
                <input type="text" name="gstin" class="form-control" placeholder="Optional">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">State</label>
                <input type="text" name="state" class="form-control" placeholder="Optional">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" placeholder="10 digit mobile" value="{{ session('searched_mobile') }}" maxlength="10">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Optional">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary ms-auto">Create Party</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Party (Looped) -->
@foreach($parties as $party)
<div class="modal modal-blur fade" id="modal-edit-party-{{ $party->id }}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <form action="{{ route('erp.parties.update', $party->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">Edit Party: {{ $party->name }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-8">
              <div class="mb-3">
                <label class="form-label">Party Name</label>
                <input type="text" name="name" class="form-control" value="{{ $party->name }}" required>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="mb-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select" required>
                  <option value="customer" @selected($party->type == 'customer')>Customer</option>
                  <option value="vendor" @selected($party->type == 'vendor')>Vendor</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">GSTIN</label>
                <input type="text" name="gstin" class="form-control" value="{{ $party->gstin }}">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">State</label>
                <input type="text" name="state" class="form-control" value="{{ $party->state }}">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ $party->phone }}">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ $party->email }}">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary ms-auto">Update Party</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableContainer = document.getElementById('table-container');
    const bulkForm = document.getElementById('bulk-action-form');
    const searchInput = document.getElementById('ajax-search');
    const typeFilter = document.getElementById('type-filter');
    let searchTimeout = null;

    function fetchParties(url = null) {
        if (!url) {
            url = new URL(window.location.href);
            url.searchParams.set('search', searchInput.value);
            url.searchParams.set('type', typeFilter.value);
            url.searchParams.set('view', '{{ $view }}');
            url.searchParams.delete('page');
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                tableContainer.innerHTML = html;
            });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetchParties();
            }, 500);
        });
    }

    if (typeFilter) {
        typeFilter.addEventListener('change', () => fetchParties());
    }

    document.addEventListener('change', function (e) {
        if (e.target && e.target.id === 'select-all') {
            const checkboxes = tableContainer.querySelectorAll('.party-checkbox');
            checkboxes.forEach(cb => cb.checked = e.target.checked);
        }
    });

    tableContainer.addEventListener('click', function (e) {
        const link = e.target.closest('#pagination-links a');
        if (link) {
            e.preventDefault();
            fetchParties(link.href);
        }
    });

    if (bulkForm) {
        bulkForm.addEventListener('submit', function (e) {
            const selectedIds = Array.from(tableContainer.querySelectorAll('.party-checkbox:checked')).map(cb => cb.value);
            if (selectedIds.length === 0) {
                e.preventDefault();
                alert('Please select at least one party.');
                return;
            }
            if (!confirm('Are you sure?')) {
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

    // Handle Search Modal trigger (Global Search)
    @if(session('open_create_modal'))
        var myModal = new bootstrap.Modal(document.getElementById('modal-create-party'));
        myModal.show();
    @endif
});
</script>
@endpush
