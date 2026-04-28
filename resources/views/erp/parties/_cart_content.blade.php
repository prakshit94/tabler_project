@if(count($cart) > 0)
<div class="flex-fill scroll-y p-3">
    <div class="list-group list-group-flush list-group-hoverable">
        @foreach($cart as $id => $item)
        <div class="list-group-item py-3 px-0 border-0 border-bottom">
            <div class="row align-items-center">
                <div class="col-auto">
                    <span class="avatar avatar-md rounded" style="background-image: url(/tabler/static/photos/product-{{ ($item['id'] % 5) + 1 }}.jpg)"></span>
                </div>
                <div class="col text-truncate">
                    <a href="#" class="text-reset d-block font-weight-bold">{{ $item['name'] }}</a>
                    <div class="d-block text-secondary text-truncate mt-n1 small">
                        SKU: {{ $item['sku'] }} | Qty: {{ $item['quantity'] }}
                    </div>
                </div>
                <div class="col-auto text-end">
                    <div class="font-weight-bold text-primary">₹ {{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                    <form action="{{ route('erp.parties.cart.remove', [$party->id, $id]) }}" method="POST" class="cart-remove-form">
                        @csrf @method('DELETE')
                        <input type="hidden" name="product_id" value="{{ $id }}">
                        <input type="hidden" name="quantity" value="{{ $item['quantity'] }}">
                        <button type="submit" class="btn btn-ghost-danger btn-sm border-0 p-0 mt-1">Remove</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Sticky Bottom Summary -->
<div class="mt-auto p-4 bg-primary-lt border-top shadow-sm">
    @php 
        $subTotal = array_reduce($cart, function($carry, $item) { return $carry + ($item['price'] * $item['quantity']); }, 0);
        $taxTotal = array_reduce($cart, function($carry, $item) { return $carry + (($item['tax'] ?? 0) * $item['quantity']); }, 0);
        $discountTotal = array_reduce($cart, function($carry, $item) { return $carry + (($item['discount'] ?? 0) * $item['quantity']); }, 0);
    @endphp
    
    <div class="card mb-3 border-0 shadow-none bg-transparent">
        <div class="card-body p-0">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">Subtotal</span>
                <span class="h4 m-0">₹ {{ number_format($subTotal, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">GST (Estimated)</span>
                <span class="text-success h4 m-0">+ ₹ {{ number_format($taxTotal, 2) }}</span>
            </div>
            @if($discountTotal > 0)
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">Total Savings</span>
                <span class="text-danger h4 m-0">- ₹ {{ number_format($discountTotal, 2) }}</span>
            </div>
            @endif
            <hr class="my-3 opacity-20">
            <div class="d-flex justify-content-between align-items-center mb-0">
                <span class="h3 m-0">Grand Total</span>
                <span class="h2 m-0 text-primary font-weight-bold">₹ {{ number_format($subTotal + $taxTotal - $discountTotal, 2) }}</span>
            </div>
        </div>
    </div>
    
    <div class="row g-2">
        <div class="col">
            <button class="btn btn-primary w-100 py-2 d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#modal-checkout" onclick="bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasCart')).hide();">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
                Place Order
            </button>
        </div>
    </div>
</div>
@else
<div class="flex-fill d-flex flex-column align-items-center justify-content-center text-center p-5">
    <div class="avatar avatar-xl bg-light text-secondary mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
    </div>
    <h2 class="h3">Cart is Empty</h2>
    <p class="text-secondary max-w-250">Browse the catalog and add products to start a new order for this customer.</p>
    <button class="btn btn-primary mt-4" data-bs-dismiss="offcanvas">Start Browsing</button>
</div>
@endif
