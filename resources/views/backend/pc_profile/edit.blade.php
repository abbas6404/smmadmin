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

                <div class="mb-3">
                    <label for="pc_name" class="form-label">PC Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('pc_name') is-invalid @enderror" 
                           id="pc_name" name="pc_name" value="{{ old('pc_name', $pcProfile->pc_name) }}" required>
                    @error('pc_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="hardware_id" class="form-label">Hardware ID <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('hardware_id') is-invalid @enderror" 
                           id="hardware_id" name="hardware_id" value="{{ old('hardware_id', $pcProfile->hardware_id) }}" required>
                     @error('hardware_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="max_profile_limit" class="form-label">Max Chrome Profiles <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('max_profile_limit') is-invalid @enderror" 
                               id="max_profile_limit" name="max_profile_limit" value="{{ old('max_profile_limit', $pcProfile->max_profile_limit) }}" min="1" required>
                         @error('max_profile_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="max_link_limit" class="form-label">Max Link Limit <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('max_link_limit') is-invalid @enderror" 
                               id="max_link_limit" name="max_link_limit" value="{{ old('max_link_limit', $pcProfile->max_link_limit) }}" min="1" required>
                        @error('max_link_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required {{ $pcProfile->trashed() ? 'disabled' : '' }}>
                            <option value="active" {{ old('status', $pcProfile->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $pcProfile->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="blocked" {{ old('status', $pcProfile->status) == 'blocked' ? 'selected' : '' }}>Blocked</option>
                            <option value="deleted" {{ old('status', $pcProfile->status) == 'deleted' ? 'selected' : '' }}>Deleted</option>
                        </select>
                         @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($pcProfile->trashed())
                            <small class="text-danger">Profile is deleted. Change status to restore.</small>
                        @endif
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>

</div>
@endsection 