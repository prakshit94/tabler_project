@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">New {{ ucfirst($type) }} Order</h2>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <form action="{{ route('erp.orders.store') }}" method="POST">
      @csrf
      <input type="hidden" name="type" value="{{ $type }}">
      
      <div class="row row-cards">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-lg-4">
                  <div class="mb-3">
                    <label class="form-label">{{ $type == 'sale' ? 'Customer' : 'Vendor' }}</label>
                    <select name="party_id" class="form-select" required>
                      <option value="">Select Party</option>
                      @foreach($parties as $p)
                      <option value="{{ $p->id }}">{{ $p->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-lg-4">
                  <div class="mb-3">
                    <label class="form-label">Warehouse</label>
                    <select name="warehouse_id" class="form-select" required>
                      @foreach($warehouses as $wh)
                      <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-lg-4">
                  <div class="mb-3">
                    <label class="form-label">Order Date</label>
                    <input type="date" name="order_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-12">
          <div class="card mt-3">
            <div class="card-header"><h3 class="card-title">Order Items</h3></div>
            <div class="table-responsive">
              <table class="table card-table table-vcenter" id="items-table">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th class="w-1">Quantity</th>
                    <th class="w-1">Unit Price</th>
                    <th class="w-1">Total</th>
                    <th class="w-1"></th>
                  </tr>
                </thead>
                <tbody id="items-body">
                  <tr class="item-row">
                    <td>
                      <select name="items[0][product_id]" class="form-select product-select" required>
                        <option value="">Select Product</option>
                        @foreach($products as $prod)
                        <option value="{{ $prod->id }}" data-price="{{ $type == 'sale' ? $prod->selling_price : $prod->purchase_price }}">{{ $prod->name }} ({{ $prod->sku }})</option>
                        @endforeach
                      </select>
                    </td>
                    <td><input type="number" name="items[0][quantity]" class="form-control qty-input" step="0.01" min="0.01" value="1" required></td>
                    <td><input type="number" name="items[0][unit_price]" class="form-control price-input" step="0.01" min="0" required></td>
                    <td><input type="text" class="form-control total-input" readonly value="0.00"></td>
                    <td><button type="button" class="btn btn-icon btn-danger remove-item"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg></button></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="card-footer d-flex align-items-center">
              <button type="button" class="btn btn-white" id="add-item">Add Item</button>
              <div class="ms-auto text-end">
                <div class="h3 mb-0">Total: ₹ <span id="grand-total">0.00</span></div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-12 text-end mt-3">
          <a href="{{ route('erp.orders.index', ['type' => $type]) }}" class="btn btn-link">Cancel</a>
          <button type="submit" class="btn btn-primary">Create Order</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemsBody = document.getElementById('items-body');
    const addItemBtn = document.getElementById('add-item');
    let rowIdx = 1;

    addItemBtn.addEventListener('click', function() {
        const row = document.querySelector('.item-row').cloneNode(true);
        row.querySelectorAll('input').forEach(input => {
            input.value = input.classList.contains('qty-input') ? 1 : (input.classList.contains('total-input') ? '0.00' : '');
            input.name = input.name.replace('[0]', `[${rowIdx}]`);
        });
        row.querySelector('select').name = row.querySelector('select').name.replace('[0]', `[${rowIdx}]`);
        row.querySelector('select').value = '';
        itemsBody.appendChild(row);
        rowIdx++;
    });

    itemsBody.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            if (itemsBody.querySelectorAll('.item-row').length > 1) {
                e.target.closest('.item-row').remove();
                calculateGrandTotal();
            }
        }
    });

    itemsBody.addEventListener('change', function(e) {
        const row = e.target.closest('.item-row');
        if (e.target.classList.contains('product-select')) {
            const price = e.target.selectedOptions[0].dataset.price || 0;
            row.querySelector('.price-input').value = price;
        }
        calculateRowTotal(row);
    });

    itemsBody.addEventListener('input', function(e) {
        if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
            calculateRowTotal(e.target.closest('.item-row'));
        }
    });

    function calculateRowTotal(row) {
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const total = qty * price;
        row.querySelector('.total-input').value = total.toFixed(2);
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let grand = 0;
        document.querySelectorAll('.total-input').forEach(input => {
            grand += parseFloat(input.value) || 0;
        });
        document.getElementById('grand-total').textContent = grand.toFixed(2);
    }
});
</script>
@endpush
