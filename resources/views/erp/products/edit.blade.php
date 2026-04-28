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
    <form action="{{ route('erp.products.update', $product->id) }}" method="POST">
      @csrf @method('PUT')
      <div class="row row-cards">
        <div class="col-lg-8">
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
</script>
@endpush
