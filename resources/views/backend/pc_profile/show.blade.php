@extends('backend.layouts.master')

@section('title', 'PC Profile Details')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">PC Profile Details</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Profile #{{ $pcProfile->id }} - {{ $pcProfile->pc_name }}</h6>
            <div>
                <a href="{{ route('admin.pc-profiles.edit', $pcProfile->id) }}" class="btn btn-warning btn-sm me-1">Edit Profile</a>
                <a href="{{ route('admin.pc-profiles.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th style="width: 25%;">ID</th>
                        <td>{{ $pcProfile->id }}</td>
                    </tr>
                    <tr>
                        <th>PC Name</th>
                        <td>{{ $pcProfile->pc_name }}</td>
                    </tr>
                    <tr>
                        <th>Hardware ID</th>
                        <td>{{ $pcProfile->hardware_id }}</td>
                    </tr>
                     <tr>
                        <th>Max Chrome Profiles</th>
                        <td>{{ $pcProfile->max_profile_limit }}</td>
                    </tr>
                    <tr>
                        <th>Max Link Limit</th>
                        <td>{{ $pcProfile->max_link_limit }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-{{
                                $pcProfile->status == 'active' ? 'success' :
                                ($pcProfile->status == 'inactive' ? 'secondary' :
                                ($pcProfile->status == 'blocked' ? 'warning' : 
                                ($pcProfile->status == 'deleted' ? 'danger' : 'dark')))
                            }}">
                                {{ ucfirst($pcProfile->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>{{ $pcProfile->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td>{{ $pcProfile->updated_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    @if($pcProfile->trashed())
                    <tr>
                        <th>Deleted At</th>
                        <td>{{ $pcProfile->deleted_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    @endif
                    {{-- Optional: Add counts for related items --}}
                    {{-- 
                    <tr>
                        <th>Chrome Profiles</th>
                        <td>{{ $pcProfile->chrome_profiles_count ?? $pcProfile->chromeProfiles()->count() }}</td>
                    </tr>
                    <tr>
                        <th>Facebook Accounts</th>
                        <td>{{ $pcProfile->facebook_accounts_count ?? $pcProfile->facebookAccounts()->count() }}</td>
                    </tr>
                     <tr>
                        <th>Gmail Accounts</th>
                        <td>{{ $pcProfile->gmail_accounts_count ?? $pcProfile->gmailAccounts()->count() }}</td>
                    </tr> 
                    --}}
                </tbody>
            </table>
        </div>
    </div>

    {{-- Optional: Add tables listing related accounts --}}

</div>
@endsection 