<div class="table-responsive">
  <table class="table table-vcenter card-table" id="users-table">
    <thead>
      <tr>
        <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" id="select-all"></th>
        <th>User Details</th>
        <th>Access level</th>
        <th>Status</th>
        <th>Last Activity</th>
        <th class="w-1"></th>
      </tr>
    </thead>
    <tbody>
      @forelse($users as $user)
      <tr>
        <td><input class="form-check-input m-0 align-middle user-checkbox" type="checkbox" name="ids[]" value="{{ $user->id }}"></td>
        <td>
          <div class="d-flex py-1 align-items-center">
            <span class="avatar avatar-sm me-2 bg-primary-lt">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
            <div class="flex-fill">
              <div class="font-weight-medium text-reset">{{ $user->name }}</div>
              <div class="text-secondary small">
                {{ $user->email }} <br>
                <span class="text-muted">ID: #{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
              </div>
            </div>
          </div>
        </td>
        <td>
          <div>
            @foreach($user->roles as $role)
              <span class="badge bg-azure-lt me-1">{{ $role->name }}</span>
            @endforeach
          </div>
          <div class="text-muted small mt-1">{{ $user->getAllPermissions()->count() }} permissions</div>
        </td>
        <td>
            @php 
               $statusColor = $user->status === 'active' ? 'success' : ($user->status === 'suspended' ? 'warning' : 'danger');
            @endphp
            <span class="badge bg-{{ $statusColor }}-lt text-capitalize">{{ $user->status }}</span>
        </td>
        <td>
          <div class="text-secondary small">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'No login yet' }}</div>
        </td>
        <td>
          <div class="btn-list flex-nowrap">
            @if($view === 'trash')
              <form action="{{ route('admin.users.restore', $user->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-ghost-success btn-sm">Restore</button>
              </form>
              <form action="{{ route('admin.users.force-delete', $user->id) }}" method="POST" onsubmit="return confirm('Permanently delete?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-ghost-danger btn-sm">Delete</button>
              </form>
            @else
              <a href="#" class="btn btn-ghost-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-edit-user-{{ $user->id }}">
                Edit
              </a>
              <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Trash user?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-ghost-danger btn-sm">Trash</button>
              </form>
            @endif
          </div>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="6" class="text-center py-4 text-muted">No users found</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="card-footer d-flex align-items-center" id="pagination-links">
  <p class="m-0 text-secondary d-none d-md-block small">Showing <span>{{ $users->firstItem() ?? 0 }}</span> to <span>{{ $users->lastItem() ?? 0 }}</span> of <span>{{ $users->total() }}</span> entries</p>
  <div class="ms-auto">
    {{ $users->links('pagination::bootstrap-5') }}
  </div>
</div>
