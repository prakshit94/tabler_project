@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">Logistics & Procurement</div>
        <h2 class="page-title text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
            Edit {{ ucfirst($type) }} Order — {{ $order->order_number }}
        </h2>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <form action="{{ route('erp.orders.update', $order) }}" method="POST" id="order-form">
      @csrf
      @method('PUT')
      <input type="hidden" name="type" value="{{ $type }}">
      
      <div class="row row-cards">
        <div class="col-lg-8">
          <div class="card shadow-sm border-0 mb-3">
            <div class="card-status-top bg-primary"></div>
            <div class="card-header">
                <h3 class="card-title">General Information</h3>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label required">{{ $type == 'sale' ? 'Customer' : 'Vendor' }}</label>
                  <div class="input-group input-group-flat border rounded">
                    <span class="input-group-text bg-light border-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                    </span>
                    <select name="party_id" class="form-select border-0 ps-0" required id="select-party">
                      <option value="">Select {{ $type == 'sale' ? 'Customer' : 'Vendor' }}</option>
                      @foreach($parties as $p)
                      <option value="{{ $p->id }}" @selected($order->party_id == $p->id)>{{ $p->name }} ({{ $p->mobile }})</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <label class="form-label required">Warehouse</label>
                  <select name="warehouse_id" class="form-select border rounded" required>
                    @foreach($warehouses as $wh)
                    <option value="{{ $wh->id }}" @selected($order->warehouse_id == $wh->id)>{{ $wh->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label required">Order Date</label>
                  <input type="date" name="order_date" class="form-control border rounded" value="{{ $order->order_date->format('Y-m-d') }}" required>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0 overflow-hidden">
            <div class="card-header d-flex justify-content-between align-items-center bg-light-lt">
                <h3 class="card-title">Order Items</h3>
                <button type="button" class="btn btn-sm btn-ghost-primary" id="add-item">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    Add Product
                </button>
            </div>
            <div class="table-responsive" style="min-height: 250px;">
              <table class="table table-vcenter card-table table-hover" id="items-table">
                <thead class="bg-light">
                  <tr>
                    <th class="py-3">Product Description</th>
                    <th class="w-1 py-3 text-center">Quantity</th>
                    <th class="w-1 py-3 text-end">Unit Price</th>
                    <th class="w-1 py-3 text-end">Line Total</th>
                    <th class="w-1 py-3"></th>
                  </tr>
                </thead>
                <tbody id="items-body">
                  @foreach($order->items as $index => $item)
                  <tr class="item-row">
                    <td class="p-2">
                      <select name="items[{{ $index }}][product_id]" class="form-select border-0 shadow-none product-select" required>
                        <option value="">Search Product...</option>
                        @foreach($products as $prod)
                        <option value="{{ $prod->id }}" data-price="{{ $type == 'sale' ? $prod->selling_price : $prod->purchase_price }}" data-sku="{{ $prod->sku }}" @selected($item->product_id == $prod->id)>
                            {{ $prod->name }}
                        </option>
                        @endforeach
                      </select>
                      <div class="small text-muted mt-1 sku-label ms-1">SKU: {{ $item->product->sku }}</div>
                    </td>
                    <td class="p-2">
                        <input type="number" name="items[{{ $index }}][quantity]" class="form-control text-center border-0 shadow-none qty-input" step="0.01" min="0.01" value="{{ $item->quantity }}" required>
                    </td>
                    <td class="p-2">
                        <div class="input-group input-group-flat border-0 shadow-none">
                            <span class="input-group-text bg-transparent border-0 pe-0">₹</span>
                            <input type="number" name="items[{{ $index }}][unit_price]" class="form-control text-end border-0 shadow-none price-input" step="0.01" min="0" value="{{ $item->unit_price }}" required>
                        </div>
                    </td>
                    <td class="p-2 text-end align-middle">
                        <span class="h4 mb-0 text-dark fw-bold">₹ <span class="total-label">{{ number_format($item->total_price, 2) }}</span></span>
                        <input type="hidden" class="total-input" value="{{ $item->total_price }}">
                    </td>
                    <td class="p-2 text-center">
                        <button type="button" class="btn btn-icon btn-ghost-danger rounded-circle remove-item" title="Remove item">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                        </button>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card shadow-sm border-0 sticky-top" style="top: 1rem;">
            <div class="card-header bg-dark text-white">
                <h3 class="card-title">Order Summary</h3>
            </div>
            <div class="card-body">
              <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">Subtotal</span>
                <span class="fw-bold">₹ <span id="summary-subtotal">{{ number_format($order->total_amount, 2) }}</span></span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">Tax (0%)</span>
                <span class="fw-bold">₹ 0.00</span>
              </div>
              <hr class="my-2">
              <div class="d-flex justify-content-between mb-4">
                <span class="h3 mb-0">Grand Total</span>
                <span class="h2 mb-0 text-primary">₹ <span id="grand-total">{{ number_format($order->total_amount, 2) }}</span></span>
              </div>
              
              <div class="mb-3">
                <label class="form-label">Notes / Instructions</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Additional details...">{{ $order->notes }}</textarea>
              </div>

              <div class="space-y">
                <button type="submit" class="btn btn-primary w-100 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-1.293 5.953a1 1 0 0 0 -1.414 0l-3.293 3.293l-1.293 -1.293a1 1 0 0 0 -1.414 1.414l2 2a1 1 0 0 0 1.414 0l4 -4a1 1 0 0 0 0 -1.414z" fill="currentColor" stroke-width="0" /></svg>
                    Update {{ ucfirst($type) }} Order
                </button>
                <a href="{{ route('erp.orders.show', $order) }}" class="btn btn-link w-100 text-secondary">
                    Cancel & Return
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<style>
.item-row:hover { background-color: rgba(var(--tblr-primary-rgb), 0.02); }
.product-select:focus, .qty-input:focus, .price-input:focus { border-bottom: 2px solid var(--tblr-primary) !important; background: transparent; }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemsBody = document.getElementById('items-body');
    const addItemBtn = document.getElementById('add-item');
    const grandTotalSpan = document.getElementById('grand-total');
    const subtotalSpan = document.getElementById('summary-subtotal');
    let rowIdx = {{ $order->items->count() }};

    addItemBtn.addEventListener('click', function() {
        const row = document.querySelector('.item-row').cloneNode(true);
        row.querySelectorAll('input').forEach(input => {
            if (input.classList.contains('qty-input')) {
                input.value = 1;
            } else if (input.classList.contains('total-input')) {
                input.value = 0;
            } else {
                input.value = '';
            }
            input.name = input.name.replace(/items\[\d+\]/, `items[${rowIdx}]`);
        });
        row.querySelector('select').name = row.querySelector('select').name.replace(/items\[\d+\]/, `items[${rowIdx}]`);
        row.querySelector('select').value = '';
        row.querySelector('.total-label').textContent = '0.00';
        row.querySelector('.sku-label').textContent = 'SKU: ---';
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
            const option = e.target.selectedOptions[0];
            const price = option.dataset.price || 0;
            const sku = option.dataset.sku || '---';
            row.querySelector('.price-input').value = price;
            row.querySelector('.sku-label').textContent = `SKU: ${sku}`;
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
        row.querySelector('.total-input').value = total;
        row.querySelector('.total-label').textContent = total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let grand = 0;
        document.querySelectorAll('.total-input').forEach(input => {
            grand += parseFloat(input.value) || 0;
        });
        const formatted = grand.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        grandTotalSpan.textContent = formatted;
        subtotalSpan.textContent = formatted;
    }
});
</script>
@endpush
