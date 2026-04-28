<div class="table-responsive">
  <table class="table card-table table-vcenter text-nowrap datatable">
    <thead>
      <tr>
        <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" id="select-all"></th>
        <th class="w-1">ID</th>
        <th>HSN Code</th>
        <th>Description</th>
        <th>Tax Rate</th>
        <th>Created At</th>
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($hsnCodes as $hsnCode)
      <tr>
        <td><input class="form-check-input m-0 align-middle hsncode-checkbox" type="checkbox" value="{{ $hsnCode->id }}"></td>
        <td><span class="text-secondary">{{ $hsnCode->id }}</span></td>
        <td>{{ $hsnCode->code }}</td>
        <td class="text-truncate" style="max-width: 200px;">{{ $hsnCode->description }}</td>
        <td>{{ $hsnCode->taxRate->name ?? 'N/A' }} ({{ $hsnCode->taxRate->igst ?? 0 }}%)</td>
        <td>{{ $hsnCode->created_at->format('d M Y') }}</td>
        <td class="text-end">
          @if($view === 'active')
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-edit-hsn-code-{{ $hsnCode->id }}">Edit</button>
            <form action="{{ route('erp.hsn-codes.destroy', $hsnCode->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Move to trash?')">Delete</button>
            </form>
          @else
            <form action="{{ route('erp.hsn-codes.restore', $hsnCode->id) }}" method="POST" class="d-inline">
              @csrf @method('PATCH')
              <button type="submit" class="btn btn-sm btn-success">Restore</button>
            </form>
            <form action="{{ route('erp.hsn-codes.force-delete', $hsnCode->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-dark" onclick="return confirm('Permanently delete?')">Force Delete</button>
            </form>
          @endif
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="7" class="text-center text-secondary">No HSN codes found</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="card-footer d-flex align-items-center" id="pagination-links">
  <p class="m-0 text-secondary">Showing <span>{{ $hsnCodes->firstItem() ?? 0 }}</span> to <span>{{ $hsnCodes->lastItem() ?? 0 }}</span> of <span>{{ $hsnCodes->total() }}</span> entries</p>
  <div class="ms-auto">
    {{ $hsnCodes->links('pagination::bootstrap-4') }}
  </div>
</div>
