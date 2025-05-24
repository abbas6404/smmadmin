@extends('backend.layouts.master')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Disk Management</h1>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <!-- Total Disks -->
        <div class="col-xl-2 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Disks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hdd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Minimum Free Space -->
        <div class="col-xl-2 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Minimum Free Space</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    function formatBytes($bytes) {
                                        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                                        $bytes = max($bytes, 0);
                                        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
                                        $pow = min($pow, count($units) - 1);
                                        $bytes /= pow(1024, $pow);
                                        return round($bytes, 2) . ' ' . $units[$pow];
                                    }
                                @endphp
                                {{ $stats['min_free_space'] ? formatBytes($stats['min_free_space']) : 'N/A' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-database fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Free Space -->
        <div class="col-xl-2 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Free Space</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_free_space'] ? formatBytes($stats['total_free_space']) : 'N/A' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-server fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Healthy Disks -->
        <div class="col-xl-2 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Healthy Disks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['healthy'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Warning Disks -->
        <div class="col-xl-2 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Warning Disks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['warning'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Disk List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Disk List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>PC Profile</th>
                            <th>Drive Letter</th>
                            <th>File System</th>
                            <th>Total Size</th>
                            <th>Free Space</th>
                            <th>Health</th>
                            <th>Last Checked</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($disks as $disk)
                            <tr>
                                <td>{{ $disk->pcProfile->pc_name }}</td>
                                <td>{{ $disk->drive_letter }}:</td>
                                <td>{{ $disk->file_system }}</td>
                                <td>{{ $disk->formatted_total_size }}</td>
                                <td>{{ $disk->formatted_free_space }}</td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar {{ $disk->health_percentage >= 90 ? 'bg-success' : ($disk->health_percentage >= 70 ? 'bg-warning' : 'bg-danger') }}" 
                                             role="progressbar" 
                                             style="width: {{ $disk->health_percentage }}%" 
                                             aria-valuenow="{{ $disk->health_percentage }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ $disk->health_percentage }}%
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $disk->last_checked_at ? $disk->last_checked_at->diffForHumans() : 'Never' }}</td>
                                <td>
                                    <a href="{{ route('admin.disks.show', $disk) }}" class="btn btn-info btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $disks->firstItem() ?? 0 }} to {{ $disks->lastItem() ?? 0 }} of {{ $disks->total() }} results
                </div>
                <div>
                    @if ($disks->hasPages())
                        <nav>
                            <ul class="pagination mb-0">
                                {{-- Previous Page Link --}}
                                @if ($disks->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link" aria-hidden="true">&laquo; Previous</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $disks->previousPageUrl() }}" rel="prev">&laquo; Previous</a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($disks->getUrlRange(1, $disks->lastPage()) as $page => $url)
                                    @if ($page == $disks->currentPage())
                                        <li class="page-item active">
                                            <span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($disks->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $disks->nextPageUrl() }}" rel="next">Next &raquo;</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link" aria-hidden="true">Next &raquo;</span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .progress {
        height: 20px;
        background-color: #f8f9fc;
    }
    .progress-bar {
        line-height: 20px;
        font-size: 12px;
    }
    .pagination {
        margin: 0;
        display: flex;
        padding-left: 0;
        list-style: none;
        border-radius: 0.35rem;
    }
    .page-link {
        position: relative;
        display: block;
        padding: 0.5rem 0.75rem;
        margin-left: -1px;
        line-height: 1.25;
        color: #4e73df;
        background-color: #fff;
        border: 1px solid #dddfeb;
    }
    .page-item:first-child .page-link {
        margin-left: 0;
        border-top-left-radius: 0.35rem;
        border-bottom-left-radius: 0.35rem;
    }
    .page-item:last-child .page-link {
        border-top-right-radius: 0.35rem;
        border-bottom-right-radius: 0.35rem;
    }
    .page-item.active .page-link {
        z-index: 3;
        color: #fff;
        background-color: #4e73df;
        border-color: #4e73df;
    }
    .page-item.disabled .page-link {
        color: #858796;
        pointer-events: none;
        cursor: auto;
        background-color: #fff;
        border-color: #dddfeb;
    }
    .page-link:hover {
        z-index: 2;
        color: #224abe;
        text-decoration: none;
        background-color: #eaecf4;
        border-color: #dddfeb;
    }
    .page-link:focus {
        z-index: 3;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
</style>
@endpush 