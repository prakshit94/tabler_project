<div class="table-responsive">
  <table class="table card-table table-vcenter text-nowrap datatable">
    <thead>
      <tr>
        <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" id="select-all"></th>
        <th class="w-1">ID</th>
        <th>Crop Name</th>
        <th>Created At</th>
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($crops as $crop)
      <tr>
        <td><input class="form-check-input m-0 align-middle crop-checkbox" type="checkbox" value="{{ $crop->id }}"></td>
        <td><span class="text-secondary">{{ $crop->id }}</span></td>
        <td>{{ $crop->name }}</td>
        <td>{{ $crop->created_at->format('d M Y') }}</td>
        <td class="text-end">
          @if($view === 'active')
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-edit-crop-{{ $crop->id }}">Edit</button>
            <form action="{{ route('erp.crops.destroy', $crop->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Move to trash?')">Delete</button>
            </form>
          @else
            <form action="{{ route('erp.crops.restore', $crop->id) }}" method="POST" class="d-inline">
              @csrf @method('PATCH')
              <button type="submit" class="btn btn-sm btn-success">Restore</button>
            </form>
            <form action="{{ route('erp.crops.force-delete', $crop->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-dark" onclick="return confirm('Permanently delete?')">Force Delete</button>
            </form>
          @endif
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="5" class="text-center text-secondary">No crops found</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="card-footer d-flex align-items-center" id="pagination-links">
  <p class="m-0 text-secondary">Showing <span>{{ $crops->firstItem() ?? 0 }}</span> to <span>{{ $crops->lastItem() ?? 0 }}</span> of <span>{{ $crops->total() }}</span> entries</p>
  <div class="ms-auto">
    {{ $crops->links('pagination::bootstrap-4') }}
  </div>
</div>
