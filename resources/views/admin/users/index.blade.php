@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">User Management</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-create-user">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
            Create new user
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
            <a href="{{ route('admin.users.index', ['view' => 'active']) }}" class="nav-link {{ $view === 'active' ? 'active' : '' }}">Active Users</a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.users.index', ['view' => 'trash']) }}" class="nav-link {{ $view === 'trash' ? 'active' : '' }}">
              Trash <span class="badge bg-red-lt ms-2" id="trash-count"></span>
            </a>
          </li>
        </ul>
      </div>
      
      <div class="card-body border-bottom py-3">
        <div class="d-flex">
          <div class="text-secondary">
            <form id="bulk-action-form" action="{{ route('admin.users.bulk-action') }}" method="POST" class="d-flex align-items-center">
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
          <div class="ms-auto text-secondary">
            <div class="input-icon">
              <span class="input-icon-addon">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
              </span>
              <input type="text" id="ajax-search" class="form-control form-control-sm" placeholder="Search..." aria-label="Search users">
            </div>
          </div>
        </div>
      </div>

      <div id="table-container">
        @include('admin.users._table')
      </div>
    </div>
  </div>
</div>

<!-- Modal Create User -->
<div class="modal modal-blur fade" id="modal-create-user" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">New User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="Enter name" required>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter email" required>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Mobile Number</label>
                <input type="text" name="mobile" class="form-control" placeholder="Enter mobile" required>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                  <option value="active">Active</option>
                  <option value="suspended">Suspended</option>
                  <option value="blocked">Blocked</option>
                </select>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Set password" required>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Assign Roles</label>
            <div class="form-selectgroup">
              @foreach($roles as $role)
              <label class="form-selectgroup-item">
                <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="form-selectgroup-input">
                <span class="form-selectgroup-label">{{ $role->name }}</span>
              </label>
              @endforeach
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary ms-auto">Create User</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit User (Looped) -->
@foreach($users as $user)
<div class="modal modal-blur fade" id="modal-edit-user-{{ $user->id }}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">Edit User: {{ $user->name }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Mobile Number</label>
                <input type="text" name="mobile" class="form-control" value="{{ $user->mobile }}" required>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                  <option value="active" @selected($user->status == 'active')>Active</option>
                  <option value="suspended" @selected($user->status == 'suspended')>Suspended</option>
                  <option value="blocked" @selected($user->status == 'blocked')>Blocked</option>
                </select>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="mb-3">
                <label class="form-label">Password <small class="text-muted">(Leave blank to keep current)</small></label>
                <input type="password" name="password" class="form-control" placeholder="Enter new password">
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Assign Roles</label>
            <div class="form-selectgroup">
              @php $userRoles = $user->roles->pluck('name')->toArray(); @endphp
              @foreach($roles as $role)
              <label class="form-selectgroup-item">
                <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="form-selectgroup-input" @checked(in_array($role->name, $userRoles))>
                <span class="form-selectgroup-label">{{ $role->name }}</span>
              </label>
              @endforeach
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary ms-auto">Update User</button>
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

    // AJAX Search
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetchUsers();
            }, 500);
        });
    }

    function fetchUsers(url = null) {
        if (!url) {
            url = new URL(window.location.href);
            url.searchParams.set('search', searchInput.value);
            url.searchParams.set('view', '{{ $view }}');
            url.searchParams.delete('page'); // Reset to page 1 on search
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                tableContainer.innerHTML = html;
                // Re-initialize any components if needed here
            });
    }

    // Event Delegation for Select All
    document.addEventListener('change', function (e) {
        if (e.target && e.target.id === 'select-all') {
            const isChecked = e.target.checked;
            const checkboxes = tableContainer.querySelectorAll('.user-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = isChecked;
            });
        }
    });

    // Sync Select All checkbox state
    document.addEventListener('change', function (e) {
        if (e.target && e.target.classList.contains('user-checkbox')) {
            const selectAll = document.getElementById('select-all');
            if (selectAll) {
                const total = tableContainer.querySelectorAll('.user-checkbox').length;
                const checked = tableContainer.querySelectorAll('.user-checkbox:checked').length;
                selectAll.checked = total > 0 && total === checked;
                selectAll.indeterminate = checked > 0 && checked < total;
            }
        }
    });

    // Event Delegation for Pagination
    tableContainer.addEventListener('click', function (e) {
        const link = e.target.closest('#pagination-links a');
        if (link) {
            e.preventDefault();
            fetchUsers(link.href);
        }
    });

    // Bulk Action Submission
    if (bulkForm) {
        bulkForm.addEventListener('submit', function (e) {
            const selectedCheckboxes = tableContainer.querySelectorAll('.user-checkbox:checked');
            const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

            if (selectedIds.length === 0) {
                e.preventDefault();
                alert('Please select at least one user.');
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

            // Remove existing hidden ids to avoid duplicates
            bulkForm.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
            
            // Add new hidden ids
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
