@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Edit Product: {{ $product->name }}</h2>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <form action="{{ route('erp.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
      @csrf @method('PUT')
      <div class="row row-cards">
        <div class="col-lg-8">

          {{-- Product Images Card --}}
          <div class="card mb-3">
            <div class="card-header">
              <h3 class="card-title">Product Images</h3>
            </div>
            <div class="card-body">
              {{-- Existing images gallery --}}
              @if($product->images->count())
              <div class="row g-3 mb-3" id="image-gallery">
                @foreach($product->images as $img)
                <div class="col-6 col-md-3" id="img-card-{{ $img->id }}">
                  <div class="card shadow-sm h-100 {{ $img->is_primary ? 'border border-primary' : '' }}">
                    <img src="{{ asset('storage/' . $img->image_path) }}"
                         class="card-img-top"
                         style="height:120px;object-fit:cover;"
                         alt="Product Image">
                    <div class="card-body p-2 text-center">
                      @if($img->is_primary)
                        <span class="badge bg-primary mb-1">Primary</span>
                      @else
                        <button type="button"
                                class="btn btn-sm btn-ghost-primary btn-set-primary w-100 mb-1"
                                data-image-id="{{ $img->id }}"
                                data-product-id="{{ $product->id }}">
                          Set Primary
                        </button>
                      @endif
                      <button type="button"
                              class="btn btn-sm btn-ghost-danger btn-delete-image w-100"
                              data-image-id="{{ $img->id }}"
                              data-product-id="{{ $product->id }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                        Delete
                      </button>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
              @else
              <div class="text-center text-secondary py-3" id="no-images-msg">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01" /><path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" /><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" /><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" /></svg>
                <div>No images uploaded yet.</div>
              </div>
              @endif

              {{-- Upload new images --}}
              <div class="mt-3">
                <label class="form-label text-primary fw-bold">Upload New Images</label>
                <input type="file" name="images[]" id="new-image-upload" class="form-control" multiple accept="image/*">
                <small class="form-hint">Selecting new images will add them to the existing ones.</small>
                <!-- Preview Container -->
                <div id="new-image-preview" class="row g-2 mt-2" style="display:none;"></div>
              </div>
            </div>
          </div>

          {{-- Basic Info Card --}}
          <div class="card">
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label">Product Name</label>
                <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="5">{{ $product->description }}</textarea>
              </div>
              <div class="row">
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label class="form-label">SKU</label>
                    <input type="text" name="sku" class="form-control" value="{{ $product->sku }}" required>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label class="form-label">Unit</label>
                    <select name="unit" class="form-select" required>
                      <option value="pcs" @selected($product->unit == 'pcs')>Pcs</option>
                      <option value="kg" @selected($product->unit == 'kg')>Kg</option>
                      <option value="mtr" @selected($product->unit == 'mtr')>Mtr</option>
                      <option value="box" @selected($product->unit == 'box')>Box</option>
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
                      <input type="number" name="purchase_price" class="form-control" step="0.01" value="{{ $product->purchase_price }}" required>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label class="form-label">Selling Price</label>
                    <div class="input-group">
                      <span class="input-group-text">₹</span>
                      <input type="number" name="selling_price" class="form-control" step="0.01" value="{{ $product->selling_price }}" required>
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
                      <option value="{{ $tr->id }}" @selected($tr->id == $product->tax_rate_id)>{{ $tr->name }} ({{ $tr->igst }}%)</option>
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
                      <option value="{{ $hc->id }}" @selected($hc->id == $product->hsn_code_id)>{{ $hc->code }} - {{ $hc->description }}</option>
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
                  <option value="{{ $brand->id }}" @selected($brand->id == $product->brand_id)>{{ $brand->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" id="category_id" class="form-select">
                  <option value="">No Category</option>
                  @foreach($categories as $cat)
                  <option value="{{ $cat->id }}" @selected($cat->id == $product->category_id)>{{ $cat->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Sub-Category</label>
                <select name="sub_category_id" id="sub_category_id" class="form-select">
                  <option value="">No Sub-Category</option>
                  @foreach($subCategories as $sub)
                  <option value="{{ $sub->id }}" @selected($sub->id == $product->sub_category_id)>{{ $sub->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Min Stock Level</label>
                <input type="number" name="min_stock_level" class="form-control" value="{{ $product->min_stock_level }}">
              </div>
              <div class="mb-3">
                <label class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked($product->is_active)>
                  <span class="form-check-label">Product is Active</span>
                </label>
              </div>
            </div>
            <div class="card-footer text-end">
              <a href="{{ route('erp.products.index') }}" class="btn btn-link">Cancel</a>
              <button type="submit" class="btn btn-primary">Update Product</button>
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
document.getElementById('category_id').addEventListener('change', function() {
    const categoryId = this.value;
    const subCategorySelect = document.getElementById('sub_category_id');
    subCategorySelect.innerHTML = '<option value="">Loading...</option>';
    if (!categoryId) {
        subCategorySelect.innerHTML = '<option value="">No Sub-Category</option>';
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

// Delete image via AJAX
document.querySelectorAll('.btn-delete-image').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Delete this image?')) return;
        const imageId = this.dataset.imageId;
        const productId = this.dataset.productId;
        const card = document.getElementById('img-card-' + imageId);

        fetch(`/erp/products/${productId}/images/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        }).then(res => res.json()).then(data => {
            if (data.success) {
                card.remove();
            }
        });
    });
});

// Set primary image via AJAX
document.querySelectorAll('.btn-set-primary').forEach(btn => {
    btn.addEventListener('click', function() {
        const imageId = this.dataset.imageId;
        const productId = this.dataset.productId;

        fetch(`/erp/products/${productId}/images/${imageId}/set-primary`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        }).then(res => res.json()).then(data => {
            if (data.success) {
                // Reload page to reflect updated primary badge
                window.location.reload();
            }
        });
    });
});
// Image preview for new uploads
document.getElementById('new-image-upload').addEventListener('change', function() {
    const container = document.getElementById('new-image-preview');
    container.innerHTML = '';
    if (this.files.length === 0) {
        container.style.display = 'none';
        return;
    }
    const hasPrimary = {{ $product->images->where('is_primary', true)->count() > 0 ? 'true' : 'false' }};
    container.style.display = 'flex';
    Array.from(this.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const col = document.createElement('div');
            col.className = 'col-6 col-md-3';
            const isPrimary = !hasPrimary && index === 0;
            col.innerHTML = `
                <div class="card shadow-sm position-relative">
                    ${ isPrimary ? '<span class="badge bg-primary position-absolute top-0 start-0 m-1">Will be Primary</span>' : '' }
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
