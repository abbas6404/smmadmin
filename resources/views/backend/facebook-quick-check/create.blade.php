@extends('backend.layouts.master')

@section('title', 'Add Facebook Accounts for Quick Check')

@section('content')
<div class="container-fluid">
    <!-- Success Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i>
        <strong>Success!</strong> {!! nl2br(e(session('success'))) !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Error Messages -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-times-circle me-1"></i>
        <strong>Error!</strong> {!! nl2br(e(session('error'))) !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Display specific errors -->
    @if(session('errorList') && is_array(session('errorList')))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-1"></i>
        <strong>Errors:</strong>
        <ul class="mt-2 mb-0">
            @foreach(session('errorList') as $error)
                <li>{!! nl2br(e($error)) !!}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add Facebook Accounts for Quick Check</h1>
        <a href="{{ route('admin.facebook-quick-check.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.facebook-quick-check.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <!-- Email and Password Input -->
                    <div class="col-md-12 mb-3">
                        <label for="accounts" class="form-label required">Email|Password|2FA Combinations</label>
                        <textarea name="accounts" id="accounts" class="form-control {{ $errors->has('accounts') ? 'is-invalid' : '' }}" 
                                rows="15" required placeholder="Enter one email|password|2fa combination per line, example:&#10;user1@gmail.com|password1|2FA_SECRET&#10;user2@gmail.com|password2|2FA_SECRET">{{ old('accounts') }}</textarea>
                        @if($errors->has('accounts'))
                            <div class="invalid-feedback">{{ $errors->first('accounts') }}</div>
                        @endif
                        <div class="form-text">
                            Enter each account on a new line in the format: email|password|2fa<br>
                            The 2FA code is optional.<br>
                            Examples:<br>
                            user1@gmail.com|password123|K35A YRDA ADHP FIWT XCBU SRJS O54L LCTS<br>
                            user2@gmail.com|password456
                        </div>
                    </div>
                </div>

                <!-- Duplicate handling information -->
                <div class="alert alert-info mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle me-2 fa-lg"></i>
                        <strong>Important:</strong>
                    </div>
                    <ul class="mb-0 mt-2">
                        <li>Duplicate emails will be automatically skipped</li>
                        <li>A report will be shown with successful and failed accounts</li>
                        <li>You can check existing accounts in the <a href="{{ route('admin.facebook-quick-check.index') }}">accounts list</a></li>
                    </ul>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Accounts
                    </button>
                    <a href="{{ route('admin.facebook-quick-check.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 