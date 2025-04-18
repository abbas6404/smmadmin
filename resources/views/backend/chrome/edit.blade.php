@extends('backend.layouts.master')

@section('title', 'Edit Chrome Profile')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Edit Chrome Profile</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Edit Profile #{{ $chrome->id }}</h6>
            <a href="{{ route('admin.chrome.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.chrome.update', $chrome->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="pc_profile_id" class="form-label">PC Profile</label>
                        <select class="form-control" id="pc_profile_id" name="pc_profile_id" disabled>
                            <option>{{ $chrome->pcProfile->pc_name ?? 'N/A' }} (ID: {{ $chrome->pc_profile_id }})</option>
                        </select>
                        <small class="text-muted">PC Profile cannot be changed.</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="pending" {{ old('status', $chrome->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="active" {{ old('status', $chrome->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $chrome->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="remove" {{ old('status', $chrome->status) == 'remove' ? 'selected' : '' }}>Remove</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="profile_directory" class="form-label">Profile Directory</label>
                    <input type="text" class="form-control @error('profile_directory') is-invalid @enderror" 
                           id="profile_directory" name="profile_directory" value="{{ old('profile_directory', $chrome->profile_directory) }}" required>
                    @error('profile_directory')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="user_agent" class="form-label">User Agent</label>
                    <textarea class="form-control @error('user_agent') is-invalid @enderror" 
                              id="user_agent" name="user_agent" rows="3">{{ old('user_agent', $chrome->user_agent) }}</textarea>
                    @error('user_agent')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>

</div>
@endsection 