<div class="table-responsive">
  <table class="table table-vcenter card-table">
    <thead>
      <tr>
        <th class="w-1"><input type="checkbox" class="form-check-input" id="select-all"></th>
        <th>Role Name</th>
        <th>Permissions</th>
        <th class="w-1"></th>
      </tr>
    </thead>
    <tbody>
      @forelse($roles as $role)
      <tr>
        <td><input type="checkbox" class="form-check-input role-checkbox" value="{{ $role->id }}"></td>
        <td class="font-weight-medium">{{ $role->name }}</td>
        <td>
          @foreach($role->permissions as $permission)
            <span class="badge bg-azure-lt me-1 mb-1">{{ $permission->name }}</span>
          @endforeach
          @if($role->permissions->isEmpty())
            <span class="text-muted small">No permissions assigned</span>
          @endif
        </td>
        <td>
          <div class="btn-list flex-nowrap">
            @if($view === 'active')
            <a href="#" class="btn btn-white btn-sm" data-bs-toggle="modal" data-bs-target="#modal-edit-role-{{ $role->id }}">
              Edit
            </a>
            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Move to trash?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-ghost-danger btn-sm">Trash</button>
            </form>
            @else
            <form action="{{ route('admin.roles.restore', $role->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-ghost-success btn-sm">Restore</button>
            </form>
            <form action="{{ route('admin.roles.force-delete', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-ghost-danger btn-sm">Delete</button>
            </form>
            @endif
          </div>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="4" class="text-center py-4 text-muted">No roles found</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="card-footer d-flex align-items-center" id="pagination-links">
  {{ $roles->links() }}
</div>
