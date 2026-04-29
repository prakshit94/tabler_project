<style>
    #village-results {
        border: 1px solid var(--tblr-border-color);
        border-radius: 4px;
        margin-top: 2px;
        background-color: var(--tblr-bg-surface) !important;
        box-shadow: var(--tblr-shadow-lg) !important;
        z-index: 1060;
    }
    #village-results .list-group-item {
        cursor: pointer;
        border-left: 3px solid transparent;
    }
    #village-results .list-group-item:hover {
        background-color: var(--tblr-bg-surface-secondary) !important;
        border-left: 3px solid var(--tblr-primary) !important;
    }
    .modal-body-section {
        background: var(--tblr-bg-surface-secondary);
        padding: 1.5rem;
        border-radius: 8px;
        border: 1px solid var(--tblr-border-color);
        margin-bottom: 1rem;
    }
    .form-section-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #616876;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }
    .form-section-title::after {
        content: "";
        flex: 1;
        height: 1px;
        background: var(--tblr-border-color);
        margin-left: 1rem;
    }
</style>

<div class="modal modal-blur fade" id="modal-add-address" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content shadow-lg border-0">
            <form action="{{ route('erp.parties.addresses.store', $party->id) }}" method="POST">
                @csrf
                <input type="hidden" name="active_tab" class="modal-active-tab-input" value="v-pills-profile-tab">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <!-- Section 1: Basic Info -->
                    <div class="form-section-title">General Information</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Address Type</label>
                            <div class="form-selectgroup">
                                <label class="form-selectgroup-item">
                                    <input type="radio" name="type" value="shipping" class="form-selectgroup-input" checked>
                                    <span class="form-selectgroup-label">Shipping</span>
                                </label>
                                <label class="form-selectgroup-item">
                                    <input type="radio" name="type" value="billing" class="form-selectgroup-input">
                                    <span class="form-selectgroup-label">Billing</span>
                                </label>
                                <label class="form-selectgroup-item">
                                    <input type="radio" name="type" value="both" class="form-selectgroup-input">
                                    <span class="form-selectgroup-label">Both</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Address Label</label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7l6 -3l6 3l6 -3v13l-6 3l-6 -3l-6 3v-13" /><path d="M9 4v13" /><path d="M15 7v13" /></svg>
                                </span>
                                <input type="text" name="label" class="form-control" placeholder="Home, Office, Farm, Warehouse...">
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Location Search -->
                    <div class="form-section-title">Location Auto-fill</div>
                    <div class="mb-4">
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                            </span>
                            <input type="text" id="village-search-input" class="form-control form-control-lg border-primary" placeholder="Type Village Name or Pincode to auto-fill details..." autocomplete="off">
                        </div>
                        <div id="village-results" class="list-group position-absolute w-100 shadow-xl" style="z-index: 1060; display: none; max-height: 280px; overflow-y: auto;"></div>
                    </div>

                    <!-- Section 3: Address Details -->
                    <div class="form-section-title">Street & Area</div>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label required">Address Line 1</label>
                            <input type="text" name="address_line1" id="address_line1" class="form-control" required placeholder="House No, Street, Landmark...">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address Line 2</label>
                            <input type="text" name="address_line2" id="address_line2" class="form-control" placeholder="Area, Locality...">
                        </div>
                    </div>

                    <!-- Section 4: Geographical Details -->
                    <div class="form-section-title">City / Region Details</div>
                    <div class="modal-body-section">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Village</label>
                                <input type="text" name="village" id="village" class="form-control font-weight-bold">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Taluka</label>
                                <input type="text" name="taluka" id="taluka" class="form-control font-weight-bold">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">District</label>
                                <input type="text" name="district" id="district" class="form-control font-weight-bold">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">State</label>
                                <input type="text" name="state" id="state" class="form-control font-weight-bold">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Pincode</label>
                                <input type="text" name="pincode" id="pincode" class="form-control font-weight-bold">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Post Office</label>
                                <input type="text" name="post_office" id="post_office" class="form-control font-weight-bold">
                            </div>
                        </div>
                    </div>

                    <div class="modal-body-section bg-primary-lt border-primary mb-0">
                        <label class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="is_default" value="1">
                            <span class="form-check-label h4 mb-0">Set as Default Address</span>
                            <small class="form-hint text-primary">This address will be selected by default for new orders and invoices.</small>
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-lg px-4 ms-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
                        Save Address
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('village-search-input');
    const resultsDiv = document.getElementById('village-results');

    if (searchInput) {
        let debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const q = this.value;
            
            if(q.length < 2) {
                resultsDiv.style.display = 'none';
                return;
            }

            debounceTimer = setTimeout(() => {
                const url = '{{ route("erp.villages.search") }}?q=' + encodeURIComponent(q);
                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        resultsDiv.innerHTML = '';
                        if(data.length > 0) {
                            data.forEach(item => {
                                const btn = document.createElement('button');
                                btn.type = 'button';
                                btn.className = 'list-group-item list-group-item-action py-3 border-bottom';
                                btn.innerHTML = `
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong class="text-primary h4 mb-0">${item.village_name}</strong>
                                        <span class="badge bg-blue text-white shadow-sm">${item.pincode}</span>
                                    </div>
                                    <div class="text-dark small mb-1">
                                        <strong>Taluka:</strong> ${item.taluka_name} | <strong>District:</strong> ${item.district_name}
                                    </div>
                                    <div class="text-secondary small" style="font-size: 11px;">
                                        State: ${item.state_name} ${item.post_so_name ? '| PO: ' + item.post_so_name : ''}
                                    </div>
                                `;
                                btn.onclick = () => {
                                    document.getElementById('village').value = item.village_name;
                                    document.getElementById('pincode').value = item.pincode;
                                    document.getElementById('taluka').value = item.taluka_name;
                                    document.getElementById('district').value = item.district_name;
                                    document.getElementById('state').value = item.state_name;
                                    document.getElementById('post_office').value = item.post_so_name || '';
                                    
                                    resultsDiv.style.display = 'none';
                                    searchInput.value = item.village_name + ' (' + item.pincode + ')';
                                    
                                    setTimeout(() => document.getElementById('address_line1').focus(), 100);
                                };
                                resultsDiv.appendChild(btn);
                            });
                            resultsDiv.style.display = 'block';
                        } else {
                            resultsDiv.style.display = 'none';
                        }
                    });
            }, 300);
        });

        document.addEventListener('click', function(e) {
            if(!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
                resultsDiv.style.display = 'none';
            }
        });
    }
});
</script>
