<div class="table-responsive">
  <table class="table card-table table-vcenter text-nowrap datatable">
    <thead>
      <tr>
        <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" id="select-all"></th>
        <th class="w-1">ID</th>
        <th>Tax Name</th>
        <th>CGST</th>
        <th>SGST</th>
        <th>IGST</th>
        <th>Created At</th>
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($taxRates as $taxRate)
      <tr>
        <td><input class="form-check-input m-0 align-middle taxrate-checkbox" type="checkbox" value="{{ $taxRate->id }}"></td>
        <td><span class="text-secondary">{{ $taxRate->id }}</span></td>
        <td>{{ $taxRate->name }}</td>
        <td>{{ $taxRate->cgst }}%</td>
        <td>{{ $taxRate->sgst }}%</td>
        <td>{{ $taxRate->igst }}%</td>
        <td>{{ $taxRate->created_at->format('d M Y') }}</td>
        <td class="text-end">
          @if($view === 'active')
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-edit-tax-rate-{{ $taxRate->id }}">Edit</button>
            <form action="{{ route('erp.tax-rates.destroy', $taxRate->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Move to trash?')">Delete</button>
            </form>
          @else
            <form action="{{ route('erp.tax-rates.restore', $taxRate->id) }}" method="POST" class="d-inline">
              @csrf @method('PATCH')
              <button type="submit" class="btn btn-sm btn-success">Restore</button>
            </form>
            <form action="{{ route('erp.tax-rates.force-delete', $taxRate->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-dark" onclick="return confirm('Permanently delete?')">Force Delete</button>
            </form>
          @endif
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="8" class="text-center text-secondary">No tax rates found</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="card-footer d-flex align-items-center" id="pagination-links">
  <p class="m-0 text-secondary">Showing <span>{{ $taxRates->firstItem() ?? 0 }}</span> to <span>{{ $taxRates->lastItem() ?? 0 }}</span> of <span>{{ $taxRates->total() }}</span> entries</p>
  <div class="ms-auto">
    {{ $taxRates->links('pagination::bootstrap-4') }}
  </div>
</div>
