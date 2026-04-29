@php 
    $subTotal = array_reduce($cart, function($carry, $item) { return $carry + ($item['price'] * $item['quantity']); }, 0);
    $taxTotal = array_reduce($cart, function($carry, $item) { return $carry + (($item['tax'] ?? 0) * $item['quantity']); }, 0);
    $discountTotal = array_reduce($cart, function($carry, $item) { return $carry + (($item['discount'] ?? 0) * $item['quantity']); }, 0);
    $grandTotal = ($subTotal + $taxTotal) - $discountTotal;
@endphp

<style>
    .form-selectgroup-input:checked + .form-selectgroup-label {
        border-color: var(--tblr-primary) !important;
        background-color: var(--tblr-primary-lt) !important;
        box-shadow: 0 0 0 1px var(--tblr-primary);
    }
    .form-selectgroup-label {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .form-selectgroup-label:hover {
        border-color: var(--tblr-primary);
        background-color: var(--tblr-bg-surface-secondary);
    }
    .checkout-step-number {
        width: 24px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--tblr-primary);
        color: white;
        border-radius: 50%;
        font-size: 14px;
        margin-right: 8px;
    }
</style>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Step 1: Customer Details -->
        <div class="card mb-3 shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title"><span class="checkout-step-number">1</span> Customer Information</h3>
                <button class="btn btn-ghost-primary btn-sm border-0" data-bs-toggle="modal" data-bs-target="#modal-edit-profile">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                    Edit Profile
                </button>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="subheader small mb-1 text-secondary">Full Name</div>
                        <div class="font-weight-bold h4">{{ $party->name }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="subheader small mb-1 text-secondary">Customer Code</div>
                        <div class="font-weight-bold h4">{{ $party->party_code }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="subheader small mb-1 text-secondary">Primary Mobile</div>
                        <div class="font-weight-bold h4">{{ $party->mobile }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="subheader small mb-1 text-secondary">Outstanding Balance</div>
                        <div class="font-weight-bold h4 {{ $party->outstanding_balance > 0 ? 'text-danger' : 'text-success' }}">₹ {{ number_format($party->outstanding_balance, 2) }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="subheader small mb-1 text-secondary">Credit Limit</div>
                        <div class="font-weight-bold h4">₹ {{ number_format($party->credit_limit, 2) }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="subheader small mb-1 text-secondary">Tax Status</div>
                        <div class="font-weight-bold h4"><span class="badge bg-green-lt">{{ $party->gstin ? 'GST Registered' : 'Unregistered' }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Shipping & Billing -->
        <div class="card mb-3 shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <h3 class="card-title"><span class="checkout-step-number">2</span> Shipping & Billing Addresses</h3>
                <button class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#modal-add-address">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    New Address
                </button>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="form-label font-weight-bold m-0 text-primary">Select Shipping Address</label>
                        <span class="badge bg-blue-lt">Delivery Destination</span>
                    </div>
                    <div class="row g-3">
                        @forelse($party->addresses as $address)
                        <div class="col-md-6">
                            <label class="form-selectgroup-item w-100">
                                <input type="radio" name="shipping_address_id" value="{{ $address->id }}" 
                                    class="form-selectgroup-input shipping-radio" 
                                    {{ $address->is_default ? 'checked' : '' }} 
                                    onchange="syncBilling(this.value)">
                                <div class="form-selectgroup-label p-3 h-100 text-start border-2">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="badge bg-blue-lt">{{ ucfirst($address->type) }}</span>
                                        <div class="btn-list">
                                            <button type="button" onclick='event.preventDefault(); editAddress(@json($address))' class="btn btn-ghost-primary btn-icon btn-sm border-0"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg></button>
                                        </div>
                                    </div>
                                    <div class="font-weight-bold mb-2 h4 text-dark">{{ $address->label ?? $address->contact_name ?? 'Address Details' }}</div>
                                    <div class="text-secondary small line-height-base">
                                        <div class="mb-1"><span class="text-muted subheader small">Village:</span> <span class="text-dark font-weight-bold">{{ $address->village ?? 'N/A' }}</span></div>
                                        <div class="mb-1"><span class="text-muted subheader small">Post Office:</span> <span class="text-dark font-weight-bold">{{ $address->post_office ?? 'N/A' }}</span></div>
                                        <div class="mb-1"><span class="text-muted subheader small">Taluka:</span> <span class="text-dark font-weight-bold">{{ $address->taluka ?? 'N/A' }}</span></div>
                                        <div class="mb-1"><span class="text-muted subheader small">District:</span> <span class="text-dark font-weight-bold">{{ $address->district ?? 'N/A' }}</span></div>
                                        <div class="mb-1"><span class="text-muted subheader small">State:</span> <span class="text-dark font-weight-bold">{{ $address->state ?? 'N/A' }}</span> - {{ $address->pincode }}</div>
                                        @if($address->address_line1)<div class="mt-2 p-2 bg-light rounded text-dark"><strong>Area:</strong> {{ $address->address_line1 }}</div>@endif
                                    </div>
                                </div>
                            </label>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="empty bg-light rounded py-4">
                                <p class="empty-title h4">No addresses found</p>
                                <p class="empty-subtitle">Add a shipping address to continue.</p>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>

                <div class="hr-text text-primary mb-4">Billing Configuration</div>

                <div class="mb-4">
                    <label class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="same_as_shipping" name="same_as_shipping" checked onchange="toggleBilling(this.checked)">
                        <span class="form-check-label font-weight-bold h4 m-0">Billing Address same as Shipping</span>
                    </label>

                    <div id="billing_address_section" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label font-weight-bold m-0 text-success">Select Separate Billing Address</label>
                            <span class="badge bg-green-lt">Invoice Source</span>
                        </div>
                        <div class="row g-3">
                            @foreach($party->addresses as $address)
                            <div class="col-md-6">
                                <label class="form-selectgroup-item w-100">
                                    <input type="radio" name="billing_address_id" value="{{ $address->id }}" 
                                        class="form-selectgroup-input billing-radio" 
                                        {{ $address->is_default ? 'checked' : '' }}>
                                    <div class="form-selectgroup-label p-3 h-100 text-start border-2">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="badge bg-green-lt">{{ ucfirst($address->type) }}</span>
                                        </div>
                                        <div class="font-weight-bold mb-2 h4 text-dark">{{ $address->label ?? $address->contact_name ?? 'Billing Details' }}</div>
                                        <div class="text-secondary small line-height-base">
                                            <div class="mb-1"><span class="text-muted subheader small">Village:</span> <span class="text-dark font-weight-bold">{{ $address->village ?? 'N/A' }}</span></div>
                                            <div class="mb-1"><span class="text-muted subheader small">Post Office:</span> <span class="text-dark font-weight-bold">{{ $address->post_office ?? 'N/A' }}</span></div>
                                            <div class="mb-1"><span class="text-muted subheader small">Taluka:</span> <span class="text-dark font-weight-bold">{{ $address->taluka ?? 'N/A' }}</span></div>
                                            <div class="mb-1"><span class="text-muted subheader small">District:</span> <span class="text-dark font-weight-bold">{{ $address->district ?? 'N/A' }}</span></div>
                                            <div class="mb-1"><span class="text-muted subheader small">State:</span> <span class="text-dark font-weight-bold">{{ $address->state ?? 'N/A' }}</span> - {{ $address->pincode }}</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Order Items -->
        <div class="card mb-3 shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <h3 class="card-title"><span class="checkout-step-number">3</span> Review Items</h3>
                <span class="badge bg-primary-lt h3 m-0 px-3">{{ count($cart) }} Items</span>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Subtotal</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody id="checkout-items-body">
                        @foreach($cart as $id => $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-md rounded me-3" style="background-image: url(/tabler/static/photos/product-{{ ($item['id'] % 5) + 1 }}.jpg)"></span>
                                    <div>
                                        <div class="font-weight-bold h4 mb-0">{{ $item['name'] }}</div>
                                        <div class="text-secondary small">SKU: {{ $item['sku'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <form action="{{ route('erp.parties.cart.update', [$party->id, $id]) }}" method="POST" class="cart-update-form">
                                    @csrf @method('PATCH')
                                    <div class="input-group input-group-flat shadow-none border rounded mx-auto" style="width: 130px;">
                                        <button class="btn btn-icon btn-light border-0 cart-qty-minus" type="button"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /></svg></button>
                                        <input type="text" name="quantity" class="form-control text-center border-0 font-weight-bold" value="{{ $item['quantity'] }}">
                                        <button class="btn btn-icon btn-light border-0 cart-qty-plus" type="button"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg></button>
                                    </div>
                                </form>
                            </td>
                            <td class="text-end text-secondary h4 font-weight-normal">₹ {{ number_format($item['price'], 2) }}</td>
                            <td class="text-end font-weight-bold h4 text-primary">₹ {{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                            <td>
                                <form action="{{ route('erp.parties.cart.remove', [$party->id, $id]) }}" method="POST" class="cart-remove-form">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-ghost-danger btn-icon border-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-light py-3">
                <button class="btn btn-outline-primary" onclick="document.getElementById('v-pills-products-tab').click()">Add More Products</button>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <form action="{{ route('erp.parties.place-order', $party->id) }}" method="POST">
            @csrf
            <!-- Step 4: Finalize -->
            <div class="card mb-3 shadow-sm border-0 overflow-hidden">
                <div class="card-header bg-dark text-white py-3">
                    <h3 class="card-title text-white"><span class="checkout-step-number bg-surface text-primary">4</span> Finalize & Place Order</h3>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label font-weight-bold text-secondary subheader">Select Shipping Warehouse</label>
                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                            @foreach($warehouses as $warehouse)
                            <label class="form-selectgroup-item flex-fill mb-2">
                                <input type="radio" name="warehouse_id" value="{{ $warehouse->id }}" class="form-selectgroup-input" {{ $loop->first ? 'checked' : '' }}>
                                <div class="form-selectgroup-label d-flex align-items-center p-3 border-2">
                                    <div class="me-3">
                                        <span class="form-selectgroup-check"></span>
                                    </div>
                                    <div class="form-selectgroup-label-content">
                                        <div class="font-weight-bold h4 mb-0">{{ $warehouse->name }}</div>
                                        <div class="text-secondary small">{{ $warehouse->location ?? 'Main Center' }}</div>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label font-weight-bold text-secondary subheader">Payment Method</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-selectgroup-item w-100">
                                    <input type="radio" name="payment_method" value="Cash" class="form-selectgroup-input" checked>
                                    <div class="form-selectgroup-label text-center py-3 border-2 font-weight-bold">Cash</div>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="form-selectgroup-item w-100">
                                    <input type="radio" name="payment_method" value="Bank Transfer" class="form-selectgroup-input">
                                    <div class="form-selectgroup-label text-center py-3 border-2 font-weight-bold">Bank</div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="hr-text text-secondary mb-4">Summary</div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary h4 font-weight-normal">Subtotal</span>
                        <span class="h4">₹ {{ number_format($subTotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary h4 font-weight-normal">GST (Tax)</span>
                        <span class="text-success h4">+ ₹ {{ number_format($taxTotal, 2) }}</span>
                    </div>
                    @if($discountTotal > 0)
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary h4 font-weight-normal">Total Savings</span>
                        <span class="text-danger h4">- ₹ {{ number_format($discountTotal, 2) }}</span>
                    </div>
                    @endif
                    
                    <div class="card bg-primary-lt border-0 mb-4">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h3 m-0 font-weight-bold text-primary">Grand Total</span>
                                <span class="h1 m-0 text-primary font-weight-bold">₹ {{ number_format($grandTotal, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 py-3 shadow-sm font-weight-bold" style="font-size: 1.1rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
                        Place Order Now
                    </button>
                    
                    <div class="mt-4 text-center p-3 bg-light rounded border border-dashed">
                        <div class="text-secondary small mb-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline text-success me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                            Inventory will be reserved upon confirmation.
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    function toggleBilling(sameAsShipping) {
        const billingSection = document.getElementById('billing_address_section');
        if (sameAsShipping) {
            billingSection.style.display = 'none';
            // Sync billing ID with shipping ID
            const selectedShipping = document.querySelector('input[name="shipping_address_id"]:checked');
            if(selectedShipping) syncBilling(selectedShipping.value);
        } else {
            billingSection.style.display = 'block';
        }
    }

    function syncBilling(addressId) {
        if(document.getElementById('same_as_shipping').checked) {
            const billingRadio = document.querySelector(`.billing-radio[value="${addressId}"]`);
            if(billingRadio) billingRadio.checked = true;
        }
    }
</script>
