@extends('backend.layouts.master')

@section('title', 'Edit PC Profile')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Edit PC Profile</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Edit Profile #{{ $pcProfile->id }} - {{ $pcProfile->pc_name }}</h6>
            <a href="{{ route('admin.pc-profiles.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pc-profiles.update', $pcProfile->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="pc_name" class="form-label">PC Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('pc_name') is-invalid @enderror" 
                               id="pc_name" name="pc_name" value="{{ old('pc_name', $pcProfile->pc_name) }}" required>
                        @error('pc_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $pcProfile->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="hardware_id" class="form-label">Hardware ID</label>
                        <input type="text" class="form-control @error('hardware_id') is-invalid @enderror" 
                               id="hardware_id" name="hardware_id" value="{{ old('hardware_id', $pcProfile->hardware_id) }}">
                        @error('hardware_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="hostname" class="form-label">Hostname</label>
                        <input type="text" class="form-control @error('hostname') is-invalid @enderror" 
                               id="hostname" name="hostname" value="{{ old('hostname', $pcProfile->hostname) }}">
                        @error('hostname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="os_version" class="form-label">OS Version</label>
                        <select class="form-control @error('os_version') is-invalid @enderror" id="os_version" name="os_version">
                            <option value="">Select OS Version</option>
                            <option value="Windows 10 Home" {{ old('os_version', $pcProfile->os_version) == 'Windows 10 Home' ? 'selected' : '' }}>Windows 10 Home</option>
                            <option value="Windows 10 Pro" {{ old('os_version', $pcProfile->os_version) == 'Windows 10 Pro' ? 'selected' : '' }}>Windows 10 Pro</option>
                            <option value="Windows 11 Home" {{ old('os_version', $pcProfile->os_version) == 'Windows 11 Home' ? 'selected' : '' }}>Windows 11 Home</option>
                            <option value="Windows 11 Pro" {{ old('os_version', $pcProfile->os_version) == 'Windows 11 Pro' ? 'selected' : '' }}>Windows 11 Pro</option>
                        </select>
                        @error('os_version')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="user_agent" class="form-label">User Agent</label>
                        <input type="text" class="form-control @error('user_agent') is-invalid @enderror" 
                               id="user_agent" name="user_agent" value="{{ old('user_agent', $pcProfile->user_agent) }}"
                               placeholder="Enter user agent range (e.g., 115-123)">
                        @error('user_agent')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Enter two numbers separated by hyphen where first number is less than second (e.g., 115-123)</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="profile_root_directory" class="form-label">Profile Root Directory <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select class="form-select" name="drive" required>
                                <option value="C" {{ old('drive', substr($pcProfile->profile_root_directory, 0, 1)) == 'C' ? 'selected' : '' }}>C:</option>
                                <option value="D" {{ old('drive', substr($pcProfile->profile_root_directory, 0, 1)) == 'D' ? 'selected' : '' }}>D:</option>
                                <option value="E" {{ old('drive', substr($pcProfile->profile_root_directory, 0, 1)) == 'E' ? 'selected' : '' }}>E:</option>
                                <option value="F" {{ old('drive', substr($pcProfile->profile_root_directory, 0, 1)) == 'F' ? 'selected' : '' }}>F:</option>
                            </select>
                            <input type="text" class="form-control @error('profile_root_directory') is-invalid @enderror" 
                                name="profile_root_directory" 
                                value="{{ old('profile_root_directory', substr($pcProfile->profile_root_directory, 2)) }}" 
                                placeholder="Enter folder name" required>
                            @error('profile_root_directory')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="text-muted">Enter the folder name where Chrome profiles will be stored (e.g., profiles)</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="max_profile_limit" class="form-label">Max Chrome Profiles <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('max_profile_limit') is-invalid @enderror" 
                               id="max_profile_limit" name="max_profile_limit" value="{{ old('max_profile_limit', $pcProfile->max_profile_limit) }}" min="1" required>
                        @error('max_profile_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="max_order_limit" class="form-label">Max Order Limit <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('max_order_limit') is-invalid @enderror" 
                               id="max_order_limit" name="max_order_limit" value="{{ old('max_order_limit', $pcProfile->max_order_limit) }}" min="1" required>
                        @error('max_order_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="min_order_limit" class="form-label">Min Order Limit <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('min_order_limit') is-invalid @enderror" 
                               id="min_order_limit" name="min_order_limit" value="{{ old('min_order_limit', $pcProfile->min_order_limit) }}" min="1" required>
                        @error('min_order_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Leave blank to keep current password</div>
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" 
                               id="password_confirmation" name="password_confirmation">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="active" {{ old('status', $pcProfile->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $pcProfile->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="blocked" {{ old('status', $pcProfile->status) == 'blocked' ? 'selected' : '' }}>Blocked</option>
                        <option value="deleted" {{ old('status', $pcProfile->status) == 'deleted' ? 'selected' : '' }}>Deleted</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Update Profile</button>
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