@extends('backend.layouts.master')

@section('title', 'Edit Gmail Account')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Gmail Account</h1>
        <div>
            <a href="{{ route('admin.gmail.show', $gmail->id) }}" class="btn btn-info btn-sm">
                <i class="fas fa-eye fa-sm text-white-50"></i> View Account
            </a>
            <a href="{{ route('admin.gmail.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Account #{{ $gmail->id }} - {{ $gmail->email }}
            </h6>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.gmail.update', $gmail->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- PC Profile -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <label class="form-label">PC Profile</label>
                        <input type="text" class="form-control bg-light" 
                               value="{{ $gmail->pcProfile->pc_name ?? 'N/A' }} (ID: {{ $gmail->pc_profile_id }})" 
                               disabled readonly>
                        <div class="form-text">PC Profile cannot be changed after creation.</div>
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Email -->
                    <div class="col-md-6">
                        <label for="email" class="form-label required">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $gmail->email) }}" 
                               required>
                        <div class="form-text">Gmail account email address</div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="col-md-6">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" 
                               placeholder="Leave blank to keep current password">
                        <div class="form-text">Optional. Leave empty to keep the current password.</div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Status -->
                    <div class="col-md-6">
                        <label for="status" class="form-label required">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                            <option value="pending" {{ old('status', $gmail->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ old('status', $gmail->status) == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="active" {{ old('status', $gmail->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $gmail->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="remove" {{ old('status', $gmail->status) == 'remove' ? 'selected' : '' }}>Remove</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Account Features -->
                    <div class="col-md-6">
                        <label class="form-label d-block">Account Features</label>
                        <div class="form-check form-check-inline">
                            <input type="hidden" name="have_use" value="0">
                            <input class="form-check-input" type="checkbox" name="have_use" value="1" 
                                   id="have_use" {{ old('have_use', $gmail->have_use) ? 'checked' : '' }}>
                            <label class="form-check-label" for="have_use">Have Use</label>
                        </div>
                    </div>
                </div>

                <!-- Note -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <label for="note" class="form-label">Note</label>
                        <textarea class="form-control @error('note') is-invalid @enderror" 
                                  id="note" name="note" rows="3" 
                                  placeholder="Add any notes about this account">{{ old('note', $gmail->note) }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Account
                    </button>
                    <a href="{{ route('admin.gmail.show', $gmail->id) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.form-label.required::after {
    content: " *";
    color: red;
}
.form-text {
    font-size: 0.875em;
    color: #6c757d;
}
</style>
@endsection 