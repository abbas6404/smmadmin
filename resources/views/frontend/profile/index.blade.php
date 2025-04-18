@extends('frontend.layouts.master')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">My Profile</h4>
    </div>

    <!-- Profile Navigation -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link" id="personal-tab" data-bs-toggle="tab" href="#personal">
                <i class="fas fa-user me-2"></i>Personal Information
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="security-tab" data-bs-toggle="tab" href="#security">
                <i class="fas fa-shield-alt me-2"></i>Security Settings
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="password-tab" data-bs-toggle="tab" href="#password">
                <i class="fas fa-key me-2"></i>Change Password
            </a>
        </li>
    </ul>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Personal Information -->
        <div class="tab-pane fade" id="personal">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', auth()->user()->name) }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', auth()->user()->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="timezone" class="form-label">Timezone</label>
                                    <select class="form-select @error('timezone') is-invalid @enderror" id="timezone" name="timezone">
                                        <option value="">Select Timezone</option>
                                        @foreach(timezone_identifiers_list() as $timezone)
                                            <option value="{{ $timezone }}" {{ old('timezone', auth()->user()->timezone) == $timezone ? 'selected' : '' }}>
                                                {{ $timezone }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('timezone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="country" class="form-label">Country</label>
                                    <select class="form-select @error('country') is-invalid @enderror" id="country" name="country">
                                        <option value="">Select Country</option>
                                        @foreach(config('countries') as $code => $name)
                                            <option value="{{ $code }}" {{ old('country', auth()->user()->country) == $code ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="tab-pane fade" id="security">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('profile.security') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <h5>Two-Factor Authentication</h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="two_factor" name="two_factor"
                                       {{ auth()->user()->two_factor_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="two_factor">Enable Two-Factor Authentication</label>
                            </div>
                            <small class="text-muted">
                                When enabled, you'll be required to enter a security code sent to your email when logging in from a new device.
                            </small>
                        </div>

                        <div class="mb-4">
                            <h5>Login Notifications</h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="login_notifications" name="login_notifications"
                                       {{ auth()->user()->login_notifications_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="login_notifications">Enable Login Notifications</label>
                            </div>
                            <small class="text-muted">
                                Receive email notifications when someone logs into your account.
                            </small>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                Save Security Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="tab-pane fade" id="password">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                           id="current_password" name="current_password">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation">
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary px-4">
                                        Change Password
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get tab from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        
        // Set default tab to personal if no tab parameter
        const targetTab = tab ? tab : 'personal';
        
        // Activate the correct tab
        const tabElement = document.getElementById(targetTab + '-tab');
        const tabContent = document.getElementById(targetTab);
        
        if (tabElement && tabContent) {
            // Remove active class from all tabs
            document.querySelectorAll('.nav-link').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            // Activate the target tab
            tabElement.classList.add('active');
            tabContent.classList.add('show', 'active');
        }
        
        // Update URL when tabs are clicked
        document.querySelectorAll('.nav-link').forEach(tab => {
            tab.addEventListener('click', function(e) {
                const tabId = this.id.replace('-tab', '');
                if (tabId === 'personal') {
                    history.pushState({}, '', '{{ route("profile.index") }}');
                } else {
                    history.pushState({}, '', '{{ route("profile.index") }}?tab=' + tabId);
                }
            });
        });
    });
</script>
@endpush 