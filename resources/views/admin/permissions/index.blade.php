@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Permission Management</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-create-permission">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
            Create new permission
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
            <a href="{{ route('admin.permissions.index', ['view' => 'active']) }}" class="nav-link {{ $view === 'active' ? 'active' : '' }}">Active Permissions</a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.permissions.index', ['view' => 'trash']) }}" class="nav-link {{ $view === 'trash' ? 'active' : '' }}">
              Trash
            </a>
          </li>
        </ul>
      </div>
      
      <div class="card-body border-bottom py-3">
        <div class="d-flex">
          <div class="text-secondary">
            <form id="bulk-action-form" action="{{ route('admin.permissions.bulk-action') }}" method="POST" class="d-flex align-items-center">
              @csrf
              <select name="action" class="form-select form-select-sm w-auto me-2" id="bulk-action-select">
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
          <div class="ms-auto text-secondary">
            <div class="input-icon">
              <span class="input-icon-addon">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
              </span>
              <input type="text" id="ajax-search" class="form-control form-control-sm" placeholder="Search permissions..." aria-label="Search permissions">
            </div>
          </div>
        </div>
      </div>

      <div id="table-container">
        @include('admin.permissions._table')
      </div>
    </div>
  </div>
</div>

<!-- Modal Create Permission -->
<div class="modal modal-blur fade" id="modal-create-permission" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <form action="{{ route('admin.permissions.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">New Permission</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Permission Name</label>
            <input type="text" name="name" class="form-control" placeholder="e.g. view reports" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary ms-auto">Create Permission</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Permission (Looped) -->
@foreach($permissions as $permission)
<div class="modal modal-blur fade" id="modal-edit-permission-{{ $permission->id }}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <form action="{{ route('admin.permissions.update', $permission->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">Edit Permission: {{ $permission->name }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Permission Name</label>
            <input type="text" name="name" class="form-control" value="{{ $permission->name }}" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary ms-auto">Update Permission</button>
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
    let searchTimeout = null;

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetchPermissions();
            }, 500);
        });
    }

    function fetchPermissions(url = null) {
        if (!url) {
            url = new URL(window.location.href);
            url.searchParams.set('search', searchInput.value);
            url.searchParams.set('view', '{{ $view }}');
            url.searchParams.delete('page');
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                tableContainer.innerHTML = html;
            });
    }

    document.addEventListener('change', function (e) {
        if (e.target && e.target.id === 'select-all') {
            const isChecked = e.target.checked;
            const checkboxes = tableContainer.querySelectorAll('.permission-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = isChecked;
            });
        }
    });

    document.addEventListener('change', function (e) {
        if (e.target && e.target.classList.contains('permission-checkbox')) {
            const selectAll = document.getElementById('select-all');
            if (selectAll) {
                const total = tableContainer.querySelectorAll('.permission-checkbox').length;
                const checked = tableContainer.querySelectorAll('.permission-checkbox:checked').length;
                selectAll.checked = total > 0 && total === checked;
                selectAll.indeterminate = checked > 0 && checked < total;
            }
        }
    });

    tableContainer.addEventListener('click', function (e) {
        const link = e.target.closest('#pagination-links a');
        if (link) {
            e.preventDefault();
            fetchPermissions(link.href);
        }
    });

    if (bulkForm) {
        bulkForm.addEventListener('submit', function (e) {
            const selectedCheckboxes = tableContainer.querySelectorAll('.permission-checkbox:checked');
            const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

            if (selectedIds.length === 0) {
                e.preventDefault();
                alert('Please select at least one permission.');
                return;
            }

            const actionSelect = document.getElementById('bulk-action-select');
            if (!actionSelect.value) {
                e.preventDefault();
                alert('Please select an action.');
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
