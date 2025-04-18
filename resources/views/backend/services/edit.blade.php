@extends('backend.layouts.master')

@section('title', 'Edit Service')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Edit Service</h1>
        <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.services.update', $service) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Service Name</label>
                    <input type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $service->name) }}" 
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="category" class="form-label">Category</label>
                    <input type="text" 
                           class="form-control @error('category') is-invalid @enderror" 
                           id="category" 
                           name="category" 
                           value="{{ old('category', $service->category) }}" 
                           required>
                    @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="price" class="form-label">Price (per item)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" 
                                   class="form-control @error('price') is-invalid @enderror" 
                                   id="price" 
                                   name="price" 
                                   value="{{ old('price', $service->price) }}" 
                                   step="0.0001" 
                                   min="0" 
                                   required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="min_quantity" class="form-label">Minimum Quantity</label>
                        <input type="number" 
                               class="form-control @error('min_quantity') is-invalid @enderror" 
                               id="min_quantity" 
                               name="min_quantity" 
                               value="{{ old('min_quantity', $service->min_quantity) }}" 
                               min="1" 
                               required>
                        @error('min_quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="max_quantity" class="form-label">Maximum Quantity</label>
                        <input type="number" 
                               class="form-control @error('max_quantity') is-invalid @enderror" 
                               id="max_quantity" 
                               name="max_quantity" 
                               value="{{ old('max_quantity', $service->max_quantity) }}" 
                               min="1" 
                               required>
                        @error('max_quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" 
                              name="description" 
                              rows="3" 
                              required>{{ old('description', $service->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select @error('status') is-invalid @enderror" 
                            id="status" 
                            name="status" 
                            required>
                        <option value="">Select Status</option>
                        <option value="active" {{ old('status', $service->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $service->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Service
                    </button>
                    <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validate max quantity is greater than min quantity
    const minQuantityInput = document.getElementById('min_quantity');
    const maxQuantityInput = document.getElementById('max_quantity');

    function validateQuantities() {
        const min = parseInt(minQuantityInput.value);
        const max = parseInt(maxQuantityInput.value);
        
        if (min && max && max <= min) {
            maxQuantityInput.setCustomValidity('Maximum quantity must be greater than minimum quantity');
        } else {
            maxQuantityInput.setCustomValidity('');
        }
    }

    minQuantityInput.addEventListener('change', validateQuantities);
    maxQuantityInput.addEventListener('change', validateQuantities);
});
</script>
@endsection 