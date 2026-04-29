@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Create New Product</h2>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <form action="{{ route('erp.products.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="row row-cards">
        <div class="col-lg-8">
          <div class="card">
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label text-primary fw-bold">Product Media</label>
                <input type="file" name="images[]" id="image-upload" class="form-control" multiple accept="image/*">
                <small class="form-hint">You can select multiple images. First image will be set as primary.</small>
                <!-- Preview Container -->
                <div id="image-preview-container" class="row g-2 mt-2" style="display:none;"></div>
              </div>
              <div class="mb-3">
                <label class="form-label">Product Name</label>
                <input type="text" name="name" class="form-control" placeholder="Enter product name" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="5" placeholder="Product description..."></textarea>
              </div>
              <div class="row">
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label class="form-label">SKU</label>
                    <input type="text" name="sku" class="form-control" placeholder="Unique SKU" required>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label class="form-label">Unit</label>
                    <select name="unit" class="form-select" required>
                      <option value="pcs">Pcs</option>
                      <option value="kg">Kg</option>
                      <option value="mtr">Mtr</option>
                      <option value="box">Box</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="card mt-3">
            <div class="card-header"><h3 class="card-title">Pricing & Tax</h3></div>
            <div class="card-body">
              <div class="row">
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label class="form-label">Purchase Price</label>
                    <div class="input-group">
                      <span class="input-group-text">₹</span>
                      <input type="number" name="purchase_price" class="form-control" step="0.01" required>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label class="form-label">Selling Price</label>
                    <div class="input-group">
                      <span class="input-group-text">₹</span>
                      <input type="number" name="selling_price" class="form-control" step="0.01" required>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label class="form-label">Tax Rate</label>
                    <select name="tax_rate_id" class="form-select">
                      <option value="">Select Tax Rate</option>
                      @foreach($taxRates as $tr)
                      <option value="{{ $tr->id }}">{{ $tr->name }} ({{ $tr->igst }}%)</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label class="form-label">HSN Code</label>
                    <select name="hsn_code_id" class="form-select">
                      <option value="">Select HSN Code</option>
                      @foreach($hsnCodes as $hc)
                      <option value="{{ $hc->id }}">{{ $hc->code }} - {{ $hc->description }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card">
            <div class="card-header"><h3 class="card-title">Organization</h3></div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label">Brand</label>
                <select name="brand_id" class="form-select">
                  <option value="">No Brand</option>
                  @foreach($brands as $brand)
                  <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" id="category_id" class="form-select">
                  <option value="">No Category</option>
                  @foreach($categories as $cat)
                  <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Sub-Category</label>
                <select name="sub_category_id" id="sub_category_id" class="form-select">
                  <option value="">Select Category First</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Min Stock Level</label>
                <input type="number" name="min_stock_level" class="form-control" value="0">
              </div>
              <div class="mb-3">
                <label class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                  <span class="form-check-label">Product is Active</span>
                </label>
              </div>
            </div>
            <div class="card-footer text-end">
              <a href="{{ route('erp.products.index') }}" class="btn btn-link">Cancel</a>
              <button type="submit" class="btn btn-primary">Save Product</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Sub-category loader
document.getElementById('category_id').addEventListener('change', function() {
    const categoryId = this.value;
    const subCategorySelect = document.getElementById('sub_category_id');
    subCategorySelect.innerHTML = '<option value="">Loading...</option>';
    if (!categoryId) {
        subCategorySelect.innerHTML = '<option value="">Select Category First</option>';
        return;
    }
    fetch(`{{ route('erp.products.get-subcategories') }}?category_id=${categoryId}`)
        .then(res => res.json())
        .then(data => {
            subCategorySelect.innerHTML = '<option value="">No Sub-Category</option>';
            data.forEach(sub => {
                subCategorySelect.innerHTML += `<option value="${sub.id}">${sub.name}</option>`;
            });
        });
});

// Image preview
document.getElementById('image-upload').addEventListener('change', function() {
    const container = document.getElementById('image-preview-container');
    container.innerHTML = '';
    if (this.files.length === 0) {
        container.style.display = 'none';
        return;
    }
    container.style.display = 'flex';
    Array.from(this.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const col = document.createElement('div');
            col.className = 'col-6 col-md-3';
            col.innerHTML = `
                <div class="card shadow-sm position-relative">
                    ${ index === 0 ? '<span class="badge bg-primary position-absolute top-0 start-0 m-1">Primary</span>' : '' }
                    <img src="${e.target.result}"
                         class="card-img-top"
                         style="height:120px;object-fit:cover;"
                         alt="Preview">
                    <div class="card-body p-1 text-center">
                        <small class="text-secondary text-truncate d-block" style="max-width:100%;">${file.name}</small>
                    </div>
                </div>
            `;
            container.appendChild(col);
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endpush
