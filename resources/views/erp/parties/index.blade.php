@extends('layouts.tabler')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    .ts-control { border: var(--tblr-border-width) solid var(--tblr-border-color); border-radius: var(--tblr-border-radius); padding: 0.5rem 0.75rem; background: var(--tblr-bg-surface); }
    .ts-dropdown { background: var(--tblr-bg-surface) !important; border-radius: var(--tblr-border-radius); box-shadow: var(--tblr-shadow-lg); border-color: var(--tblr-border-color); z-index: 2000 !important; }
    .ts-control .item { border-radius: 4px !important; padding: 2px 8px !important; }
    .ts-dropdown .active { background: var(--tblr-primary-lt) !important; color: var(--tblr-primary) !important; }
</style>
@endpush

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Farmer Management (Agriculture & Vendors)</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-create-party">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
            Register new Farmer
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
            <a href="{{ route('erp.parties.index', ['view' => 'active']) }}" class="nav-link {{ $view === 'active' ? 'active' : '' }}">Active Records</a>
          </li>
          <li class="nav-item">
            <a href="{{ route('erp.parties.index', ['view' => 'trash']) }}" class="nav-link {{ $view === 'trash' ? 'active' : '' }}">
              Trash
            </a>
          </li>
        </ul>
      </div>
      
      <div class="card-body border-bottom py-3">
        <div class="d-flex">
          <div class="text-secondary">
            <form id="bulk-action-form" action="{{ route('erp.parties.bulk-action') }}" method="POST" class="d-flex align-items-center">
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
            <select id="type-filter" class="form-select form-select-sm w-auto me-2">
              <option value="">All Types</option>
              <option value="customer">Customer</option>
              <option value="vendor">Vendor</option>
              <option value="farmer">Farmer</option>
              <option value="dealer">Dealer</option>
            </select>
            <div class="input-icon">
              <span class="input-icon-addon">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
              </span>
              <input type="text" id="ajax-search" class="form-control form-control-sm" placeholder="Search Code, Name, Mobile..." aria-label="Search farmers">
            </div>
          </div>
        </div>
      </div>

      <div id="table-container">
        @include('erp.parties._table')
      </div>
    </div>
  </div>
</div>

