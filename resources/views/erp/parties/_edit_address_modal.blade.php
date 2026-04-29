<div class="modal modal-blur fade" id="modal-edit-address" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content shadow-lg border-0">
            <form id="edit-address-form" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title h2 font-weight-bold">Edit Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="form-section-title">General Information</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Address Type</label>
                            <select name="type" id="edit_type" class="form-select">
                                <option value="shipping">Shipping</option>
                                <option value="billing">Billing</option>
                                <option value="both">Both</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Address Label</label>
                            <input type="text" name="label" id="edit_label" class="form-control" placeholder="Home, Office...">
                        </div>
                    </div>

                    <div class="form-section-title">Street & Area</div>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label required">Address Line 1</label>
                            <input type="text" name="address_line1" id="edit_address_line1" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address Line 2</label>
                            <input type="text" name="address_line2" id="edit_address_line2" class="form-control">
                        </div>
                    </div>

                    <div class="form-section-title">City / Region Details</div>
                    <div class="modal-body-section">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Village</label>
                                <input type="text" name="village" id="edit_village" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Taluka</label>
                                <input type="text" name="taluka" id="edit_taluka" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">District</label>
                                <input type="text" name="district" id="edit_district" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">State</label>
                                <input type="text" name="state" id="edit_state" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Pincode</label>
                                <input type="text" name="pincode" id="edit_pincode" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Post Office</label>
                                <input type="text" name="post_office" id="edit_post_office" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_default" id="edit_is_default" value="1">
                            <span class="form-check-label font-weight-bold">Mark this as primary shipping address</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-lg px-4 ms-auto">Update Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editAddress(address) {
    const form = document.getElementById('edit-address-form');
    form.action = `/erp/parties/{{ $party->id }}/addresses/${address.id}`;
    
    document.getElementById('edit_type').value = address.type;
    document.getElementById('edit_label').value = address.label || '';
    document.getElementById('edit_address_line1').value = address.address_line1 || '';
    document.getElementById('edit_address_line2').value = address.address_line2 || '';
    document.getElementById('edit_village').value = address.village || '';
    document.getElementById('edit_taluka').value = address.taluka || '';
    document.getElementById('edit_district').value = address.district || '';
    document.getElementById('edit_state').value = address.state || '';
    document.getElementById('edit_pincode').value = address.pincode || '';
    document.getElementById('edit_post_office').value = address.post_office || '';
    document.getElementById('edit_is_default').checked = !!address.is_default;
    
    new bootstrap.Modal(document.getElementById('modal-edit-address')).show();
}
</script>
