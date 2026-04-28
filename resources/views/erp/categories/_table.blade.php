<div class="table-responsive">
  <table class="table card-table table-vcenter text-nowrap datatable">
    <thead>
      <tr>
        <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" id="select-all"></th>
        <th class="w-1">ID</th>
        <th>Category Name</th>
        <th>Created At</th>
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($categories as $category)
      <tr>
        <td><input class="form-check-input m-0 align-middle category-checkbox" type="checkbox" value="{{ $category->id }}"></td>
        <td><span class="text-secondary">{{ $category->id }}</span></td>
        <td>{{ $category->name }}</td>
        <td>{{ $category->created_at->format('d M Y') }}</td>
        <td class="text-end">
          @if($view === 'active')
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-edit-category-{{ $category->id }}">Edit</button>
            <form action="{{ route('erp.categories.destroy', $category->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Move to trash?')">Delete</button>
            </form>
          @else
            <form action="{{ route('erp.categories.restore', $category->id) }}" method="POST" class="d-inline">
              @csrf @method('PATCH')
              <button type="submit" class="btn btn-sm btn-success">Restore</button>
            </form>
            <form action="{{ route('erp.categories.force-delete', $category->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-dark" onclick="return confirm('Permanently delete?')">Force Delete</button>
            </form>
          @endif
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="5" class="text-center text-secondary">No categories found</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="card-footer d-flex align-items-center" id="pagination-links">
  <p class="m-0 text-secondary">Showing <span>{{ $categories->firstItem() ?? 0 }}</span> to <span>{{ $categories->lastItem() ?? 0 }}</span> of <span>{{ $categories->total() }}</span> entries</p>
  <div class="ms-auto">
    {{ $categories->links('pagination::bootstrap-4') }}
  </div>
</div>