<!-- Modal Create Party -->
<div class="modal modal-blur fade" id="modal-create-party" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content border-0 shadow-lg">
      <form action="{{ route('erp.parties.store') }}" method="POST">
        @csrf
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Register New Farmer / Vendor</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body" style="max-height: 70vh; overflow-y: auto; overflow-x: hidden;">
          <div class="row g-4 pb-4">
            <!-- Left Column: Personal & Identity -->
            <div class="col-md-7">
              <fieldset class="form-fieldset">
                <legend class="fw-bold text-uppercase text-primary small mb-3">Identity & KYC</legend>
                
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label required">First Name</label>
                    <input type="text" name="first_name" class="form-control" placeholder="Required" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Middle Name</label>
                    <input type="text" name="middle_name" class="form-control" placeholder="Optional">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" placeholder="Optional">
                  </div>
                  
                  <div class="col-md-6">
                    <label class="form-label required">Mobile Number</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light">+91</span>
                      <input type="text" name="mobile" class="form-control" placeholder="10 Digits" required value="{{ session('searched_mobile') }}" maxlength="10">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="Optional">
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Secondary Phone</label>
                    <input type="text" name="phone_number_2" class="form-control">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Relative Phone</label>
                    <input type="text" name="relative_phone" class="form-control">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Aadhaar (Last 4)</label>
                    <input type="text" name="aadhaar_last4" class="form-control" maxlength="4">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label required">Account Type</label>
                    <select name="type" class="form-select" required>
                      <option value="farmer">Farmer (Producer)</option>
                      <option value="customer">Retail Customer</option>
                      <option value="vendor">Material Vendor</option>
                      <option value="dealer">Wholesale Dealer</option>
                      <option value="buyer">Bulk Buyer</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label required">Category</label>
                    <div class="form-selectgroup w-100">
                      <label class="form-selectgroup-item flex-fill">
                        <input type="radio" name="category" value="individual" class="form-selectgroup-input" checked>
                        <span class="form-selectgroup-label py-2">Individual</span>
                      </label>
                      <label class="form-selectgroup-item flex-fill">
                        <input type="radio" name="category" value="business" class="form-selectgroup-input">
                        <span class="form-selectgroup-label py-2">Business</span>
                      </label>
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <label class="form-label">Source / Referral</label>
                    <input type="text" name="source" class="form-control" list="source-list" placeholder="Select or type source">
                    <datalist id="source-list">
                      <option value="Walk-in">
                      <option value="Reference">
                      <option value="WhatsApp">
                      <option value="Facebook">
                      <option value="Field Visit">
                      <option value="Newspaper Ad">
                    </datalist>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Referred By</label>
                    <input type="text" name="referred_by" class="form-control">
                  </div>
                </div>
              </fieldset>

              <fieldset class="form-fieldset mt-4">
                <legend class="fw-bold text-uppercase text-primary small mb-3">Primary Address</legend>
                <div class="row g-3">
                  <div class="col-12">
                    <label class="form-label">Address Line 1</label>
                    <input type="text" name="address_line1" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Village / Town</label>
                    <input type="text" name="village" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Taluka</label>
                    <input type="text" name="taluka" class="form-control" list="taluka-list">
                    <datalist id="taluka-list">
                      <option value="Rahata">
                      <option value="Shrirampur">
                      <option value="Sangamner">
                      <option value="Kopargaon">
                      <option value="Akole">
                      <option value="Rahuri">
                      <option value="Nevasa">
                    </datalist>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">District</label>
                    <input type="text" name="district" class="form-control" value="Ahmadnagar">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">State</label>
                    <input type="text" name="state" class="form-control" value="Maharashtra">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Pincode</label>
                    <input type="text" name="pincode" class="form-control" maxlength="6">
                  </div>
                </div>
              </fieldset>
            </div>

            <!-- Right Column -->
            <div class="col-md-5">
              <fieldset class="form-fieldset h-100">
                <legend class="fw-bold text-uppercase text-primary small mb-3">Business & Finance</legend>
                
                <div class="row g-3">
                  <div class="col-12">
                    <label class="form-label">Farm / Company Name</label>
                    <input type="text" name="company_name" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">GSTIN</label>
                    <input type="text" name="gstin" class="form-control uppercase">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">PAN Number</label>
                    <input type="text" name="pan_number" class="form-control uppercase">
                  </div>
                  
                  <div class="col-md-6">
                    <label class="form-label">Credit Limit</label>
                    <input type="number" name="credit_limit" class="form-control" value="0">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Opening Balance</label>
                    <input type="number" name="opening_balance" class="form-control" value="0">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Credit Valid Till</label>
                    <input type="date" name="credit_valid_till" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Payment Terms</label>
                    <input type="text" name="payment_terms" class="form-control" list="payment-list" placeholder="e.g. 30 Days">
                    <datalist id="payment-list">
                      <option value="Cash Only">
                      <option value="Immediate">
                      <option value="7 Days">
                      <option value="15 Days">
                      <option value="30 Days">
                      <option value="45 Days">
                      <option value="End of Month">
                    </datalist>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Ledger Group</label>
                    <input type="text" name="ledger_group" class="form-control">
                  </div>

                  <div class="col-12 mt-3 pt-2 border-top">
                    <h4 class="subheader mb-2">Banking Information</h4>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Bank Name</label>
                    <input type="text" name="bank_name" class="form-control">
                  </div>
                  <div class="col-12">
                    <label class="form-label">Account Number</label>
                    <input type="text" name="account_number" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">IFSC Code</label>
                    <input type="text" name="ifsc_code" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Branch</label>
                    <input type="text" name="branch_name" class="form-control">
                  </div>

                  <div class="col-12 mt-3 pt-2 border-top">
                    <h4 class="subheader mb-2">Agriculture & Tags</h4>
                  </div>
                  <div class="col-md-7">
                    <label class="form-label">Land Area</label>
                    <div class="input-group">
                      <input type="number" name="land_area" class="form-control" step="0.01">
                      <select name="land_unit" class="form-select w-auto">
                        <option value="acre">Acre</option>
                        <option value="bigha">Bigha</option>
                        <option value="hectare">Hec</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-5">
                    <label class="form-label">Irrigation</label>
                    <select name="irrigation_type" class="form-select">
                      <option value="">Select...</option>
                      <option value="borewell">Borewell</option>
                      <option value="canal">Canal</option>
                      <option value="rainfed">Rainfed</option>
                    </select>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Active Crops Portfolio</label>
                    <select name="crops_master[]" id="crops-select" class="form-select" multiple>
                      @foreach($crops_master as $crop)
                        <option value="{{ $crop->id }}">{{ $crop->name }}</option>
                      @endforeach
                    </select>
                    <div class="text-secondary small mt-1">Select all crops currently grown by the farmer</div>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Search Tags (comma separated)</label>
                    <input type="text" name="tags_input" class="form-control" list="tags-list" placeholder="VIP, Credit, Local...">
                    <datalist id="tags-list">
                      <option value="VIP">
                      <option value="High Credit">
                      <option value="Regular">
                      <option value="New Lead">
                      <option value="Local">
                      <option value="Outstation">
                      <option value="Defaulter">
                    </datalist>
                  </div>
                </div>
              </fieldset>
            </div>
          </div>

          <div class="mt-3 pt-3 border-top">
            <div class="row g-3">
              <div class="col-lg-12">
                <label class="form-label">Internal Registration Notes</label>
                <textarea name="internal_notes" class="form-control" rows="2" placeholder="Special instructions..."></textarea>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer bg-light py-3 px-4">
          <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary ms-auto px-5 shadow-sm">
            Create & Open Profile
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Party (Simplified) -->
@foreach($parties as $party)
<div class="modal modal-blur fade" id="modal-edit-party-{{ $party->id }}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form action="{{ route('erp.parties.update', $party->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">Quick Edit: {{ $party->name }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">First Name</label>
              <input type="text" name="first_name" class="form-control" value="{{ $party->first_name }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Last Name</label>
              <input type="text" name="last_name" class="form-control" value="{{ $party->last_name }}">
            </div>
            <div class="col-12">
              <label class="form-label">Mobile</label>
              <input type="text" name="mobile" class="form-control" value="{{ $party->mobile }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Account Type</label>
                <select name="type" class="form-select">
                  <option value="customer" @selected($party->type == 'customer')>Customer</option>
                  <option value="farmer" @selected($party->type == 'farmer')>Farmer</option>
                  <option value="vendor" @selected($party->type == 'vendor')>Vendor</option>
                </select>
            </div>
          </div>
          <div class="mt-3 text-secondary small fst-italic">For full details editing, visit the record's profile page.</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary ms-auto">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableContainer = document.getElementById('table-container');
    const bulkForm = document.getElementById('bulk-action-form');
    const searchInput = document.getElementById('ajax-search');
    const typeFilter = document.getElementById('type-filter');
    let searchTimeout = null;

    function fetchParties(url = null) {
        if (!url) {
            url = new URL(window.location.href);
            url.searchParams.set('search', searchInput.value);
            url.searchParams.set('type', typeFilter.value);
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
                fetchParties();
            }, 500);
        });
    }

    if (typeFilter) {
        typeFilter.addEventListener('change', () => fetchParties());
    }

    document.addEventListener('change', function (e) {
        if (e.target && e.target.id === 'select-all') {
            const checkboxes = tableContainer.querySelectorAll('.party-checkbox');
            checkboxes.forEach(cb => cb.checked = e.target.checked);
        }
    });

    tableContainer.addEventListener('click', function (e) {
        const link = e.target.closest('#pagination-links a');
        if (link) {
            e.preventDefault();
            fetchParties(link.href);
        }
    });

    if (bulkForm) {
        bulkForm.addEventListener('submit', function (e) {
            const selectedIds = Array.from(tableContainer.querySelectorAll('.party-checkbox:checked')).map(cb => cb.value);
            if (selectedIds.length === 0) {
                e.preventDefault();
                alert('Please select at least one party.');
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

    @if(session('open_create_modal'))
        var modalEl = document.getElementById('modal-create-party');
        if (modalEl) {
            var myModal = new bootstrap.Modal(modalEl);
            myModal.show();
        }
    @endif

    // Initialize TomSelect for Crops
    if (document.getElementById('crops-select')) {
        new TomSelect('#crops-select', {
            plugins: ['remove_button'],
            maxItems: 20,
            placeholder: 'Search and select crops...',
            onItemAdd: function() {
                this.setTextboxValue('');
                this.refreshOptions();
            },
            render: {
                option: function(data, escape) {
                    return '<div class="py-2 px-3">' + escape(data.text) + '</div>';
                },
                item: function(data, escape) {
                    return '<div class="item text-primary-fg bg-primary">' + escape(data.text) + '</div>';
                }
            }
        });
    }
});
</script>
@endpush
