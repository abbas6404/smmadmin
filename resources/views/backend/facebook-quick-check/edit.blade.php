@extends('backend.layouts.master')

@section('title', 'Edit Facebook Quick Check Account')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Facebook Quick Check Account</h1>
        <div>
            <a href="{{ route('admin.facebook-quick-check.show', $account->id) }}" class="btn btn-info btn-sm">
                <i class="fas fa-eye fa-sm text-white-50"></i> View Account
            </a>
            <a href="{{ route('admin.facebook-quick-check.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Account #{{ $account->id }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.facebook-quick-check.update', $account->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Email Field -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label required">Email or Phone Number</label>
                                <input type="text" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $account->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Password Field -->
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" placeholder="Leave empty to keep current password">
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">Leave empty to keep the current password.</div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Two-Factor Secret Field -->
                            <div class="col-md-6 mb-3">
                                <label for="two_factor_secret" class="form-label">Two-Factor Secret</label>
                                <div class="input-group">
                                    <input type="text" class="form-control @error('two_factor_secret') is-invalid @enderror" 
                                           id="two_factor_secret" name="two_factor_secret" 
                                           value="{{ old('two_factor_secret', $account->two_factor_secret) }}">
                                    <button class="btn btn-outline-secondary toggle-input" type="button" data-target="two_factor_secret">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('two_factor_secret')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Status Field -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label required">Status</label>
                                <select class="form-control @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="pending" {{ old('status', $account->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="active" {{ old('status', $account->status) === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="in_use" {{ old('status', $account->status) === 'in_use' ? 'selected' : '' }}>In Use</option>
                                    <option value="blocked" {{ old('status', $account->status) === 'blocked' ? 'selected' : '' }}>Blocked</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Check Result Field -->
                            <div class="col-md-12 mb-3">
                                <label for="check_result" class="form-label">Check Result</label>
                                <input type="text" class="form-control @error('check_result') is-invalid @enderror" 
                                       id="check_result" name="check_result" 
                                       value="{{ old('check_result', $account->check_result) }}">
                                @error('check_result')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Notes Field -->
                            <div class="col-md-12 mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="4">{{ old('notes', $account->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Account
                            </button>
                            <a href="{{ route('admin.facebook-quick-check.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            @if(!$account->trashed())
                            <button type="button" class="btn btn-danger float-end" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash"></i> Move to Trash
                            </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
@if(!$account->trashed())
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Trash</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to move this account to trash?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.facebook-quick-check.destroy', $account->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Yes, Move to Trash</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    document.querySelector('.toggle-password').addEventListener('click', function() {
        var passwordInput = document.getElementById('password');
        var icon = this.querySelector('i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
    
    // Toggle 2FA input visibility
    document.querySelectorAll('.toggle-input').forEach(function(button) {
        button.addEventListener('click', function() {
            var targetId = this.getAttribute('data-target');
            var input = document.getElementById(targetId);
            var icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
});
</script>
@endpush 