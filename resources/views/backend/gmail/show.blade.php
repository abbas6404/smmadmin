@extends('backend.layouts.master')

@section('title', 'Gmail Account Details') {{-- Title --}}

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Gmail Account Details</h1> {{-- Heading --}}

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Account #{{ $gmail->id }} - {{ $gmail->email }}</h6> {{-- Variable name --}}
            <div>
                <a href="{{ route('admin.gmail.edit', $gmail->id) }}" class="btn btn-warning btn-sm me-1">Edit Account</a> {{-- Route/Variable --}}
                <a href="{{ route('admin.gmail.index') }}" class="btn btn-secondary btn-sm">Back to List</a> {{-- Route --}}
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th style="width: 20%;">ID</th>
                        <td>{{ $gmail->id }}</td> {{-- Variable --}}
                    </tr>
                    <tr>
                        <th>PC Profile</th>
                        <td>{{ $gmail->pcProfile->pc_name ?? 'N/A' }} (ID: {{ $gmail->pc_profile_id }})</td> {{-- Variable --}}
                    </tr>
                    <tr>
                        <th>Chrome Profile ID</th>
                        <td>
                            {{ $gmail->chrome_profile_id ?? 'N/A' }}
                            @if($gmail->chrome_profile_id)
                                <a href="{{ route('admin.chrome.show', $gmail->chrome_profile_id) }}" title="View Chrome Profile #{{ $gmail->chrome_profile_id }}" class="ms-1">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Submission Batch</th>
                        <td>
                            @if($gmail->submissionBatch)
                                <a href="{{ route('admin.submission-batch.show', $gmail->submission_batch_id) }}" title="View Batch">
                                    {{ $gmail->submissionBatch->name }}
                                </a>
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $gmail->email }}</td> {{-- Variable --}}
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
                               data-bs-content="<small><code>{{ $gmail->password }}</code><br>(This is NOT the original password)</small>" 
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
                                $gmail->status == 'active' ? 'success' :
                                ($gmail->status == 'pending' ? 'warning' :
                                ($gmail->status == 'inactive' ? 'secondary' :
                                ($gmail->status == 'processing' ? 'info' :
                                ($gmail->status == 'remove' ? 'danger' : 'dark'))))
                            }}">
                                {{ ucfirst($gmail->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Have Use</th> {{-- Keep if applicable --}}
                        <td>{{ $gmail->have_use ? 'Yes' : 'No' }}</td> {{-- Variable --}}
                    </tr>
                    {{-- Remove Have Page/Post if not applicable for Gmail --}}
                    <tr>
                        <th>Total Count</th>
                        <td>{{ number_format($gmail->total_count) }}</td> {{-- Variable --}}
                    </tr>
                     {{-- Remove Language if not applicable --}}
                    <tr>
                        <th>Order Link UIDs</th>
                        <td>
                            @php
                                $hasUids = false;
                                $popoverContent = 'None';
                                $uidList = [];
                                $maxPreview = 15;

                                if (is_array($gmail->order_link_uid)) {
                                    $uidList = $gmail->order_link_uid;
                                } elseif (is_string($gmail->order_link_uid)) {
                                    $decoded = json_decode($gmail->order_link_uid);
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
                                     $popoverContent = '<span class="text-muted">None</span>';
                                }
                            @endphp
                            @if ($hasUids)
                                <span class="text-muted me-1">[...] ({{ $uidCount }})</span>
                                <a href="javascript:void(0);"
                                   class="ms-1"
                                   data-bs-toggle="popover"
                                   data-bs-trigger="click"
                                   data-bs-placement="top"
                                   data-bs-title="Order Link UIDs (Preview)"
                                   data-bs-content="{{ $popoverContent }}"
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
                        <td>{{ $gmail->note ?? '-' }}</td> {{-- Variable --}}
                    </tr>
                    <tr>
                        <th>Account Cookies</th>
                        <td>
                            @if($gmail->account_cookies)
                                <pre style="max-height: 150px; overflow-y: auto; background-color: #f8f9fa; padding: 10px; border-radius: 4px;"><code>{{ json_encode($gmail->account_cookies, JSON_PRETTY_PRINT) }}</code></pre>
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </td>
                    </tr>
                     {{-- Remove Account Cookies if not applicable --}}
                    <tr>
                        <th>Created At</th>
                        <td>{{ $gmail->created_at->format('Y-m-d H:i:s') }}</td> {{-- Variable --}}
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td>{{ $gmail->updated_at->format('Y-m-d H:i:s') }}</td> {{-- Variable --}}
                    </tr>
                    @if($gmail->deleted_at) {{-- Variable --}}
                    <tr>
                        <th>Removed At</th>
                        <td>{{ $gmail->deleted_at->format('Y-m-d H:i:s') }}</td> {{-- Variable --}}
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
// Initialize Bootstrap Popovers (already should be initialized by layout or other views)
var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
  return new bootstrap.Popover(popoverTriggerEl, {
    sanitize: false 
  });
});
</script>
@endpush 