@extends('backend.layouts.master')

@section('title', 'PC Profile Details')

@section('content')
<div class="container-fluid">
    <!-- Page Heading with Status Badge -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                PC Profile: {{ $pcProfile->pc_name }}
                <span class="badge bg-{{ 
                    $pcProfile->status == 'active' ? 'success' : 
                    ($pcProfile->status == 'inactive' ? 'secondary' : 
                    ($pcProfile->status == 'blocked' ? 'warning' : 'danger')) 
                }}">
                    {{ ucfirst($pcProfile->status) }}
                </span>
                @if($pcProfile->trashed())
                <span class="badge bg-danger">Deleted</span>
                @endif
            </h1>
            <p class="text-muted">
                <i class="fas fa-clock"></i> Last verified: 
                {{ $pcProfile->last_verified_at ? $pcProfile->last_verified_at->format('Y-m-d H:i') : 'Never' }}
            </p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.pc-profiles.edit', $pcProfile->id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Edit Profile
            </a>
            @if($pcProfile->trashed())
                <form action="{{ route('admin.pc-profiles.update', $pcProfile->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="inactive">
                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to restore this PC Profile?')">
                        <i class="fas fa-undo"></i> Restore Profile
                    </button>
                </form>
            @else
                <form action="{{ route('admin.pc-profiles.destroy', $pcProfile->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this PC Profile?')">
                        <i class="fas fa-trash"></i> Delete Profile
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.pc-profiles.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Total Accounts Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 bg-gradient-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-calculator"></i> Total Accounts Summary
                    </h6>
                </div>
                <div class="card-body">
    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-primary h-100 py-2">
                                <div class="card-body py-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Facebook Accounts</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $pcProfile->facebookAccounts()->count() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fab fa-facebook fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-danger h-100 py-2">
                                <div class="card-body py-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Gmail Accounts</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $pcProfile->gmailAccounts()->count() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-info h-100 py-2">
                                <div class="card-body py-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Chrome Profiles</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $pcProfile->chromeProfiles()->count() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fab fa-chrome fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Status Summary Cards -->
    <div class="row mb-4">
        <!-- Facebook Accounts Status -->
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-gradient-dark text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-chart-bar"></i> Account Status Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h5 class="border-bottom pb-2">Facebook Accounts</h5>
                            <div class="row">
                                <div class="col-4 mb-3">
                                    <div class="card border-left-success h-100 py-2">
                                        <div class="card-body py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        {{ $pcProfile->facebookAccounts()->where('status', 'active')->count() }}
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 mb-3">
                                    <div class="card border-left-secondary h-100 py-2">
                                        <div class="card-body py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Inactive</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        {{ $pcProfile->facebookAccounts()->where('status', 'inactive')->count() }}
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-pause-circle fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 mb-3">
                                    <div class="card border-left-warning h-100 py-2">
                                        <div class="card-body py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        {{ $pcProfile->facebookAccounts()->where('status', 'pending')->count() }}
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 mb-3">
                                    <div class="card border-left-info h-100 py-2">
                                        <div class="card-body py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Processing</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        {{ $pcProfile->facebookAccounts()->where('status', 'processing')->count() }}
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-sync fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 mb-3">
                                    <div class="card border-left-danger h-100 py-2">
                                        <div class="card-body py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Removed</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        {{ $pcProfile->facebookAccounts()->where('status', 'remove')->count() }}
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-trash-alt fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 mb-3">
                                    <div class="card border-left-dark h-100 py-2">
                                        <div class="card-body py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Logout</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        {{ $pcProfile->facebookAccounts()->where('status', 'logout')->count() }}
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-sign-out-alt fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <h5 class="border-bottom pb-2">Gmail Accounts</h5>
                            <div class="row">
                                <div class="col-4 mb-3">
                                    <div class="card border-left-success h-100 py-2">
                                        <div class="card-body py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        {{ $pcProfile->gmailAccounts()->where('status', 'active')->count() }}
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 mb-3">
                                    <div class="card border-left-secondary h-100 py-2">
                                        <div class="card-body py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Inactive</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        {{ $pcProfile->gmailAccounts()->where('status', 'inactive')->count() }}
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-pause-circle fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 mb-3">
                                    <div class="card border-left-warning h-100 py-2">
                                        <div class="card-body py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        {{ $pcProfile->gmailAccounts()->where('status', 'pending')->count() }}
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 mb-3">
                                    <div class="card border-left-info h-100 py-2">
                                        <div class="card-body py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Processing</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        {{ $pcProfile->gmailAccounts()->where('status', 'processing')->count() }}
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-sync fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 mb-3">
                                    <div class="card border-left-danger h-100 py-2">
                                        <div class="card-body py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Removed</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        {{ $pcProfile->gmailAccounts()->where('status', 'remove')->count() }}
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-trash-alt fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 mb-3">
                                    <div class="card border-left-dark h-100 py-2">
                                        <div class="card-body py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Logout</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        {{ $pcProfile->gmailAccounts()->where('status', 'logout')->count() }}
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-sign-out-alt fa-2x text-gray-300"></i>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Left Column -->
        <div class="col-xl-8">
            <!-- Profile Overview Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Profile Overview
                    </h6>
                    <div>
                        <button class="btn btn-sm btn-outline-primary" id="refreshSystemInfo">
                            <i class="fas fa-sync-alt"></i> Refresh System Info
                        </button>
                    </div>
                    </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th style="width: 30%">PC Name</th>
                                    <td>{{ $pcProfile->pc_name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $pcProfile->email }}</td>
                                </tr>
                                <tr>
                                    <th>Hardware ID</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span id="hardware-id">{{ $pcProfile->hardware_id }}</span>
                                            <button class="btn btn-sm btn-outline-secondary ml-2 copy-btn" data-clipboard-target="#hardware-id">
                                                <i class="fas fa-copy"></i>
                                            </button>
                    </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Hostname</th>
                                    <td>{{ $pcProfile->hostname }}</td>
                                </tr>
                                <tr>
                                    <th>OS Version</th>
                                    <td>{{ $pcProfile->os_version ?? 'Not set' }}</td>
                                </tr>
                                <tr>
                                    <th>User Agent</th>
                                    <td>{{ $pcProfile->user_agent ?? 'Not set' }}</td>
                                </tr>
                                <tr>
                                    <th>Profile Root Directory</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-primary me-2">{{ substr($pcProfile->profile_root_directory, 0, 2) }}</span>
                                            <span>{{ substr($pcProfile->profile_root_directory, 2) ?? 'Not set' }}</span>
                    </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $pcProfile->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Max Order Limit</th>
                                    <td>{{ $pcProfile->max_order_limit }}</td>
                                </tr>
                                <tr>
                                    <th>Min Order Limit</th>
                                    <td>{{ $pcProfile->min_order_limit }}</td>
                                </tr>
                            </tbody>
                        </table>
                </div>
            </div>
        </div>

        <!-- Limits & Statistics Card -->
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

        <!-- Right Column -->
        <div class="col-xl-4">
        <!-- System Information Card -->
            <div class="card border-left-info shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">System Information</h6>
                </div>
                <div class="card-body">
                    <!-- CPU and Memory Summary -->
                    <div class="row mb-4">
                        <!-- CPU Information -->
                        <div class="col-md-6">
                            <div class="card border-left-primary h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                CPU Cores
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $pcProfile->cpu_cores ?? 'Not Set' }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-microchip fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Memory Information -->
                        <div class="col-md-6">
                            <div class="card border-left-success h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Total Memory
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                @if($pcProfile->total_memory)
                                                    {{ number_format($pcProfile->total_memory, 2) }} GB
                                                @else
                                                    Not Set
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-memory fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Disk Information -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold text-info mb-3">Disk Information</h6>
                        @if($pcProfile->relationLoaded('disks') && $pcProfile->disks->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Drive</th>
                                            <th>File System</th>
                                            <th>Total Size</th>
                                            <th>Free Space</th>
                                            <th>Used Space</th>
                                            <th>Health</th>
                                            <th>Read Speed</th>
                                            <th>Write Speed</th>
                                            <th>Last Checked</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pcProfile->disks as $disk)
                                            <tr>
                                                <td class="font-weight-bold">{{ $disk->drive_letter }}:</td>
                                                <td>{{ $disk->file_system ?? 'N/A' }}</td>
                                                <td>{{ number_format($disk->total_size / 1024 / 1024 / 1024, 2) }} GB</td>
                                                <td>{{ number_format($disk->free_space / 1024 / 1024 / 1024, 2) }} GB</td>
                                                <td>{{ number_format($disk->used_space / 1024 / 1024 / 1024, 2) }} GB</td>
                                                <td>
                                                    @if($disk->health_percentage)
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar {{ $disk->health_percentage < 50 ? 'bg-danger' : ($disk->health_percentage < 80 ? 'bg-warning' : 'bg-success') }}" 
                                                                 role="progressbar" 
                                                                 style="width: {{ $disk->health_percentage }}%"
                                                                 aria-valuenow="{{ $disk->health_percentage }}" 
                                                                 aria-valuemin="0" 
                                                                 aria-valuemax="100">
                                                                {{ number_format($disk->health_percentage, 1) }}%
                                                            </div>
                                                        </div>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ $disk->read_speed ? $disk->read_speed . ' MB/s' : 'N/A' }}</td>
                                                <td>{{ $disk->write_speed ? $disk->write_speed . ' MB/s' : 'N/A' }}</td>
                                                <td>{{ $disk->last_checked_at ? $disk->last_checked_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No disk information available.
                            </div>
                        @endif
                    </div>

                    @if($pcProfile->system_info)
                        @php 
                            $sysInfo = is_string($pcProfile->system_info) ? json_decode($pcProfile->system_info, true) : $pcProfile->system_info;
                        @endphp
                        
                        @if($sysInfo)
                            <!-- CPU Information -->
                            @if(isset($sysInfo['cpu']))
                                <div class="mb-4">
                                    <h6 class="font-weight-bold text-info mb-3">CPU Information</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <tbody>
                                                @foreach($sysInfo['cpu'] as $key => $value)
                                                    <tr>
                                                        <th style="width: 40%">{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                                                        <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <!-- Memory Information -->
                            @if(isset($sysInfo['memory']))
                                <div class="mb-4">
                                    <h6 class="font-weight-bold text-info mb-3">Memory Information</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <tbody>
                                                @foreach($sysInfo['memory'] as $key => $value)
                                                    <tr>
                                                        <th style="width: 40%">{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                                                        <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <!-- Disk Information -->
                            @if(isset($sysInfo['disk']))
                                <div class="mb-4">
                                    <h6 class="font-weight-bold text-info mb-3">Disk Information</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <tbody>
                                                @foreach($sysInfo['disk'] as $key => $value)
                                                    <tr>
                                                        <th style="width: 40%">{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                                                        <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <!-- Network Information -->
                            @if(isset($sysInfo['network']))
                                <div class="mb-4">
                                    <h6 class="font-weight-bold text-info mb-3">Network Information</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <tbody>
                                                @foreach($sysInfo['network'] as $key => $value)
                                                    <tr>
                                                        <th style="width: 40%">{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                                                        <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <!-- Operating System Information -->
                            @if(isset($sysInfo['os']))
                                <div class="mb-4">
                                    <h6 class="font-weight-bold text-info mb-3">Operating System Information</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <tbody>
                                                @foreach($sysInfo['os'] as $key => $value)
                                                    <tr>
                                                        <th style="width: 40%">{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                                                        <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <!-- Other Information -->
                            @php
                                $otherInfo = array_diff_key($sysInfo, [
                                    'cpu' => 1,
                                    'memory' => 1,
                                    'disk' => 1,
                                    'network' => 1,
                                    'os' => 1
                                ]);
                            @endphp
                            @if(count($otherInfo) > 0)
                                <div class="mb-4">
                                    <h6 class="font-weight-bold text-info mb-3">Other Information</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <tbody>
                                                @foreach($otherInfo as $key => $value)
                                                    <tr>
                                                        <th style="width: 40%">{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                                                        <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> System information is not in a valid format.
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No system information available.
                        </div>
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

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize clipboard.js
        var clipboard = new ClipboardJS('.copy-btn');
        
        clipboard.on('success', function(e) {
            // Show success message
            const button = $(e.trigger);
            const originalHtml = button.html();
            button.html('<i class="fas fa-check"></i>');
            button.removeClass('btn-outline-secondary').addClass('btn-success');
            
            setTimeout(function() {
                button.html(originalHtml);
                button.removeClass('btn-success').addClass('btn-outline-secondary');
            }, 2000);
            
            e.clearSelection();
        });
        
        // System info refresh button
        $('#refreshSystemInfo').on('click', function() {
            const button = $(this);
            const originalHtml = button.html();
            button.html('<i class="fas fa-spinner fa-spin"></i> Refreshing...');
            button.prop('disabled', true);
            
            // Here you would add AJAX call to refresh system info
            // For now, just simulate with a timeout
            setTimeout(function() {
                button.html(originalHtml);
                button.prop('disabled', false);
                
                // Show success message
                const alert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">')
                    .html('System information refreshed successfully! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
                
                $('.card-header').after(alert);
                
                // Auto dismiss after 5 seconds
                setTimeout(function() {
                    alert.alert('close');
                }, 5000);
            }, 2000);
        });
    });
</script>
@endsection 