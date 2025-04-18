@extends('backend.layouts.master')

@section('title', 'Chrome Profile Details')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Chrome Profile Details</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Profile #{{ $chrome->id }}</h6>
            <div>
                <a href="{{ route('admin.chrome.edit', $chrome->id) }}" class="btn btn-warning btn-sm me-1">Edit Profile</a>
                <a href="{{ route('admin.chrome.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th style="width: 20%;">ID</th>
                        <td>{{ $chrome->id }}</td>
                    </tr>
                    <tr>
                        <th>PC Profile</th>
                        <td>{{ $chrome->pcProfile->pc_name ?? 'N/A' }} (ID: {{ $chrome->pc_profile_id }})</td>
                    </tr>
                    <tr>
                        <th>Profile Directory</th>
                        <td>{{ $chrome->profile_directory }}</td>
                    </tr>
                    <tr>
                        <th>User Agent</th>
                        <td>{{ $chrome->user_agent }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-{{
                                $chrome->status == 'active' ? 'success' :
                                ($chrome->status == 'pending' ? 'warning' :
                                ($chrome->status == 'inactive' ? 'secondary' :
                                ($chrome->status == 'remove' ? 'danger' : 'dark')))
                            }}">
                                {{ ucfirst($chrome->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>{{ $chrome->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td>{{ $chrome->updated_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    @if($chrome->deleted_at)
                    <tr>
                        <th>Removed At</th>
                        <td>{{ $chrome->deleted_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection 