<div class="table-responsive">
  <table class="table card-table table-vcenter text-nowrap datatable">
    <thead>
      <tr>
        <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" id="select-all"></th>
        <th class="w-1">ID</th>
        <th>Name</th>
        <th>Type</th>
        <th>GSTIN</th>
        <th>Phone/Email</th>
        <th>Created At</th>
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($parties as $party)
      <tr>
        <td><input class="form-check-input m-0 align-middle party-checkbox" type="checkbox" value="{{ $party->id }}"></td>
        <td><span class="text-secondary">{{ $party->id }}</span></td>
        <td>{{ $party->name }}</td>
        <td>
          <span class="badge {{ $party->type == 'customer' ? 'bg-blue-lt' : 'bg-purple-lt' }}">
            {{ ucfirst($party->type) }}
          </span>
        </td>
        <td><code>{{ $party->gstin ?? '-' }}</code></td>
        <td>
          <div>{{ $party->phone ?? '-' }}</div>
          <div class="text-secondary small">{{ $party->email ?? '-' }}</div>
        </td>
        <td>{{ $party->created_at->format('d M Y') }}</td>
        <td class="text-end">
          @if($view === 'active')
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-edit-party-{{ $party->id }}">Edit</button>
            <form action="{{ route('erp.parties.destroy', $party->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Move to trash?')">Delete</button>
            </form>
          @else
            <form action="{{ route('erp.parties.restore', $party->id) }}" method="POST" class="d-inline">
              @csrf @method('PATCH')
              <button type="submit" class="btn btn-sm btn-success">Restore</button>
            </form>
            <form action="{{ route('erp.parties.force-delete', $party->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-dark" onclick="return confirm('Permanently delete?')">Force Delete</button>
            </form>
          @endif
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="8" class="text-center text-secondary">No parties found</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="card-footer d-flex align-items-center" id="pagination-links">
  <p class="m-0 text-secondary">Showing <span>{{ $parties->firstItem() ?? 0 }}</span> to <span>{{ $parties->lastItem() ?? 0 }}</span> of <span>{{ $parties->total() }}</span> entries</p>
  <div class="ms-auto">
    {{ $parties->links('pagination::bootstrap-4') }}
  </div>
</div>
