@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Product Management</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="{{ route('erp.products.create') }}" class="btn btn-primary d-none d-sm-inline-block">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
            Add new product
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="card">
      <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
          <li class="nav-item">
            <a href="{{ route('erp.products.index', ['view' => 'active']) }}" class="nav-link {{ $view === 'active' ? 'active' : '' }}">Active Products</a>
          </li>
          <li class="nav-item">
            <a href="{{ route('erp.products.index', ['view' => 'trash']) }}" class="nav-link {{ $view === 'trash' ? 'active' : '' }}">
              Trash
            </a>
          </li>
        </ul>
      </div>
      
      <div class="card-body border-bottom py-3">
        <div class="d-flex">
          <div class="text-secondary">
            <form id="bulk-action-form" action="{{ route('erp.products.bulk-action') }}" method="POST" class="d-flex align-items-center">
              @csrf
              <select name="action" class="form-select form-select-sm w-auto me-2" id="bulk-action-select" required>
                <option value="">Bulk Actions</option>
                @if($view === 'active')
                  <option value="delete">Move to Trash</option>
                @else
                  <option value="restore">Restore Selected</option>
                  <option value="force-delete">Permanently Delete</option>
                @endif
              </select>
              <button type="submit" class="btn btn-sm btn-white">Apply</button>
            </form>
          </div>
          <div class="ms-auto text-secondary d-flex">
             <select id="category-filter" class="form-select form-select-sm w-auto me-2">
              <option value="">All Categories</option>
              @foreach($categories as $cat)
              <option value="{{ $cat->id }}">{{ $cat->name }}</option>
              @endforeach
            </select>
            <div class="input-icon">
              <span class="input-icon-addon">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
              </span>
              <input type="text" id="ajax-search" class="form-control form-control-sm" placeholder="Search..." aria-label="Search products">
            </div>
          </div>
        </div>
      </div>

      <div id="table-container">
        @include('erp.products._table')
      </div>
    </div>
  </div>
</div>

<!-- Product Details Modal -->
<div class="modal modal-blur fade" id="modal-product-details" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0" id="product-details-content">
        <div class="modal-body text-center py-5">
            <div class="spinner-border text-primary mb-3" role="status"></div>
            <div class="text-secondary">Loading product information...</div>
        </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableContainer = document.getElementById('table-container');
    const bulkForm = document.getElementById('bulk-action-form');
    const searchInput = document.getElementById('ajax-search');
    const categoryFilter = document.getElementById('category-filter');
    let searchTimeout = null;

    function fetchProducts(url = null) {
        if (!url) {
            url = new URL(window.location.href);
            url.searchParams.set('search', searchInput.value);
            url.searchParams.set('category_id', categoryFilter.value);
            url.searchParams.set('view', '{{ $view }}');
            url.searchParams.delete('page');
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                tableContainer.innerHTML = html;
            });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetchProducts();
            }, 500);
        });
    }

    if (categoryFilter) {
        categoryFilter.addEventListener('change', () => fetchProducts());
    }

    document.addEventListener('change', function (e) {
        if (e.target && e.target.id === 'select-all') {
            const checkboxes = tableContainer.querySelectorAll('.product-checkbox');
            checkboxes.forEach(cb => cb.checked = e.target.checked);
        }
    });

    tableContainer.addEventListener('click', function (e) {
        const link = e.target.closest('#pagination-links a');
        if (link) {
            e.preventDefault();
            fetchProducts(link.href);
        }
    });

    if (bulkForm) {
        bulkForm.addEventListener('submit', function (e) {
            const selectedIds = Array.from(tableContainer.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
            if (selectedIds.length === 0) {
                e.preventDefault();
                alert('Please select at least one product.');
                return;
            }
            if (!confirm('Are you sure?')) {
                e.preventDefault();
                return;
            }
            bulkForm.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                bulkForm.appendChild(input);
            });
        });
    }

    // Product Details Modal Loading
    const productDetailsModal = document.getElementById('modal-product-details');
    const productDetailsContent = document.getElementById('product-details-content');

    tableContainer.addEventListener('click', function(e) {
        const viewBtn = e.target.closest('.view-product-btn');
        if (viewBtn) {
            e.preventDefault();
            const productId = viewBtn.dataset.productId;
            const url = `/erp/products/${productId}`;

            // Show loading state
            productDetailsContent.innerHTML = `
                <div class="modal-body text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <div class="text-secondary">Fetching product details...</div>
                </div>
            `;

            const modal = new bootstrap.Modal(productDetailsModal);
            modal.show();

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    productDetailsContent.innerHTML = html;
                })
                .catch(err => {
                    productDetailsContent.innerHTML = `
                        <div class="modal-body text-center py-5 text-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 17h.01" /></svg>
                            <div>Failed to load product data. Please try again.</div>
                        </div>
                    `;
                });
        }
    });
});
</script>
@endpush
