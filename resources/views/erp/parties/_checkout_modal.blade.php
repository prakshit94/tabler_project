<!-- Premium Checkout Modal -->
<div class="modal modal-blur fade" id="modal-checkout" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content shadow-lg border-0">
            <form action="{{ route('erp.parties.place-order', $party->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Finalize Order Placement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-7">
                            <h3 class="card-title mb-3">Order Configuration</h3>
                            <div class="mb-3">
                                <label class="form-label required">Select Shipping Warehouse</label>
                                <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                                    @foreach($warehouses as $warehouse)
                                    <label class="form-selectgroup-item flex-fill mb-2">
                                        <input type="radio" name="warehouse_id" value="{{ $warehouse->id }}" class="form-selectgroup-input" {{ $loop->first ? 'checked' : '' }}>
                                        <div class="form-selectgroup-label d-flex align-items-center p-3">
                                            <div class="me-3">
                                                <span class="form-selectgroup-check"></span>
                                            </div>
                                            <div class="form-selectgroup-label-content">
                                                <div class="font-weight-bold">{{ $warehouse->name }}</div>
                                                <div class="text-secondary small">{{ $warehouse->location ?? 'Default Distribution Center' }}</div>
                                            </div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-selectgroup-item w-100">
                                            <input type="radio" name="payment_method" value="Cash" class="form-selectgroup-input" checked>
                                            <div class="form-selectgroup-label text-center py-2">Cash</div>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-selectgroup-item w-100">
                                            <input type="radio" name="payment_method" value="Bank Transfer" class="form-selectgroup-input">
                                            <div class="form-selectgroup-label text-center py-2">Bank</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-5 border-start-md">
                            <h3 class="card-title mb-3">Order Summary</h3>
                            <div class="card bg-surface-secondary border-0">
                                <div class="card-body p-3">
                                    @php 
                                        $subTotal = array_reduce($cart, function($carry, $item) { return $carry + ($item['price'] * $item['quantity']); }, 0);
                                        $taxTotal = array_reduce($cart, function($carry, $item) { return $carry + (($item['tax'] ?? 0) * $item['quantity']); }, 0);
                                    @endphp
                                    <div class="list-group list-group-flush bg-transparent">
                                        @foreach($cart as $item)
                                        <div class="list-group-item bg-transparent px-0 py-2 border-0">
                                            <div class="d-flex justify-content-between small">
                                                <span class="text-truncate" style="max-width: 120px;">{{ $item['name'] }} x {{ $item['quantity'] }}</span>
                                                <span class="font-weight-bold">₹ {{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <hr class="my-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-secondary">Subtotal:</span>
                                        <span>₹ {{ number_format($subTotal, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-secondary">GST (18%):</span>
                                        <span class="text-success">+ ₹ {{ number_format($taxTotal, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between h2 mb-0 mt-3 pt-3 border-top">
                                        <span class="font-weight-bold">Total:</span>
                                        <span class="text-primary font-weight-bold">₹ {{ number_format($subTotal + $taxTotal, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 text-secondary small">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></svg>
                                Final order value may vary based on exact shipping costs.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                        Confirm & Place Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
