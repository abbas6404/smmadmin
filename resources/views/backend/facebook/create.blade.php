@extends('backend.layouts.master')

@section('title', 'Create Facebook Account')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Facebook Account</h1>
        <a href="{{ route('admin.facebook.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.facebook.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <!-- PC Profile Selection -->
                    <div class="col-md-6 mb-3">
                        <label for="pc_profile_id" class="form-label">PC Profile</label>
                        <select name="pc_profile_id" id="pc_profile_id" class="form-control @error('pc_profile_id') is-invalid @enderror">
                            <option value="">All PCs</option>
                            @foreach($pcProfiles as $profile)
                                <option value="{{ $profile->id }}" {{ old('pc_profile_id') == $profile->id ? 'selected' : '' }}>
                                    {{ $profile->pc_name }} ({{ $profile->hardware_id }})
                                </option>
                            @endforeach
                        </select>
                        @error('pc_profile_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Select 'All PCs' to make this account available to all PCs, or select a specific PC to restrict it.</div>
                    </div>

                    <!-- Batch Selection Type -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Batch Selection</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="batch_type" id="existing_batch" value="existing" checked>
                            <label class="form-check-label" for="existing_batch">
                                Select Existing Batch
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="batch_type" id="new_batch" value="new">
                            <label class="form-check-label" for="new_batch">
                                Create New Batch
                            </label>
                        </div>
                    </div>

                    <!-- Existing Batch Selection -->
                    <div class="col-md-6 mb-3" id="existing_batch_section">
                        <label for="submission_batch_id" class="form-label">Select Existing Batch</label>
                        <select name="submission_batch_id" id="submission_batch_id" class="form-control @error('submission_batch_id') is-invalid @enderror">
                            <option value="">Select Batch (Optional)</option>
                            @foreach($submissionBatches as $batch)
                                <option value="{{ $batch->id }}" {{ old('submission_batch_id') == $batch->id ? 'selected' : '' }}>
                                    {{ $batch->name }} ({{ $batch->submission_type }})
                                </option>
                            @endforeach
                        </select>
                        @error('submission_batch_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Select an existing submission batch to associate these accounts with.</div>
                    </div>

                    <!-- New Batch Creation -->
                    <div class="col-md-6 mb-3" id="new_batch_section" style="display: none;">
                        <label for="new_batch_name" class="form-label">New Batch Name</label>
                        <div class="input-group">
                            <input type="text" name="new_batch_name" id="new_batch_name" class="form-control @error('new_batch_name') is-invalid @enderror" placeholder="Enter batch name" value="{{ old('new_batch_name') }}">
                            <button class="btn btn-outline-secondary" type="button" id="generate_batch_name">
                                <i class="fas fa-magic"></i> Generate
                            </button>
                        </div>
                        @error('new_batch_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Enter a name for the new submission batch or click Generate to create one automatically.</div>
                    </div>

                    <!-- Email and Password Input -->
                    <div class="col-md-12 mb-3">
                        <label for="accounts" class="form-label required">Email|Password Combinations</label>
                        <textarea name="accounts" id="accounts" class="form-control @error('accounts') is-invalid @enderror" 
                                rows="10" required placeholder="Enter one identifier|password combination per line, example:&#10;user1@gmail.com|password1&#10;692394938783|password2">{{ old('accounts') }}</textarea>
                        @error('accounts')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Enter each account on a new line in the format: identifier|password<br>
                            The identifier can be an email address or a user ID.<br>
                            Examples:<br>
                            user1@gmail.com|password123<br>
                            692394938783|password456
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Accounts
                    </button>
                    <a href="{{ route('admin.facebook.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const existingBatchRadio = document.getElementById('existing_batch');
    const newBatchRadio = document.getElementById('new_batch');
    const existingBatchSection = document.getElementById('existing_batch_section');
    const newBatchSection = document.getElementById('new_batch_section');
    const submissionBatchSelect = document.getElementById('submission_batch_id');
    const newBatchNameInput = document.getElementById('new_batch_name');
    const generateBatchNameBtn = document.getElementById('generate_batch_name');

    function toggleBatchSections() {
        if (existingBatchRadio.checked) {
            existingBatchSection.style.display = 'block';
            newBatchSection.style.display = 'none';
            submissionBatchSelect.disabled = false;
            newBatchNameInput.disabled = true;
        } else {
            existingBatchSection.style.display = 'none';
            newBatchSection.style.display = 'block';
            submissionBatchSelect.disabled = true;
            newBatchNameInput.disabled = false;
        }
    }

    function generateBatchName() {
        // Get current date in YYYY_MM_DD format
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const dateStr = `${year}_${month}_${day}`;
        
        // Use the next batch ID from the controller
        const batchId = {{ $nextBatchId }};
        
        // Format: Batch_facebook_batchId_year_month_day
        const batchName = `Batch_facebook_${batchId}_${dateStr}`;
        newBatchNameInput.value = batchName;
    }

    existingBatchRadio.addEventListener('change', toggleBatchSections);
    newBatchRadio.addEventListener('change', toggleBatchSections);
    generateBatchNameBtn.addEventListener('click', generateBatchName);

    // Initial state
    toggleBatchSections();
});
</script>
@endpush
@endsection 