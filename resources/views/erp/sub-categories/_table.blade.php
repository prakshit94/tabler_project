<div class="table-responsive">
  <table class="table card-table table-vcenter text-nowrap datatable">
    <thead>
      <tr>
        <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" id="select-all"></th>
        <th class="w-1">ID</th>
        <th>Sub-Category Name</th>
        <th>Parent Category</th>
        <th>Created At</th>
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($subCategories as $subCategory)
      <tr>
        <td><input class="form-check-input m-0 align-middle subcategory-checkbox" type="checkbox" value="{{ $subCategory->id }}"></td>
        <td><span class="text-secondary">{{ $subCategory->id }}</span></td>
        <td>{{ $subCategory->name }}</td>
        <td>{{ $subCategory->category->name ?? 'N/A' }}</td>
        <td>{{ $subCategory->created_at->format('d M Y') }}</td>
        <td class="text-end">
          @if($view === 'active')
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-edit-sub-category-{{ $subCategory->id }}">Edit</button>
            <form action="{{ route('erp.sub-categories.destroy', $subCategory->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Move to trash?')">Delete</button>
            </form>
          @else
            <form action="{{ route('erp.sub-categories.restore', $subCategory->id) }}" method="POST" class="d-inline">
              @csrf @method('PATCH')
              <button type="submit" class="btn btn-sm btn-success">Restore</button>
            </form>
            <form action="{{ route('erp.sub-categories.force-delete', $subCategory->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-dark" onclick="return confirm('Permanently delete?')">Force Delete</button>
            </form>
          @endif
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="6" class="text-center text-secondary">No sub-categories found</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="card-footer d-flex align-items-center" id="pagination-links">
  <p class="m-0 text-secondary">Showing <span>{{ $subCategories->firstItem() ?? 0 }}</span> to <span>{{ $subCategories->lastItem() ?? 0 }}</span> of <span>{{ $subCategories->total() }}</span> entries</p>
  <div class="ms-auto">
    {{ $subCategories->links('pagination::bootstrap-4') }}
  </div>
</div>
