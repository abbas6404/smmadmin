@extends('backend.layouts.master')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Disk Details</h1>
        <div>
            
            <a href="{{ route('admin.disks.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Disk Information</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">PC Profile</th>
                            <td>{{ $disk->pcProfile->pc_name }}</td>
                        </tr>
                        <tr>
                            <th>Drive Letter</th>
                            <td>{{ $disk->drive_letter }}:</td>
                        </tr>
                        <tr>
                            <th>File System</th>
                            <td>{{ $disk->file_system }}</td>
                        </tr>
                        <tr>
                            <th>Health Status</th>
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
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">Total Size</th>
                            <td>{{ $disk->formatted_total_size }}</td>
                        </tr>
                        <tr>
                            <th>Free Space</th>
                            <td>{{ $disk->formatted_free_space }}</td>
                        </tr>
                        <tr>
                            <th>Used Space</th>
                            <td>{{ $disk->formatted_used_space }}</td>
                        </tr>
                        <tr>
                            <th>Last Checked</th>
                            <td>{{ $disk->last_checked_at ? $disk->last_checked_at->format('Y-m-d H:i:s') : 'Never' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">Read Speed</th>
                            <td>{{ $disk->read_speed }} MB/s</td>
                        </tr>
                        <tr>
                            <th>Write Speed</th>
                            <td>{{ $disk->write_speed }} MB/s</td>
                        </tr>
                    </table>
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
    th {
        background-color: #f8f9fc;
    }
</style>
@endpush 