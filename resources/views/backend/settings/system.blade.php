@extends('backend.layouts.master')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">System Settings</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">System Notification Settings</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.settings.system.update') }}" method="POST">
                @csrf
                
                <div class="form-group mb-4">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="system_notification_active" name="system_notification_active" {{ isset($settings['system_notification_active']) && ($settings['system_notification_active'] == '1' || $settings['system_notification_active'] === true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="system_notification_active">Enable System Notification</label>
                    </div>
                    <small class="form-text text-muted">When enabled, users will see a notification on the services page and won't be able to place new orders.</small>
                </div>
                
                <div class="form-group mb-4">
                    <label for="system_notification_message">Notification Message</label>
                    <textarea class="form-control @error('system_notification_message') is-invalid @enderror" id="system_notification_message" name="system_notification_message" rows="3">{{ $settings['system_notification_message'] ?? 'We are currently experiencing high volume of orders. New orders will be accepted tomorrow. Thank you for your patience!' }}</textarea>
                    @error('system_notification_message')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                    <small class="form-text text-muted">This message will be displayed to users when the notification is active.</small>
                </div>
                
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</div>
@endsection 