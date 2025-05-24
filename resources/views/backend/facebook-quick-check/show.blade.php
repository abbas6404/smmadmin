@extends('backend.layouts.master')

@section('title', 'View Facebook Quick Check Account')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">View Facebook Quick Check Account</h1>
        <div>
            <a href="{{ route('admin.facebook-quick-check.edit', $account->id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit Account
            </a>
            <a href="{{ route('admin.facebook-quick-check.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Account Information Card -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Account Information</h6>
                    @if(!$account->trashed())
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <div class="dropdown-menu">
                                <form action="{{ route('admin.facebook-quick-check.toggle-valid', $account->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-{{ $account->status == 'active' ? 'times' : 'check' }} fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Mark as {{ $account->status == 'active' ? 'Pending' : 'Active' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.facebook-quick-check.quick-check', $account->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sync fa-sm fa-fw mr-2 text-gray-400"></i> Quick Check
                                    </button>
                                </form>
                                @if($account->status == 'active')
                                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#transferModal">
                                    <i class="fas fa-exchange-alt fa-sm fa-fw mr-2 text-gray-400"></i> Transfer to FB Account
                                </button>
                                @endif
                                <div class="dropdown-divider"></div>
                                <form action="{{ route('admin.facebook-quick-check.destroy', $account->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to move this account to trash?')">
                                        <i class="fas fa-trash fa-sm fa-fw"></i> Trash
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="dropdown">
                            <button class="btn btn-sm btn-danger dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Trash Actions
                            </button>
                            <div class="dropdown-menu">
                                <form action="{{ route('admin.facebook-quick-check.restore', $account->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-success">
                                        <i class="fas fa-trash-restore fa-sm fa-fw"></i> Restore
                                    </button>
                                </form>
                                <form action="{{ route('admin.facebook-quick-check.force-delete', $account->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to permanently delete this account? This action cannot be undone.')">
                                        <i class="fas fa-trash-alt fa-sm fa-fw"></i> Delete Permanently
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th width="200">ID</th>
                                    <td>{{ $account->id }}</td>
                                </tr>
                                <tr>
                                    <th>Email/Phone</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            {{ $account->email }}
                                            <button class="btn btn-sm btn-outline-secondary ms-2 copy-btn" 
                                                    data-clipboard-text="{{ $account->email }}">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Password</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="password-field position-relative">
                                                <input type="password" class="form-control-plaintext password-input" 
                                                       readonly value="{{ $account->password }}">
                                                <button type="button" class="btn btn-sm btn-link toggle-password position-absolute" 
                                                        style="right: 0; top: 0;">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            <button class="btn btn-sm btn-outline-secondary ms-2 copy-btn" 
                                                    data-clipboard-text="{{ $account->password }}">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @if($account->two_factor_secret)
                                <tr>
                                    <th>Two-Factor Secret</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="password-field position-relative">
                                                <input type="password" class="form-control-plaintext password-input" 
                                                       readonly value="{{ $account->two_factor_secret }}">
                                                <button type="button" class="btn btn-sm btn-link toggle-password position-absolute" 
                                                        style="right: 0; top: 0;">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            <button class="btn btn-sm btn-outline-secondary ms-2 copy-btn" 
                                                    data-clipboard-text="{{ $account->two_factor_secret }}">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($account->status == 'active')
                                            <span class="badge bg-success">Active</span>
                                        @elseif($account->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($account->status == 'processing')
                                            <span class="badge bg-info">Processing</span>
                                        @elseif($account->status == 'in_use')
                                            <span class="badge bg-secondary">In Use</span>
                                        @elseif($account->status == 'blocked')
                                            <span class="badge bg-danger">Blocked</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Check Count</th>
                                    <td>{{ $account->check_count }}</td>
                                </tr>
                                <tr>
                                    <th>Last Checked</th>
                                    <td>{{ $account->last_checked_at ? $account->last_checked_at->format('Y-m-d H:i:s') : 'Never' }}</td>
                                </tr>
                                <tr>
                                    <th>Checked By</th>
                                    <td>{{ $account->checked_by ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Check Result</th>
                                    <td>{{ $account->check_result ?? 'No result yet' }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $account->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $account->updated_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                @if($account->deleted_at)
                                <tr>
                                    <th>Deleted At</th>
                                    <td>{{ $account->deleted_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information Card -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Additional Information</h6>
                </div>
                <div class="card-body">
                    <h5 class="font-weight-bold">Notes</h5>
                    <div class="p-3 bg-light mb-4 rounded">
                        {!! nl2br(e($account->notes ?? 'No notes available')) !!}
                    </div>

                    @if($account->account_cookies)
                    <h5 class="font-weight-bold">Account Cookies</h5>
                    <div class="p-3 bg-light mb-4 rounded">
                        <pre class="mb-0" style="max-height: 200px; overflow-y: auto; white-space: pre-wrap;"><code>{{ json_encode($account->account_cookies, JSON_PRETTY_PRINT) }}</code></pre>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.11/dist/clipboard.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize clipboard.js
    var clipboard = new ClipboardJS('.copy-btn');
    
    clipboard.on('success', function(e) {
        // Change button text temporarily
        var originalHTML = e.trigger.innerHTML;
        e.trigger.innerHTML = '<i class="fas fa-check"></i>';
        
        setTimeout(function() {
            e.trigger.innerHTML = originalHTML;
        }, 1000);
        
        e.clearSelection();
    });

    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(function(button) {
        button.addEventListener('click', function() {
            var input = this.parentNode.querySelector('.password-input');
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

<!-- Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferModalLabel">Transfer Account to Facebook</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.facebook-quick-check.transfer', $account->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="pc_profile_id" class="form-label required">Select PC</label>
                        <select class="form-select" id="pc_profile_id" name="pc_profile_id" required>
                            <option value="">-- Select PC --</option>
                            @foreach($pcProfiles ?? [] as $pc)
                            <option value="{{ $pc->id }}">{{ $pc->pc_name }} ({{ $pc->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This account will be transferred to the selected PC and marked as "in_use". A new account will be created with "pending" status in the Facebook accounts system.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-exchange-alt me-1"></i> Transfer Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 