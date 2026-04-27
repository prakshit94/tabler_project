<div class="table-responsive">
  <table class="table table-vcenter card-table">
    <thead>
      <tr>
        <th class="w-1"><input type="checkbox" class="form-check-input" id="select-all"></th>
        <th>Permission Name</th>
        <th>Guard Name</th>
        <th class="w-1"></th>
      </tr>
    </thead>
    <tbody>
      @forelse($permissions as $permission)
      <tr>
        <td><input type="checkbox" class="form-check-input permission-checkbox" value="{{ $permission->id }}"></td>
        <td class="font-weight-medium">{{ $permission->name }}</td>
        <td class="text-secondary">{{ $permission->guard_name }}</td>
        <td>
          <div class="btn-list flex-nowrap">
            @if($view === 'active')
            <a href="#" class="btn btn-white btn-sm" data-bs-toggle="modal" data-bs-target="#modal-edit-permission-{{ $permission->id }}">
              Edit
            </a>
            <form action="{{ route('admin.permissions.destroy', $permission->id) }}" method="POST" onsubmit="return confirm('Move to trash?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-ghost-danger btn-sm">Trash</button>
            </form>
            @else
            <form action="{{ route('admin.permissions.restore', $permission->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-ghost-success btn-sm">Restore</button>
            </form>
            <form action="{{ route('admin.permissions.force-delete', $permission->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-ghost-danger btn-sm">Delete</button>
            </form>
            @endif
          </div>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="4" class="text-center py-4 text-muted">No permissions found</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="card-footer d-flex align-items-center" id="pagination-links">
  {{ $permissions->links() }}
</div>
