<div class="table-responsive">
    <table class="table card-table table-vcenter text-nowrap">
        <thead>
            <tr>
                <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" id="select-all" aria-label="Select all orders"></th>
                <th>Order #</th>
                <th>Date</th>
                <th>Party</th>
                <th>Warehouse</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Invoice</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td><input class="form-check-input m-0 align-middle order-checkbox" type="checkbox" value="{{ $order->id }}" aria-label="Select order"></td>
                <td>
                    <a href="{{ route('erp.orders.show', $order->id) }}" class="font-weight-bold">
                        {{ $order->order_number }}
                    </a>
                </td>
                <td>
                    <div class="text-secondary">
                        {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-xs rounded me-2" style="background-image: url(https://ui-avatars.com/api/?name={{ urlencode($order->party->name) }}&background=random)"></span>
                        {{ $order->party->name }}
                    </div>
                </td>
                <td>
                    <span class="text-secondary small">{{ $order->warehouse->name }}</span>
                </td>
                <td>
                    <span class="font-weight-bold">₹ {{ number_format($order->total_amount, 2) }}</span>
                </td>
                <td>
                    <span class="badge {{ $order->status_badge_class }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                </td>
                <td>
                    @if($order->invoice)
                        <a href="{{ route('erp.invoices.show', $order->invoice->id) }}" class="badge bg-green-lt">
                            {{ $order->invoice->invoice_number }}
                        </a>
                    @else
                        <span class="badge bg-secondary-lt">Pending</span>
                    @endif
                </td>
                <td class="text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary view-order-btn" data-order-id="{{ $order->id }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                            View
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-icon btn-ghost-secondary" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M12 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M12 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /></svg>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow-sm">
                                @if($view === 'trash')
                                    <form action="{{ route('erp.orders.restore', $order->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-success">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1" /></svg>
                                            Restore Order
                                        </button>
                                    </form>
                                    <form action="{{ route('erp.orders.force-delete', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete this order?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                            Delete Permanently
                                        </button>
                                    </form>
                                @else
                                    <a class="dropdown-item view-order-btn" href="#" data-order-id="{{ $order->id }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                        View Details
                                    </a>
                                    @if($order->isEditable())
                                        <a class="dropdown-item" href="{{ route('erp.orders.edit', $order->id) }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                                            Edit Order
                                        </a>
                                    @endif
                                    @if($order->status === 'pending' && $order->type === 'sale')
                                        <form action="{{ route('erp.orders.update-status', $order->id) }}" method="POST" class="d-inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="dropdown-item text-success">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                                Mark Completed
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('erp.orders.destroy', $order->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                            Move to Trash
                                        </button>
                                    </form>
                                    <div class="dropdown-divider"></div>
                                    @if($order->invoice)
                                        <a class="dropdown-item" href="{{ route('erp.invoices.print', $order->invoice->id) }}" target="_blank">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                                            Print Invoice
                                        </a>
                                    @endif
                                    <a class="dropdown-item" href="{{ route('erp.orders.print-cod', $order->id) }}" target="_blank">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 5a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2H8a2 2 0 0 1 -2 -2v-14z" /><path d="M11 7l2 0" /><path d="M10 11l4 0" /><path d="M10 15l4 0" /><path d="M12 19l0 .01" /></svg>
                                        Print COD Label
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="empty">
                        <div class="empty-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 10l.01 0" /><path d="M15 10l.01 0" /><path d="M9.5 15a3.5 3.5 0 0 0 5 0" /></svg>
                        </div>
                        <p class="empty-title">No orders found</p>
                        <p class="empty-subtitle text-secondary">
                            Try adjusting your search or filter to find what you're looking for.
                        </p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="card-footer d-flex align-items-center" id="pagination-links">
    <div class="text-secondary small">
        Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} entries
    </div>
    <div class="ms-auto">
        {{ $orders->links() }}
    </div>
</div>
