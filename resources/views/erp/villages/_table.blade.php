<div class="table-responsive">
    <table class="table card-table table-vcenter text-nowrap datatable">
        <thead>
            <tr>
                <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" id="select-all"></th>
                <th>Village Name</th>
                <th>Pincode</th>
                <th>Post/SO Name</th>
                <th>Taluka</th>
                <th>District</th>
                <th>State</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($villages as $village)
            <tr>
                <td><input class="form-check-input m-0 align-middle village-checkbox" type="checkbox" value="{{ $village->id }}"></td>
                <td>
                    <div class="font-weight-medium">{{ $village->village_name }}</div>
                </td>
                <td><span class="badge bg-blue-lt">{{ $village->pincode }}</span></td>
                <td class="text-secondary small">{{ $village->post_so_name }}</td>
                <td class="text-secondary">{{ $village->taluka_name }}</td>
                <td class="text-secondary">{{ $village->district_name }}</td>
                <td class="text-secondary">{{ $village->state_name }}</td>
                <td class="text-end">
                    <div class="btn-list flex-nowrap justify-content-end">
                        @if($view === 'trash')
                            <form action="{{ route('erp.villages.restore', $village->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-success">Restore</button>
                            </form>
                            <form action="{{ route('erp.villages.force-delete', $village->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Permanently delete this village?')">Delete</button>
                            </form>
                        @else
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-edit-village-{{ $village->id }}">
                                Edit
                            </button>
                            <form action="{{ route('erp.villages.destroy', $village->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-ghost-danger">Trash</button>
                            </form>
                        @endif
                    </div>

                    @if($view !== 'trash')
                    <!-- Modal Edit Village (Inside table to ensure AJAX updates it) -->
                    <div class="modal modal-blur fade text-start" id="modal-edit-village-{{ $village->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <form action="{{ route('erp.villages.update', $village->id) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-header">
                              <h5 class="modal-title">Edit Village: {{ $village->village_name }}</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                      <div class="mb-3">
                                        <label class="form-label required">Village Name</label>
                                        <input type="text" name="village_name" class="form-control" value="{{ $village->village_name }}" required>
                                      </div>
                                    </div>
                                    <div class="col-lg-6">
                                      <div class="mb-3">
                                        <label class="form-label required">Pincode</label>
                                        <input type="text" name="pincode" class="form-control" value="{{ $village->pincode }}" maxlength="6" required>
                                      </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Post/SO Name</label>
                                    <input type="text" name="post_so_name" class="form-control" value="{{ $village->post_so_name }}">
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Taluka Name</label>
                                            <input type="text" name="taluka_name" class="form-control" value="{{ $village->taluka_name }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">District Name</label>
                                            <input type="text" name="district_name" class="form-control" value="{{ $village->district_name }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">State Name</label>
                                    <input type="text" name="state_name" class="form-control" value="{{ $village->state_name }}">
                                </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
                              <button type="submit" class="btn btn-primary ms-auto">Update Village</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-4 text-secondary">No villages found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="card-footer d-flex align-items-center" id="pagination-links">
    <p class="m-0 text-secondary small">
        Showing <span>{{ $villages->firstItem() ?? 0 }}</span> to <span>{{ $villages->lastItem() ?? 0 }}</span> of <span>{{ $villages->total() }}</span> entries
    </p>
    <div class="ms-auto">
        {{ $villages->links() }}
    </div>
</div>
