<div class="table-responsive">
    <table class="table table-vcenter card-table table-striped">
        <thead>
            <tr>
                <th>Backorder #</th>
                <th>Order / Party</th>
                <th>Product</th>
                <th>Warehouse</th>
                <th>Qty</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($backorders as $bo)
            <tr>
                <td>
                    <div class="font-weight-bold">{{ $bo->backorder_number }}</div>
                    <div class="text-muted small">{{ $bo->created_at->format('d M Y') }}</div>
                </td>
                <td>
                    <div><a href="{{ route('erp.orders.show', $bo->order_id) }}" class="text-reset fw-bold">{{ $bo->order->order_number }}</a></div>
                    <div class="text-muted small">{{ $bo->order->party->name ?? '-' }}</div>
                </td>
                <td>
                    <div class="fw-bold">{{ $bo->product->name }}</div>
                    <div class="text-secondary extra-small">{{ $bo->product->sku }}</div>
                </td>
                <td>{{ $bo->warehouse->name ?? '-' }}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-red-lt text-red me-2" title="Pending">{{ $bo->pending_qty }}</span>
                        @if($bo->fulfilled_qty > 0)
                            <span class="badge bg-green-lt text-green" title="Fulfilled">{{ $bo->fulfilled_qty }}</span>
                        @endif
                    </div>
                </td>
                <td>
                    @php 
                        $cls = match($bo->status) { 
                            'fulfilled' => 'bg-green-lt text-green',
                            'waiting_stock' => 'bg-yellow-lt text-yellow',
                            'pending' => 'bg-red-lt text-red',
                            'cancelled' => 'bg-secondary-lt text-secondary',
                            default => 'bg-secondary-lt text-secondary' 
                        }; 
                    @endphp
                    <span class="badge {{ $cls }}">{{ ucfirst(str_replace('_',' ',$bo->status)) }}</span>
                </td>
                <td class="text-end">
                    <div class="btn-list justify-content-end">
                        @if(in_array($bo->status, ['pending','waiting_stock']))
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#fulfillModal{{ $bo->id }}">Fulfill</button>
                        @endif
                        <a href="{{ route('erp.backorders.show', $bo) }}" class="btn btn-sm btn-white">View</a>
                    </div>
                </td>
            </tr>

            {{-- Fulfill Modal --}}
            @if(in_array($bo->status, ['pending','waiting_stock']))
            <div class="modal modal-blur fade" id="fulfillModal{{ $bo->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                    <form method="POST" action="{{ route('erp.backorders.fulfill', $bo) }}">
                        @csrf @method('PATCH')
                        <div class="modal-content shadow-lg border-0">
                            <div class="modal-header">
                                <h5 class="modal-title text-success">Fulfill Backorder</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body py-4">
                                <div class="text-center mb-3">
                                    <div class="avatar avatar-md bg-success-lt text-success mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3l8 4.5v9L12 21l-8-4.5v-9L12 3z"/><path d="M12 12l8 -4.5"/><path d="M12 12l0 9"/><path d="M12 12l-8 -4.5"/></svg>
                                    </div>
                                    <h4 class="mb-1">{{ $bo->product->name }}</h4>
                                    <p class="text-muted small">Remaining: {{ $bo->pending_qty - $bo->fulfilled_qty }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label required">Fulfilled Quantity</label>
                                    <input type="number" name="fulfilled_qty" class="form-control form-control-lg text-center" min="0.01" max="{{ $bo->pending_qty - $bo->fulfilled_qty }}" step="0.01" value="{{ $bo->pending_qty - $bo->fulfilled_qty }}" required autofocus>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">Confirm Fulfillment</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            @empty
            <tr>
                <td colspan="7" class="text-center py-5">
                    <div class="empty">
                        <div class="empty-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4"/><path d="M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg>
                        </div>
                        <p class="empty-title">No backorders in this category</p>
                        <p class="empty-subtitle text-secondary">
                            Everything looks up to date!
                        </p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($backorders->hasPages())
<div class="card-footer d-flex align-items-center" id="pagination-links">
    <p class="m-0 text-muted d-none d-md-block">Showing <span>{{ $backorders->firstItem() }}</span> to <span>{{ $backorders->lastItem() }}</span> of <span>{{ $backorders->total() }}</span> entries</p>
    <div class="ms-auto">
        {{ $backorders->links() }}
    </div>
</div>
@endif
