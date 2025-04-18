@extends('backend.layouts.master')

@section('title', 'Users')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Users</h6>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                + Add New User
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered" id="usersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>${{ number_format($user->balance, 2) }}</td>
                                <td>
                                    @if($user->status)
                                        @if($user->status === 'active')
                                            <span style="color: #0d6efd !important; font-weight: 600; font-size: 13px;">Active</span>
                                        @else
                                            <span style="color: #dc3545 !important; font-weight: 600; font-size: 13px;">Inactive</span>
                                        @endif
                                    @else
                                        <span style="color: #dc3545 !important; font-weight: 600; font-size: 13px;">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group">
                                        @if($user->deleted_at)
                                            <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" title="Restore">
                                                    <i class="fas fa-trash-restore"></i>
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info btn-sm" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this user?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .btn-group {
        gap: 5px;
    }
    .table td {
        vertical-align: middle;
    }
</style>
<link href="{{ asset('backend/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            "order": [[5, "desc"]],
            "pageLength": 25,
            "columnDefs": [
                { "orderable": false, "targets": 6 }
            ]
        });
    });
</script>
@endpush
@endsection 