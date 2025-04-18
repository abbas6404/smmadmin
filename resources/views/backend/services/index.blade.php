@extends('backend.layouts.master')

@section('title', 'Services')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Services</h1>
        <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add New Service
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-table me-1"></i>
                    Services List
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Min-Max</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr>
                                <td><code>{{ $service->id }}</code></td>
                                <td>{{ $service->name }}</td>
                                <td>{{ $service->category }}</td>
                                <td>${{ number_format($service->price, 4) }}</td>
                                <td>{{ number_format($service->min_quantity) }} - {{ number_format($service->max_quantity) }}</td>
                                <td>
                                    <span class="badge bg-{{ $service->status === 'active' ? 'success' : 'danger' }}">
                                        {{ ucfirst($service->status) }}
                                    </span>
                                </td>
                                <td>{{ $service->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.services.show', $service) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.services.edit', $service) }}" 
                                           class="btn btn-sm btn-primary" 
                                           title="Edit Service">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="deleteService({{ $service->id }})"
                                                title="Delete Service">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No services found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing {{ $services->firstItem() ?? 0 }} to {{ $services->lastItem() ?? 0 }} of {{ $services->total() }} services
                    </div>
                    <div>
                        {{ $services->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
function deleteService(serviceId) {
    if (confirm('Are you sure you want to delete this service?')) {
        const form = document.getElementById('deleteForm');
        form.action = `/admin/services/${serviceId}`;
        form.submit();
    }
}
</script>
@endsection 