@extends('layouts.tabler')

@section('title', 'Packing Queue')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Warehouse Operations</div>
                <h2 class="page-title">Packing Queue</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('erp.wms.dashboard') }}" class="btn btn-outline-secondary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layout-dashboard" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h6v8h-6z" /><path d="M4 16h6v4h-6z" /><path d="M14 12h6v8h-6z" /><path d="M14 4h6v6h-6z" /></svg>
                        Logistics Hub
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-important alert-success alert-dismissible" role="alert">
                <div class="d-flex">
                    <div><svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg></div>
                    <div>{{ session('success') }}</div>
                </div>
                <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <div class="card">
            <div class="card-body border-bottom py-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap g-3">
                    <div class="d-flex align-items-center flex-wrap g-2">
                        <select class="form-select form-select-sm w-auto me-2" id="bulk-action-select">
                            <option value="">Bulk Actions</option>
                            <option value="print">Bulk Print Labels</option>
                        </select>
                        <button type="button" class="btn btn-sm btn-white me-3">Apply</button>

                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                            </span>
                            <input type="text" id="ajax-search" class="form-control form-control-sm" placeholder="Search Order #, Party..." value="{{ request('search') }}">
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center flex-wrap g-2">
                        <select id="status-filter" class="form-select form-select-sm w-auto me-2">
                            <option value="to_pack" @selected($status === 'to_pack')>Ready to Pack</option>
                            <option value="packed" @selected($status === 'packed')>Packed / Ready for Ship</option>
                            <option value="shipped" @selected($status === 'shipped')>Recently Shipped</option>
                        </select>

                        <select id="warehouse-filter" class="form-select form-select-sm w-auto">
                            <option value="">All Warehouses</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" @selected(request('warehouse_id') == $wh->id)>{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div id="table-container">
                @include('erp.wms.packing._table')
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableContainer = document.getElementById('table-container');
    const searchInput = document.getElementById('ajax-search');
    const warehouseFilter = document.getElementById('warehouse-filter');
    const statusFilter = document.getElementById('status-filter');
    let searchTimeout = null;

    function fetchPackingQueue(url = null) {
        if (!url) {
            url = new URL(window.location.href);
            url.searchParams.set('search', searchInput.value);
            url.searchParams.set('status', statusFilter.value);
            url.searchParams.set('warehouse_id', warehouseFilter.value);
            url.searchParams.delete('page');
        }

        tableContainer.style.opacity = '0.5';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
            .then(res => res.text())
            .then(html => {
                tableContainer.innerHTML = html;
                tableContainer.style.opacity = '1';
            })
            .catch(err => {
                console.error('Fetch error:', err);
                tableContainer.style.opacity = '1';
            });
    }

    // AJAX Search
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => fetchPackingQueue(), 500);
        });
    }

    // Filters
    if (warehouseFilter) {
        warehouseFilter.addEventListener('change', () => fetchPackingQueue());
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', () => fetchPackingQueue());
    }

    // Event Delegation for Pagination
    tableContainer.addEventListener('click', function (e) {
        const link = e.target.closest('#pagination-links a');
        if (link) {
            e.preventDefault();
            fetchPackingQueue(link.href);
        }
    });

    // Select All functionality
    tableContainer.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'select-all') {
            const isChecked = e.target.checked;
            tableContainer.querySelectorAll('.order-checkbox').forEach(cb => {
                cb.checked = isChecked;
            });
        }
    });
});
</script>
@endpush

