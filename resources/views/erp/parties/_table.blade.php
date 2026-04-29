<div class="table-responsive">
  <table class="table card-table table-vcenter text-nowrap datatable">
    <thead>
      <tr>
        <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" id="select-all"></th>
        <th class="w-1">Code</th>
        <th>Name</th>
        <th>Type</th>
        <th>Mobile</th>
        <th>GSTIN</th>
        <th>Tags</th>
        <th>Status</th>
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($parties as $party)
      <tr>
        <td><input class="form-check-input m-0 align-middle party-checkbox" type="checkbox" value="{{ $party->id }}"></td>
        <td><span class="text-secondary">{{ $party->party_code }}</span></td>
        <td>
          <div class="d-flex align-items-center">
            <span class="avatar avatar-sm rounded me-2">{{ strtoupper(substr($party->name, 0, 2)) }}</span>
            <div class="flex-fill">
              <div class="font-weight-medium">{{ $party->name }}</div>
              <div class="text-secondary small">{{ ucfirst($party->category) }}</div>
            </div>
          </div>
        </td>
        <td>
          <span class="badge {{ $party->type == 'vendor' ? 'bg-purple-lt' : ($party->type == 'farmer' ? 'bg-green-lt' : 'bg-blue-lt') }}">
            {{ ucfirst($party->type) }}
          </span>
        </td>
        <td>
          <div>{{ $party->mobile }}</div>
          @if($party->phone_number_2)
            <div class="text-secondary small">Alt: {{ $party->phone_number_2 }}</div>
          @endif
        </td>
        <td><code>{{ $party->gstin ?? '-' }}</code></td>
        <td>
            @if($party->tags)
                @foreach(array_slice($party->tags, 0, 2) as $tag)
                    <span class="badge bg-info-lt">{{ $tag }}</span>
                @endforeach
                @if(count($party->tags) > 2)
                    <span class="badge bg-muted-lt">+{{ count($party->tags) - 2 }}</span>
                @endif
            @else
                -
            @endif
        </td>
        <td>
            @if($party->is_active)
                <span class="status-dot status-dot-animated bg-success d-block" title="Active"></span>
            @else
                <span class="status-dot bg-secondary d-block" title="Inactive"></span>
            @endif
        </td>
        <td class="text-end">
          @if($view === 'active')
            <a href="{{ route('erp.parties.profile', $party->id) }}" class="btn btn-sm btn-outline-primary">Profile</a>
            <button class="btn btn-sm btn-ghost-secondary" data-bs-toggle="modal" data-bs-target="#modal-edit-party-{{ $party->id }}">Edit</button>
            <form action="{{ route('erp.parties.destroy', $party->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-ghost-danger" onclick="return confirm('Move to trash?')">Delete</button>
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
        <td colspan="9" class="text-center text-secondary py-4">No records found</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="card-footer d-flex align-items-center" id="pagination-links">
  <p class="m-0 text-secondary d-none d-md-block">Showing <span>{{ $parties->firstItem() ?? 0 }}</span> to <span>{{ $parties->lastItem() ?? 0 }}</span> of <span>{{ $parties->total() }}</span> entries</p>
  <div class="ms-auto">
    {{ $parties->links('pagination::bootstrap-4') }}
  </div>
</div>
