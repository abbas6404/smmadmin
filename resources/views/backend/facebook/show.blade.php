@extends('backend.layouts.master')

@section('title', 'Facebook Account Details')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Facebook Account Details</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Account #{{ $facebook->id }} - {{ $facebook->email }}</h6>
            <div>
                <a href="{{ route('admin.facebook.edit', $facebook->id) }}" class="btn btn-warning btn-sm me-1">Edit Account</a>
                <a href="{{ route('admin.facebook.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th style="width: 20%;">ID</th>
                        <td>{{ $facebook->id }}</td>
                    </tr>
                    <tr>
                        <th>PC Profile</th>
                        {{-- PC Profile link cannot be added yet as admin routes/controller/view for PC Profiles don't exist --}}
                        <td>{{ $facebook->pcProfile->pc_name ?? 'N/A' }} (ID: {{ $facebook->pc_profile_id }})</td>
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
                        <td>{{ $facebook->email }}</td>
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
                                ($facebook->status == 'remove' ? 'danger' : 'dark'))))
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
                        <th>Account Cookies</th>
                        <td>
                            @if($facebook->account_cookies)
                                <pre style="max-height: 150px; overflow-y: auto; background-color: #f8f9fa; padding: 10px; border-radius: 4px;"><code>{{ json_encode($facebook->account_cookies, JSON_PRETTY_PRINT) }}</code></pre>
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </td>
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
@endsection

@push('scripts')
<script>
// Initialize Bootstrap Popovers
var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
  return new bootstrap.Popover(popoverTriggerEl, {
    // Optional: Custom configuration here
    sanitize: false // Allow the <code> tag from data-bs-content
  });
});
</script>
@endpush 