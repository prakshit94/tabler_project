@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">
          Create User
        </h2>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="card">
      <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
          @csrf
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Full Name</label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
              @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Email Address</label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
              @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Mobile Number</label>
              <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile') }}" required>
              @error('mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select @error('status') is-invalid @enderror">
                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                <option value="blocked" {{ old('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
              </select>
              @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
              @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-12 mb-3">
              <label class="form-label">Roles</label>
              <div class="form-selectgroup">
                @foreach($roles as $role)
                <label class="form-selectgroup-item">
                  <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="form-selectgroup-input">
                  <span class="form-selectgroup-label">{{ $role->name }}</span>
                </label>
                @endforeach
              </div>
            </div>
          </div>
          <div class="form-footer">
            <button type="submit" class="btn btn-primary">Save User</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-link">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
