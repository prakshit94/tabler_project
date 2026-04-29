<div class="table-responsive">
  <table class="table card-table table-vcenter text-nowrap datatable">
    <thead>
      <tr>
        <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" id="select-all"></th>
        <th class="w-1">ID</th>
        <th>Account Type Name</th>
        <th>Created At</th>
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($account_types as $account_type)
      <tr>
        <td><input class="form-check-input m-0 align-middle account_type-checkbox" type="checkbox" value="{{ $account_type->id }}"></td>
        <td><span class="text-secondary">{{ $account_type->id }}</span></td>
        <td>{{ $account_type->name }}</td>
        <td>{{ $account_type->created_at->format('d M Y') }}</td>
        <td class="text-end">
          @if($view === 'active')
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-edit-account_type-{{ $account_type->id }}">Edit</button>
            <form action="{{ route('erp.account-types.destroy', $account_type->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Move to trash?')">Delete</button>
            </form>
          @else
            <form action="{{ route('erp.account-types.restore', $account_type->id) }}" method="POST" class="d-inline">
              @csrf @method('PATCH')
              <button type="submit" class="btn btn-sm btn-success">Restore</button>
            </form>
            <form action="{{ route('erp.account-types.force-delete', $account_type->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-dark" onclick="return confirm('Permanently delete?')">Force Delete</button>
            </form>
          @endif
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="5" class="text-center text-secondary">No account types found</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="card-footer d-flex align-items-center" id="pagination-links">
  <p class="m-0 text-secondary">Showing <span>{{ $account_types->firstItem() ?? 0 }}</span> to <span>{{ $account_types->lastItem() ?? 0 }}</span> of <span>{{ $account_types->total() }}</span> entries</p>
  <div class="ms-auto">
    {{ $account_types->links('pagination::bootstrap-4') }}
  </div>
</div>
