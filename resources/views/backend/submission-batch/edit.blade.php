@extends('backend.layouts.master')

@section('title', 'Edit Submission Batch')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit Submission Batch</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Batch #{{ $submissionBatch->id }}</h6>
            <a href="{{ route('admin.submission-batch.show', $submissionBatch->id) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Details
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.submission-batch.update', $submissionBatch->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label required">Batch Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $submissionBatch->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Submission Type</label>
                        <input type="text" class="form-control" value="{{ ucfirst($submissionBatch->submission_type) }}" disabled>
                        <div class="form-text">Submission type cannot be changed after creation.</div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Total Submissions</label>
                        <input type="text" class="form-control" value="{{ number_format($submissionBatch->total_submissions) }}" disabled>
                        <div class="form-text">Total submissions are automatically calculated.</div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="accurate_submissions" class="form-label">Accurate Submissions</label>
                        <input type="number" class="form-control @error('accurate_submissions') is-invalid @enderror" 
                               id="accurate_submissions" name="accurate_submissions" 
                               value="{{ old('accurate_submissions', $submissionBatch->accurate_submissions) }}" min="0">
                        @error('accurate_submissions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Number of successfully verified submissions.</div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="incorrect_submissions" class="form-label">Incorrect Submissions</label>
                        <input type="number" class="form-control @error('incorrect_submissions') is-invalid @enderror" 
                               id="incorrect_submissions" name="incorrect_submissions" 
                               value="{{ old('incorrect_submissions', $submissionBatch->incorrect_submissions) }}" min="0">
                        @error('incorrect_submissions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Number of failed or incorrect submissions.</div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input @error('approved') is-invalid @enderror" 
                                   id="approved" name="approved" value="1" 
                                   {{ old('approved', $submissionBatch->approved) ? 'checked' : '' }}>
                            <label class="form-check-label" for="approved">Approved</label>
                            @error('approved')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3">{{ old('notes', $submissionBatch->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Optional notes about this submission batch.</div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Batch
                    </button>
                    <a href="{{ route('admin.submission-batch.show', $submissionBatch->id) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 