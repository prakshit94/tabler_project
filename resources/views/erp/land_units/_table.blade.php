<div class="table-responsive">
  <table class="table card-table table-vcenter text-nowrap datatable">
    <thead>
      <tr>
        <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" id="select-all"></th>
        <th class="w-1">ID</th>
        <th>Land Unit Name</th>
        <th>Unit Code</th>
        <th>Created At</th>
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($land_units as $land_unit)
      <tr>
        <td><input class="form-check-input m-0 align-middle land_unit-checkbox" type="checkbox" value="{{ $land_unit->id }}"></td>
        <td><span class="text-secondary">{{ $land_unit->id }}</span></td>
        <td>{{ $land_unit->name }}</td>
        <td><span class="badge bg-secondary-lt">{{ $land_unit->code }}</span></td>
        <td>{{ $land_unit->created_at->format('d M Y') }}</td>
        <td class="text-end">
          @if($view === 'active')
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-edit-land_unit-{{ $land_unit->id }}">Edit</button>
            <form action="{{ route('erp.land-units.destroy', $land_unit->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Move to trash?')">Delete</button>
            </form>
          @else
            <form action="{{ route('erp.land-units.restore', $land_unit->id) }}" method="POST" class="d-inline">
              @csrf @method('PATCH')
              <button type="submit" class="btn btn-sm btn-success">Restore</button>
            </form>
            <form action="{{ route('erp.land-units.force-delete', $land_unit->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-dark" onclick="return confirm('Permanently delete?')">Force Delete</button>
            </form>
          @endif
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="6" class="text-center text-secondary">No land units found</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="card-footer d-flex align-items-center" id="pagination-links">
  <p class="m-0 text-secondary">Showing <span>{{ $land_units->firstItem() ?? 0 }}</span> to <span>{{ $land_units->lastItem() ?? 0 }}</span> of <span>{{ $land_units->total() }}</span> entries</p>
  <div class="ms-auto">
    {{ $land_units->links('pagination::bootstrap-4') }}
  </div>
</div>
