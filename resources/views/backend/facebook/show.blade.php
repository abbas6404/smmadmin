@extends('backend.layouts.master')

@section('title', 'Facebook Account Details')

@section('content')
<div class="container-fluid">
    <!-- Page Heading with Status Badge -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                Facebook Account: {{ $facebook->email }}
                <span class="badge bg-{{
                    $facebook->status == 'active' ? 'success' :
                    ($facebook->status == 'pending' ? 'warning' :
                    ($facebook->status == 'inactive' ? 'secondary' :
                    ($facebook->status == 'processing' ? 'info' :
                    ($facebook->status == 'logout' ? 'dark' :
                    ($facebook->status == 'remove' ? 'danger' : 'dark')))))
                }}">
                    {{ ucfirst($facebook->status) }}
                </span>
                @if($facebook->trashed())
                <span class="badge bg-danger">Deleted</span>
                @endif
            </h1>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.facebook.edit', $facebook->id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Edit Account
            </a>
            @if($facebook->trashed())
                <form action="{{ route('admin.facebook.restore', $facebook->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to restore this Facebook account?')">
                        <i class="fas fa-undo"></i> Restore Account
                    </button>
                </form>
            @else
                <form action="{{ route('admin.facebook.destroy', $facebook->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this Facebook account?')">
                        <i class="fas fa-trash"></i> Delete Account
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.facebook.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- PC Profile Account Status Summary -->
    @if($facebook->pcProfile)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 bg-gradient-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-chart-bar"></i> PC Profile Account Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-primary h-100 py-2">
                                <div class="card-body py-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">PC Profile</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <a href="{{ route('admin.pc-profiles.show', $facebook->pc_profile_id) }}" class="text-decoration-none">
                                                    {{ $facebook->pcProfile->pc_name }}
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-desktop fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-success h-100 py-2">
                                <div class="card-body py-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Facebook Accounts</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $facebook->pcProfile->facebookAccounts()->count() }}
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
                            <div class="card border-left-info h-100 py-2">
                                <div class="card-body py-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Chrome Profile ID</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                @if($facebook->chrome_profile_id)
                                                    <a href="{{ route('admin.chrome.show', $facebook->chrome_profile_id) }}" class="text-decoration-none">
                                                        #{{ $facebook->chrome_profile_id }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
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

                    <h5 class="border-bottom pb-2 mt-3">Facebook Accounts by Status</h5>
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <div class="card border-left-success h-100 py-2">
                                <div class="card-body py-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $facebook->pcProfile->facebookAccounts()->where('status', 'active')->count() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="card border-left-secondary h-100 py-2">
                                <div class="card-body py-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Inactive</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $facebook->pcProfile->facebookAccounts()->where('status', 'inactive')->count() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-pause-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="card border-left-warning h-100 py-2">
                                <div class="card-body py-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $facebook->pcProfile->facebookAccounts()->where('status', 'pending')->count() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="card border-left-info h-100 py-2">
                                <div class="card-body py-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Processing</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $facebook->pcProfile->facebookAccounts()->where('status', 'processing')->count() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-sync fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="card border-left-danger h-100 py-2">
                                <div class="card-body py-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Removed</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $facebook->pcProfile->facebookAccounts()->where('status', 'remove')->count() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-trash-alt fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="card border-left-dark h-100 py-2">
                                <div class="card-body py-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Logout</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $facebook->pcProfile->facebookAccounts()->where('status', 'logout')->count() }}
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
    @endif

    <!-- Account Information Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-info-circle"></i> Account Information
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                <tbody>
                    <tr>
                        <th style="width: 20%;">ID</th>
                        <td>{{ $facebook->id }}</td>
                    </tr>
                    <tr>
                        <th>PC Profile</th>
                            <td>
                                @if($facebook->pcProfile)
                                    <a href="{{ route('admin.pc-profiles.show', $facebook->pc_profile_id) }}" class="text-decoration-none">
                                        {{ $facebook->pcProfile->pc_name }} (ID: {{ $facebook->pc_profile_id }})
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                    </tr>
                    <tr>
                        <th>Chrome Profile ID</th>
                        <td>
                            {{ $facebook->chrome_profile_id ?? 'N/A' }}
                            @if($facebook->chrome_profile_id)
                                <a href="{{ route('admin.chrome.show', $facebook->chrome_profile_id) }}" title="View Chrome Profile #{{ $facebook->chrome_profile_id }}" class="ms-1">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Submission Batch</th>
                        <td>
                            @if($facebook->submissionBatch)
                                <a href="{{ route('admin.submission-batch.show', $facebook->submission_batch_id) }}" title="View Batch">
                                    {{ $facebook->submissionBatch->name }}
                                </a>
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Email</th>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span id="email-value">{{ $facebook->email }}</span>
                                    <button class="btn btn-sm btn-outline-secondary ml-2 copy-btn" data-clipboard-target="#email-value">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </td>
                    </tr>
                    <tr>
                        <th>Password</th>
                        <td>
                            <span class="text-muted">(Hashed)</span> 
                            <a href="javascript:void(0);"
                               class="ms-1" 
                               data-bs-toggle="popover" 
                               data-bs-trigger="click" 
                               data-bs-placement="right" 
                               data-bs-title="Password Hash" 
                               data-bs-content="<small><code>{{ $facebook->password }}</code><br>(This is NOT the original password)</small>" 
                               data-bs-html="true"
                               title="Click to view password hash (NOT original password)">
                               <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-{{
                                $facebook->status == 'active' ? 'success' :
                                ($facebook->status == 'pending' ? 'warning' :
                                ($facebook->status == 'inactive' ? 'secondary' :
                                ($facebook->status == 'processing' ? 'info' :
                                    ($facebook->status == 'logout' ? 'dark' :
                                    ($facebook->status == 'remove' ? 'danger' : 'dark')))))
                            }}">
                                {{ ucfirst($facebook->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Have Use</th>
                        <td>{{ $facebook->have_use ? 'Yes' : 'No' }}</td>
                    </tr>
                    <tr>
                        <th>Have Page</th>
                        <td>{{ $facebook->have_page ? 'Yes' : 'No' }}</td>
                    </tr>
                    <tr>
                        <th>Have Post</th>
                        <td>{{ $facebook->have_post ? 'Yes' : 'No' }}</td>
                    </tr>
                    <tr>
                        <th>Total Count</th>
                        <td>{{ number_format($facebook->total_count) }}</td>
                    </tr>
                    <tr>
                        <th>Language</th>
                        <td>{{ strtoupper($facebook->lang) }}</td>
                    </tr>
                    <tr>
                        <th>Order Link UIDs</th>
                        <td>
                            @php
                                $hasUids = false;
                                $popoverContent = 'None';
                                $uidList = [];
                                $maxPreview = 15; // Max UIDs to show in popover preview

                                // Standardize UID list (handle array or JSON string)
                                if (is_array($facebook->order_link_uid)) {
                                    $uidList = $facebook->order_link_uid;
                                } elseif (is_string($facebook->order_link_uid)) {
                                    $decoded = json_decode($facebook->order_link_uid);
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                        $uidList = $decoded;
                                    }
                                }

                                $uidCount = count($uidList);

                                if ($uidCount > 0) {
                                    $hasUids = true;
                                    $previewUids = array_slice($uidList, 0, $maxPreview);
                                    $popoverContent = e(implode(', ', $previewUids));

                                    if ($uidCount > $maxPreview) {
                                        $remainingCount = $uidCount - $maxPreview;
                                        $popoverContent .= "<br><small>... and {$remainingCount} more UID(s).</small>";
                                    }
                                } else {
                                     $popoverContent = '<span class="text-muted">None</span>'; // Ensure None is muted
                                }

                            @endphp

                            @if ($hasUids)
                                <span class="text-muted me-1">[...] ({{ $uidCount }})</span> {{-- Indicate count --}}
                                <a href="javascript:void(0);"
                                   class="ms-1"
                                   data-bs-toggle="popover"
                                   data-bs-trigger="click"
                                   data-bs-placement="top"
                                   data-bs-title="Order Link UIDs (Preview)"
                                   data-bs-content="{{ $popoverContent }}" {{-- Use the generated content --}}
                                   data-bs-html="true"
                                   title="View UID Preview">
                                   <i class="fas fa-eye"></i>
                                </a>
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Note</th>
                        <td>{{ $facebook->note ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>{{ $facebook->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td>{{ $facebook->updated_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    @if($facebook->deleted_at)
                    <tr>
                        <th>Removed At</th>
                        <td>{{ $facebook->deleted_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <!-- Account Cookies -->
    @if($facebook->account_cookies)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Account Cookies</h6>
        </div>
        <div class="card-body">
            <pre style="max-height: 300px; overflow-y: auto; background-color: #f8f9fa; padding: 10px; border-radius: 4px;"><code>{{ json_encode($facebook->account_cookies, JSON_PRETTY_PRINT) }}</code></pre>
        </div>
    </div>
    @endif

    <!-- Account Usage Statistics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-gradient-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-chart-line"></i> Account Features
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Have Use -->
                        <div class="col-md-4 mb-4">
                            <div class="text-center">
                                <div class="mb-2">
                                    @if($facebook->have_use)
                                        <div class="circle-icon bg-success">
                                            <i class="fas fa-check text-white"></i>
                                        </div>
                                    @else
                                        <div class="circle-icon bg-danger">
                                            <i class="fas fa-times text-white"></i>
                                        </div>
                                    @endif
                                </div>
                                <h6 class="font-weight-bold">Have Use</h6>
                                <div class="text-xs text-muted">
                                    {{ $facebook->have_use ? 'Account is usable' : 'Account not usable' }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Have Page -->
                        <div class="col-md-4 mb-4">
                            <div class="text-center">
                                <div class="mb-2">
                                    @if($facebook->have_page)
                                        <div class="circle-icon bg-success">
                                            <i class="fas fa-check text-white"></i>
                                        </div>
                                    @else
                                        <div class="circle-icon bg-danger">
                                            <i class="fas fa-times text-white"></i>
                                        </div>
                                    @endif
                                </div>
                                <h6 class="font-weight-bold">Have Page</h6>
                                <div class="text-xs text-muted">
                                    {{ $facebook->have_page ? 'Has Facebook page' : 'No Facebook page' }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Have Post -->
                        <div class="col-md-4 mb-4">
                            <div class="text-center">
                                <div class="mb-2">
                                    @if($facebook->have_post)
                                        <div class="circle-icon bg-success">
                                            <i class="fas fa-check text-white"></i>
                                        </div>
                                    @else
                                        <div class="circle-icon bg-danger">
                                            <i class="fas fa-times text-white"></i>
                                        </div>
                                    @endif
                                </div>
                                <h6 class="font-weight-bold">Have Post</h6>
                                <div class="text-xs text-muted">
                                    {{ $facebook->have_post ? 'Has posts' : 'No posts' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-gradient-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-tachometer-alt"></i> Account Metrics
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Total Count -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold text-uppercase text-xs mb-1">Total Count</h6>
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1 mr-3" style="height: 20px;">
                                @php
                                    // Calculate percentage based on a reasonable maximum (e.g., 10000)
                                    $maxCount = 10000;
                                    $percentage = min(100, ($facebook->total_count / $maxCount) * 100);
                                    
                                    // Determine color based on count
                                    $colorClass = 'bg-danger';
                                    if ($facebook->total_count > 5000) {
                                        $colorClass = 'bg-success';
                                    } elseif ($facebook->total_count > 1000) {
                                        $colorClass = 'bg-info';
                                    } elseif ($facebook->total_count > 100) {
                                        $colorClass = 'bg-warning';
                                    }
                                @endphp
                                <div class="progress-bar {{ $colorClass }}" role="progressbar" 
                                     style="width: {{ $percentage }}%" 
                                     aria-valuenow="{{ $facebook->total_count }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="{{ $maxCount }}">
                                    {{ number_format($facebook->total_count) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Language -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold text-uppercase text-xs mb-1">Language</h6>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary">{{ strtoupper($facebook->lang) }}</span>
                        </div>
                    </div>
                    
                    <!-- Order Link UIDs -->
                    <div>
                        <h6 class="font-weight-bold text-uppercase text-xs mb-1">Order Link UIDs</h6>
                        <div class="d-flex align-items-center">
                            @php
                                $uidCount = 0;
                                if (is_array($facebook->order_link_uid)) {
                                    $uidCount = count($facebook->order_link_uid);
                                } elseif (is_string($facebook->order_link_uid)) {
                                    $decoded = json_decode($facebook->order_link_uid);
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                        $uidCount = count($decoded);
                                    }
                                }
                            @endphp
                            
                            @if($uidCount > 0)
                                <span class="badge bg-info">{{ $uidCount }} UIDs</span>
                            @else
                                <span class="badge bg-secondary">No UIDs</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.circle-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.circle-icon i {
    font-size: 24px;
}
</style>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
<script>
// Initialize Bootstrap Popovers
var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
  return new bootstrap.Popover(popoverTriggerEl, {
    sanitize: false // Allow the <code> tag from data-bs-content
  });
});

// Initialize clipboard.js
$(document).ready(function() {
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
});
</script>
@endsection 