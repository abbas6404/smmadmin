@extends('backend.layouts.master')

@section('title', 'PC Profile Details')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">PC Profile Details</h1>
        <div>
            <a href="{{ route('admin.pc-profiles.edit', $pcProfile->id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit Profile
            </a>
            <a href="{{ route('admin.pc-profiles.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Basic Information Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Basic Information</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                            <a class="dropdown-item" href="{{ route('admin.pc-profiles.edit', $pcProfile->id) }}">
                                <i class="fas fa-edit fa-sm fa-fw mr-2 text-gray-400"></i>
                                Edit Profile
                            </a>
                            @if($pcProfile->trashed())
                                <form action="{{ route('admin.pc-profiles.restore', $pcProfile->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-trash-restore fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Restore Profile
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.pc-profiles.destroy', $pcProfile->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this profile?')">
                                        <i class="fas fa-trash fa-sm fa-fw mr-2"></i>
                                        Delete Profile
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted mb-1">PC Name</label>
                        <p class="font-weight-bold mb-0">{{ $pcProfile->pc_name }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted mb-1">Email</label>
                        <p class="font-weight-bold mb-0">{{ $pcProfile->email }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted mb-1">Hardware ID</label>
                        <p class="font-weight-bold mb-0">{{ $pcProfile->hardware_id }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted mb-1">Hostname</label>
                        <p class="font-weight-bold mb-0">{{ $pcProfile->hostname }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted mb-1">OS Version</label>
                        <p class="font-weight-bold mb-0">{{ $pcProfile->os_version ?? 'Not set' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted mb-1">User Agent</label>
                        <p class="font-weight-bold mb-0">{{ $pcProfile->user_agent ?? 'Not set' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted mb-1">Profile Root Directory</label>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary me-2">{{ substr($pcProfile->profile_root_directory, 0, 2) }}</span>
                            <p class="font-weight-bold mb-0">{{ substr($pcProfile->profile_root_directory, 2) ?? 'Not set' }}</p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted mb-1">Created At</label>
                        <p class="font-weight-bold mb-0">{{ $pcProfile->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted mb-1">Status</label>
                        <p class="mb-0">
                            <span class="badge bg-{{ 
                                $pcProfile->status == 'active' ? 'success' : 
                                ($pcProfile->status == 'inactive' ? 'secondary' : 
                                ($pcProfile->status == 'blocked' ? 'warning' : 'danger')) 
                            }}">
                                {{ ucfirst($pcProfile->status) }}
                            </span>
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted mb-1">Max Order Limit</label>
                        <p class="font-weight-bold mb-0">{{ $pcProfile->max_order_limit }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted mb-1">Min Order Limit</label>
                        <p class="font-weight-bold mb-0">{{ $pcProfile->min_order_limit }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted mb-1">Last Verified At</label>
                        <p class="font-weight-bold mb-0">
                            {{ $pcProfile->last_verified_at ? $pcProfile->last_verified_at->format('Y-m-d H:i') : 'Never' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Limits & Statistics Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Limits & Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row no-gutters align-items-center mb-3">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Chrome Profiles
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $pcProfile->getActiveChromesCount() }} / {{ $pcProfile->max_chrome_profiles }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fab fa-chrome fa-2x text-gray-300"></i>
                        </div>
                    </div>

                    <div class="row no-gutters align-items-center mb-3">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Gmail Accounts
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $pcProfile->getActiveGmailsCount() }} / {{ $pcProfile->max_gmail_accounts }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>

                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Facebook Accounts
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $pcProfile->getActiveFacebooksCount() }} / {{ $pcProfile->max_facebook_accounts }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fab fa-facebook fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Information Card -->
        <div class="col-xl-4 col-md-12 mb-4">
            <div class="card border-left-info shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">System Information</h6>
                </div>
                <div class="card-body">
                    @if($pcProfile->system_info && is_string($pcProfile->system_info))
                        @php 
                            try {
                                $sysInfo = json_decode($pcProfile->system_info, true);
                            } catch (\Exception $e) {
                                $sysInfo = null;
                            }
                        @endphp
                        @if($sysInfo)
                            @foreach($sysInfo as $key => $value)
                                <div class="mb-3">
                                    <label class="text-muted mb-1">{{ ucfirst($key) }}</label>
                                    <p class="font-weight-bold mb-0">
                                        @if(is_array($value))
                                            <pre class="mb-0">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                        @else
                                            {{ $value }}
                                        @endif
                                    </p>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">Invalid system information format</p>
                        @endif
                    @elseif(is_array($pcProfile->system_info))
                        @foreach($pcProfile->system_info as $key => $value)
                            <div class="mb-3">
                                <label class="text-muted mb-1">{{ ucfirst($key) }}</label>
                                <p class="font-weight-bold mb-0">
                                    @if(is_array($value))
                                        <pre class="mb-0">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                        {{ $value }}
                                    @endif
                                </p>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No system information available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card-header .dropdown-toggle::after {
    display: none;
}
.badge {
    font-size: 0.875em;
}
</style>
@endsection 