<div class="modal modal-blur fade" id="modal-edit-profile" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content border-0 shadow-lg">
      <form action="{{ route('erp.parties.update', $party->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">Edit Profile: {{ $party->name }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body" style="max-height: 70vh; overflow-y: auto; overflow-x: hidden;">
          <div class="row g-4 pb-4">
            <!-- Left Column: Primary Identity -->
            <div class="col-md-7">
              <fieldset class="form-fieldset bg-primary-lt border-primary">
                <legend class="fw-bold text-uppercase text-primary small mb-3">Mandatory Information</legend>
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label required">First Name</label>
                    <input type="text" name="first_name" class="form-control border-primary" value="{{ $party->first_name }}" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Middle Name</label>
                    <input type="text" name="middle_name" class="form-control" value="{{ $party->middle_name }}">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="{{ $party->last_name }}">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label required">Mobile Number</label>
                    <input type="text" name="mobile" class="form-control border-primary" value="{{ $party->mobile }}" required maxlength="10">
                  </div>
                  
                  <div class="col-md-6">
                    <label class="form-label required">Account Type</label>
                    <select name="type" class="form-select border-primary" required>
                      @foreach($account_types as $at)
                        <option value="{{ $at->slug }}" @selected($party->type == $at->slug)>{{ $at->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-md-12">
                    <label class="form-label required">Category</label>
                    <div class="form-selectgroup w-100">
                      <label class="form-selectgroup-item flex-fill">
                        <input type="radio" name="category" value="individual" class="form-selectgroup-input" @checked($party->category == 'individual')>
                        <span class="form-selectgroup-label py-2 border-primary">Individual</span>
                      </label>
                      <label class="form-selectgroup-item flex-fill">
                        <input type="radio" name="category" value="business" class="form-selectgroup-input" @checked($party->category == 'business')>
                        <span class="form-selectgroup-label py-2 border-primary">Business</span>
                      </label>
                    </div>
                  </div>
                </div>
              </fieldset>

              <fieldset class="form-fieldset mt-4">
                <legend class="fw-bold text-uppercase text-secondary small mb-3">Optional Contact</legend>
                <div class="row g-3">
                  <div class="col-md-12">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ $party->email }}">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Secondary Phone</label>
                    <input type="text" name="phone_number_2" class="form-control" value="{{ $party->phone_number_2 }}">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Relative Phone</label>
                    <input type="text" name="relative_phone" class="form-control" value="{{ $party->relative_phone }}">
                  </div>
                  <div class="col-md-12">
                    <label class="form-label">Aadhaar (Last 4)</label>
                    <input type="text" name="aadhaar_last4" class="form-control" value="{{ $party->aadhaar_last4 }}" maxlength="4">
                  </div>
                </div>
              </fieldset>

              <fieldset class="form-fieldset mt-4">
                <legend class="fw-bold text-uppercase text-primary small mb-3">Agriculture Portfolio</legend>
                <div class="row g-3">
                  <div class="col-md-7">
                    <label class="form-label">Land Area</label>
                    <div class="input-group">
                      <input type="number" name="land_area" class="form-control" step="0.01" value="{{ $party->land_area }}">
                      <select name="land_unit" class="form-select w-auto">
                        @foreach($land_units as $lu)
                          <option value="{{ $lu->code }}" @selected($party->land_unit == $lu->code)>{{ $lu->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-md-5">
                    <label class="form-label">Irrigation</label>
                    <select name="irrigation_type" class="form-select">
                      <option value="">Select...</option>
                      @foreach($irrigation_types as $it)
                        <option value="{{ $it->name }}" @selected($party->irrigation_type == $it->name)>{{ $it->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Active Crops Portfolio</label>
                    <select name="crops_master[]" id="edit-crops-select" class="form-select" multiple>
                      @foreach($crops_master as $crop)
                        <option value="{{ $crop->id }}" @selected($party->crops_list && $party->crops_list->contains($crop->id))>{{ $crop->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </fieldset>
            </div>

            <!-- Right Column: Business Details -->
            <div class="col-md-5" id="edit-business-section">
              <fieldset class="form-fieldset h-100">
                <legend class="fw-bold text-uppercase text-primary small mb-3">Business & Finance</legend>
                
                <div class="row g-3">
                  <div class="col-12">
                    <label class="form-label">Farm / Company Name</label>
                    <input type="text" name="company_name" class="form-control mb-3" value="{{ $party->company_name }}">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">GSTIN</label>
                            <input type="text" name="gstin" class="form-control uppercase" value="{{ $party->gstin }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">PAN Number</label>
                            <input type="text" name="pan_number" class="form-control uppercase" value="{{ $party->pan_number }}">
                        </div>
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <label class="form-label">Credit Limit</label>
                    <input type="number" name="credit_limit" class="form-control" value="{{ $party->credit_limit }}">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Opening Balance</label>
                    <input type="number" name="opening_balance" class="form-control" value="{{ $party->opening_balance }}">
                  </div>

                  <div class="col-12 mt-3 pt-2 border-top">
                    <h4 class="subheader mb-2">Banking Information</h4>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Bank Name</label>
                    <input type="text" name="bank_name" class="form-control" value="{{ $party->bank_name }}">
                  </div>
                  <div class="col-12">
                    <label class="form-label">Account Number</label>
                    <input type="text" name="account_number" class="form-control" value="{{ $party->account_number }}">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">IFSC Code</label>
                    <input type="text" name="ifsc_code" class="form-control" value="{{ $party->ifsc_code }}">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Branch</label>
                    <input type="text" name="branch_name" class="form-control" value="{{ $party->branch_name }}">
                  </div>
                </div>
              </fieldset>
            </div>
          </div>

          <div class="mt-3 pt-3 border-top">
            <div class="row g-3">
              <div class="col-lg-12">
                <label class="form-label">Internal Notes</label>
                <textarea name="internal_notes" class="form-control" rows="2">{{ $party->internal_notes }}</textarea>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer bg-surface-secondary py-3 px-4">
          <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary ms-auto px-5 shadow-sm">
            Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editCategoryRadios = document.querySelectorAll('#modal-edit-profile input[name="category"]');
    const editBusinessSection = document.getElementById('edit-business-section');
    
    function toggleEditBusinessSection() {
        const selected = document.querySelector('#modal-edit-profile input[name="category"]:checked').value;
        if (selected === 'business') {
            editBusinessSection.style.display = 'block';
        } else {
            editBusinessSection.style.display = 'none';
        }
    }

    editCategoryRadios.forEach(radio => {
        radio.addEventListener('change', toggleEditBusinessSection);
    });

    toggleEditBusinessSection();

    // Initialize TomSelect for crops in edit modal
    if (document.getElementById('edit-crops-select')) {
        new TomSelect('#edit-crops-select', {
            plugins: ['remove_button'],
            maxItems: 20,
            placeholder: 'Search and select crops...'
        });
    }
});
</script>
