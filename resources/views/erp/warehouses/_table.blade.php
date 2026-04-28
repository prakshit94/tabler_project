<div class="table-responsive">
  <table class="table card-table table-vcenter text-nowrap datatable">
    <thead>
      <tr>
        <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" id="select-all"></th>
        <th class="w-1">ID</th>
        <th>Name</th>
        <th>Code</th>
        <th>State</th>
        <th>Default</th>
        <th>Created At</th>
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($warehouses as $warehouse)
      <tr>
        <td><input class="form-check-input m-0 align-middle warehouse-checkbox" type="checkbox" value="{{ $warehouse->id }}"></td>
        <td><span class="text-secondary">{{ $warehouse->id }}</span></td>
        <td>{{ $warehouse->name }}</td>
        <td><code>{{ $warehouse->code }}</code></td>
        <td>{{ $warehouse->state ?? '-' }}</td>
        <td>
          @if($warehouse->is_default)
            <span class="badge bg-green text-green-fg">Yes</span>
          @else
            <span class="text-secondary">No</span>
          @endif
        </td>
        <td>{{ $warehouse->created_at->format('d M Y') }}</td>
        <td class="text-end">
          @if($view === 'active')
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-edit-warehouse-{{ $warehouse->id }}">Edit</button>
            <form action="{{ route('erp.warehouses.destroy', $warehouse->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Move to trash?')">Delete</button>
            </form>
          @else
            <form action="{{ route('erp.warehouses.restore', $warehouse->id) }}" method="POST" class="d-inline">
              @csrf @method('PATCH')
              <button type="submit" class="btn btn-sm btn-success">Restore</button>
            </form>
            <form action="{{ route('erp.warehouses.force-delete', $warehouse->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-dark" onclick="return confirm('Permanently delete?')">Force Delete</button>
            </form>
          @endif
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="8" class="text-center text-secondary">No warehouses found</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="card-footer d-flex align-items-center" id="pagination-links">
  <p class="m-0 text-secondary">Showing <span>{{ $warehouses->firstItem() ?? 0 }}</span> to <span>{{ $warehouses->lastItem() ?? 0 }}</span> of <span>{{ $warehouses->total() }}</span> entries</p>
  <div class="ms-auto">
    {{ $warehouses->links('pagination::bootstrap-4') }}
  </div>
</div>
