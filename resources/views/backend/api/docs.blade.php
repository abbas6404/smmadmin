@extends('backend.layouts.master')

@section('title', 'API Documentation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">API Documentation</h4>
                </div>
                <div class="card-body">
                    <div class="api-section">
                        <h5>Authentication</h5>
                        <p>All API requests require authentication using an access token. Include your access token in the request header:</p>
                        <pre><code>Authorization: Bearer YOUR_ACCESS_TOKEN</code></pre>
                        <p>To get an access token, use the authentication endpoint:</p>
                        <div class="endpoint">
                            <h6>Generate Access Token</h6>
                            <p><strong>POST /api/generate-token</strong></p>
                            <p>Request Body:</p>
                            <pre><code>{
    "email": "your_email@example.com",
    "password": "your_password",
    "pc_name": "Optional PC Name",
    "hostname": "Optional Hostname",
    "os_version": "Optional OS Version",
    "hardware_id": "Optional Hardware ID"
}</code></pre>
                            <p>Response (Success):</p>
                            <pre><code>{
    "success": true,
    "message": "Access token generated successfully",
    "data": {
        "access_token": "your_access_token",
        "pc_profile": {
            "id": 1,
            "email": "your_email@example.com",
            "pc_name": "Your PC Name",
            "hostname": "Your Hostname",
            "os_version": "Your OS Version",
            "hardware_id": "Your Hardware ID",
            "status": "active",
            "last_verified_at": "2024-01-01T00:00:00.000000Z"
        }
    }
}</code></pre>
                            <p>Possible Error Responses:</p>
                            <ul>
                                <li><strong>422 Validation Error</strong> - Invalid input data</li>
                                <li><strong>404 Not Found</strong> - PC profile not found</li>
                                <li><strong>401 Unauthorized</strong> - Invalid password</li>
                                <li><strong>409 Conflict</strong> - Hardware ID already registered</li>
                                <li><strong>403 Forbidden</strong> - PC profile blocked or deleted</li>
                                <li><strong>200 OK</strong> - Access token already exists (profile is active)</li>
                            </ul>
                        </div>
                    </div>

                    <div class="api-section">
                        <h5>Base URL</h5>
                        <p>All API endpoints are relative to the base URL:</p>
                        <pre><code>{{ url('/api') }}</code></pre>
                    </div>

                    <div class="api-section">
                        <h5>Endpoints</h5>
                        
                        <div class="endpoint">
                            <h6>Get PC Profile</h6>
                            <p><strong>POST /api/get-profile</strong></p>
                            <p>Headers:</p>
                            <pre><code>Authorization: Bearer YOUR_ACCESS_TOKEN</code></pre>
                            <p>Request Body (Optional):</p>
                            <pre><code>{
    "cpu_cores": 8,
    "total_memory": 16384,
    "disks": [
        {
            "drive_letter": "C",
            "file_system": "NTFS",
            "total_size": 0.25,      // Size in GB (e.g., 0.25 GB)
            "free_space": 0.15,      // Size in GB (e.g., 0.15 GB)
            "used_space": 0.10,      // Size in GB (e.g., 0.10 GB)
            "health_percentage": 98.5,
            "read_speed": 550,
            "write_speed": 520
        }
    ]
}</code></pre>
                            <p>Response (Success):</p>
                            <pre><code>{
    "success": true,
    "message": "PC profile information retrieved successfully",
    "data": {
        "pc_profile": {
            "id": 1,
            "email": "your_email@example.com",
            "pc_name": "Your PC Name",
            "hostname": "Your Hostname",
            "os_version": "Your OS Version",
            "hardware_id": "Your Hardware ID",
            "user_agent": "115-123",
            "profile_root_directory": "C:\\profiles",
            "status": "active",
            "last_verified_at": "2024-01-01T00:00:00.000000Z",
            "limits": {
                "max_profile_limit": 10,
                "max_order_limit": 5,
                "min_order_limit": 1
            },
            "system_info": {
                "cpu_cores": 8,
                "total_memory": 16384
            },
            "disks": [
                {
                    "drive_letter": "C",
                    "file_system": "NTFS",
                    "total_size": 0.25,      // Size in GB
                    "free_space": 0.15,      // Size in GB
                    "used_space": 0.10,      // Size in GB
                    "health_percentage": 98.5,
                    "read_speed": 550,
                    "write_speed": 520,
                    "last_checked_at": "2024-01-01T00:00:00.000000Z"
                }
            ]
        }
    }
}</code></pre>
                            <p>Field Descriptions:</p>
                            <ul>
                                <li><strong>cpu_cores</strong> (optional) - Number of CPU cores</li>
                                <li><strong>total_memory</strong> (optional) - Total system memory in MB</li>
                                <li><strong>disks</strong> (optional) - Array of disk information</li>
                                <li><strong>disks[].drive_letter</strong> (required if disks provided) - Drive letter (e.g., "C")</li>
                                <li><strong>disks[].file_system</strong> (optional) - File system type (e.g., "NTFS")</li>
                                <li><strong>disks[].total_size</strong> (optional) - Total disk size in GB</li>
                                <li><strong>disks[].free_space</strong> (optional) - Free space in GB</li>
                                <li><strong>disks[].used_space</strong> (optional) - Used space in GB</li>
                                <li><strong>disks[].health_percentage</strong> (optional) - Disk health percentage (0-100)</li>
                                <li><strong>disks[].read_speed</strong> (optional) - Disk read speed in MB/s</li>
                                <li><strong>disks[].write_speed</strong> (optional) - Disk write speed in MB/s</li>
                            </ul>
                            <p>Notes:</p>
                            <ul>
                                <li>All fields in the request body are optional</li>
                                <li>Only provided fields will be updated</li>
                                <li>Disk sizes are handled in GB in the API but stored in bytes in the database</li>
                                <li>Disk size values are rounded to 2 decimal places in responses</li>
                                <li>The drive_letter field is required only when providing disk information</li>
                            </ul>
                            <p>Possible Error Responses:</p>
                            <ul>
                                <li><strong>401 Unauthorized</strong> - Missing or invalid access token</li>
                                <li><strong>403 Forbidden</strong> - PC profile blocked, deleted, or inactive</li>
                                <li><strong>422 Validation Error</strong> - Invalid input data</li>
                                <li><strong>500 Server Error</strong> - Unexpected error during update</li>
                            </ul>
                        </div>

                        <div class="endpoint">
                            <h6>Get Pending Facebook Accounts</h6>
                            <p><strong>GET /api/pending-facebook-accounts</strong></p>
                            <p>Headers:</p>
                            <pre><code>Authorization: Bearer YOUR_ACCESS_TOKEN</code></pre>
                            <p>Response (Success):</p>
                            <pre><code>{
    "success": true,
    "message": "Pending Facebook account retrieved and updated to processing",
    "data": {
        "facebook_account": {
            "id": 1,
            "email": "facebook@example.com",
            "password": "password123",
            "two_factor_secret": "K35A YRDA ADHP FIWT XCBU SRJS O54L LCTS",
            "status": "processing",
            "pc_profile_id": 1,
            "chrome_profile_id": 1,
            "have_page": 0,
            "account_cookies": null,
            "gmail_account_id": 1,
            "created_at": "2024-01-01T00:00:00.000000Z",
            "note": "Test account"
        },
        "gmail_account": {
            "id": 1,
            "email": "gmail@example.com",
            "password": "password123",
            "status": "active",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "note": "Test account"
        },
        "chrome_profile": {
            "id": 1,
            "profile_directory": "C:\\profiles\\chrome_123",
            "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.6099.130 Safari/537.36",
            "status": "pending",
            "created_at": "2024-01-01T00:00:00.000000Z"
        }
    }
}</code></pre>
                            <p>Field Descriptions:</p>
                            <ul>
                                <li><strong>facebook_account.id</strong> - Unique identifier for the Facebook account</li>
                                <li><strong>facebook_account.email</strong> - Facebook account email or user ID</li>
                                <li><strong>facebook_account.password</strong> - Facebook account password</li>
                                <li><strong>facebook_account.two_factor_secret</strong> - Two-factor authentication secret key (if enabled)</li>
                                <li><strong>facebook_account.status</strong> - Account status (pending, processing, active, inactive, remove)</li>
                                <li><strong>facebook_account.pc_profile_id</strong> - ID of the associated PC profile</li>
                                <li><strong>facebook_account.chrome_profile_id</strong> - ID of the associated Chrome profile</li>
                                <li><strong>facebook_account.have_page</strong> - Whether the account has a Facebook page</li>
                                <li><strong>facebook_account.account_cookies</strong> - Stored account cookies (if any)</li>
                                <li><strong>facebook_account.gmail_account_id</strong> - ID of the associated Gmail account (if any)</li>
                                <li><strong>facebook_account.created_at</strong> - Account creation timestamp</li>
                                <li><strong>facebook_account.note</strong> - Additional notes about the account</li>
                            </ul>
                            <p>Notes:</p>
                            <ul>
                                <li>The endpoint automatically updates the account status to 'processing' when retrieved</li>
                                <li>If no Chrome profile exists, a new one will be created with a random user agent</li>
                                <li>The Chrome profile's directory will be created in the PC profile's root directory</li>
                                <li>If a Gmail account is associated, its Chrome profile will be updated to match</li>
                                <li>The two_factor_secret field will be null if 2FA is not enabled for the account</li>
                            </ul>
                            <p>Possible Error Responses:</p>
                            <ul>
                                <li><strong>401 Unauthorized</strong> - Missing or invalid access token</li>
                                <li><strong>404 Not Found</strong> - No pending accounts available</li>
                                <li><strong>403 Forbidden</strong> - PC profile is not active</li>
                                <li><strong>500 Server Error</strong> - Failed to process Facebook account</li>
                            </ul>
                        </div>

                        <div class="endpoint">
                            <h6>Update Facebook Account</h6>
                            <p><strong>POST /api/update-facebook-account</strong></p>
                            <p>Headers:</p>
                            <pre><code>Authorization: Bearer YOUR_ACCESS_TOKEN</code></pre>
                            <p>Request Body:</p>
                            <pre><code>{
    "id": 1,
    "note": "Updated account information",
    "lang": "en",
    "account_cookies": {
        "cookie1": "value1",
        "cookie2": "value2"
    },
    "status": "active",
    "have_use": true,
    "have_page": true,
    "have_post": false,
    "chrome_profile": {
        "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.6099.130 Safari/537.36",
        "status": "active"
    }
}</code></pre>
                            <p>Field Descriptions:</p>
                            <ul>
                                <li><strong>id</strong> (required) - The ID of the Facebook account to update</li>
                                <li><strong>note</strong> (optional) - Additional notes about the account</li>
                                <li><strong>lang</strong> (optional) - Language code (en, bn, as, ar, fr, es, de, it, pt, ru, ja, ko, zh)</li>
                                <li><strong>account_cookies</strong> (optional) - Array of account cookies</li>
                                <li><strong>status</strong> (required) - Account status (active, inactive, remove)</li>
                                <li><strong>have_use</strong> (optional) - Boolean indicating if account has been used</li>
                                <li><strong>have_page</strong> (optional) - Boolean indicating if account has a page</li>
                                <li><strong>have_post</strong> (optional) - Boolean indicating if account has posts</li>
                                <li><strong>chrome_profile</strong> (optional) - Chrome profile settings</li>
                                <li><strong>chrome_profile.user_agent</strong> (optional) - Chrome user agent string</li>
                                <li><strong>chrome_profile.status</strong> (optional) - Chrome profile status (pending, active, inactive, blocked)</li>
                            </ul>
                            <p>Response (Success):</p>
                            <pre><code>{
    "success": true,
    "message": "Facebook account updated successfully",
    "data": {
        "facebook_account": {
            "id": 1,
            "email": "facebook@example.com",
            "status": "active",
            "note": "Updated account information",
            "lang": "en",
            "have_use": true,
            "have_page": true,
            "have_post": false,
            "account_cookies": {
                "cookie1": "value1",
                "cookie2": "value2"
            }
        },
        "chrome_profile": {
            "id": 1,
            "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.6099.130 Safari/537.36",
            "status": "active",
            "profile_directory": "C:\\profiles\\chrome_123"
        }
    }
}</code></pre>
                            <p>Possible Error Responses:</p>
                            <ul>
                                <li><strong>401 Unauthorized</strong> - Missing or invalid access token</li>
                                <li><strong>403 Forbidden</strong> - PC profile is not active</li>
                                <li><strong>404 Not Found</strong> - Facebook account not found</li>
                                <li><strong>422 Validation Error</strong> - Invalid input data</li>
                                <li><strong>500 Server Error</strong> - Unexpected error during update</li>
                            </ul>
                            <p>Notes:</p>
                            <ul>
                                <li>All fields except 'id' and 'status' are optional</li>
                                <li>If a field is not provided, its current value will be preserved</li>
                                <li>The update is performed within a transaction - if any part fails, all changes are rolled back</li>
                                <li>Chrome profile updates are only applied if the Facebook account has an associated Chrome profile</li>
                            </ul>
                        </div>

                        <div class="endpoint">
                            <h6>Get Orders</h6>
                            <p><strong>GET /api/orders</strong></p>
                            <p>Headers:</p>
                            <pre><code>Authorization: Bearer YOUR_ACCESS_TOKEN</code></pre>
                            <p>Response (Success - Facebook Orders):</p>
                            <pre><code>{
    "status": "success",
    "message": "Facebook orders retrieved successfully",
    "max_order_limit": 5,
    "min_order_limit": 1,
    "total_pending_processing_orders": 10,
    "category": "facebook",
    "facebook_account": {
        "id": 1,
        "email": "facebook@example.com",
        "password": "password123",
        "status": "active",
        "have_use": true,
        "have_page": true,
        "have_post": false,
        "lang": "en",
        "account_cookies": {
            "cookie1": "value1",
            "cookie2": "value2"
        },
        "chrome_profile": {
            "id": 1,
            "profile_directory": "C:\\profiles\\chrome_123",
            "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.6099.130 Safari/537.36",
            "status": "active"
        }
    },
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "service_id": 1,
            "link": "https://example.com",
            "link_uid": "unique_link_id",
            "quantity": 100,
            "status": "processing",
            "remains": 99,
            "category": "facebook",
            "error_message": null
        }
    ]
}</code></pre>
                            <p>Response (Success - Gmail Orders):</p>
                            <pre><code>{
    "status": "success",
    "message": "Gmail orders retrieved successfully",
    "max_order_limit": 5,
    "min_order_limit": 1,
    "total_pending_processing_orders": 10,
    "category": "gmail",
    "gmail_account": {
        "id": 1,
        "email": "gmail@example.com",
        "password": "password123",
        "status": "active",
        "have_use": true,
        "chrome_profile": {
            "id": 1,
            "profile_directory": "C:\\profiles\\chrome_123",
            "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.6099.130 Safari/537.36",
            "status": "active"
        }
    },
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "service_id": 1,
            "link": "https://example.com",
            "link_uid": "unique_link_id",
            "quantity": 100,
            "status": "processing",
            "remains": 99,
            "category": "gmail",
            "error_message": null
        }
    ]
}</code></pre>
                            <p>Response (No Orders Available):</p>
                            <pre><code>{
    "status": "shutdown",
    "message": "Not enough new orders available for any account",
    "max_order_limit": 5,
    "min_order_limit": 1,
    "total_pending_processing_orders": 0,
    "data": []
}</code></pre>
                            <p>Possible Error Responses:</p>
                            <ul>
                                <li><strong>401 Unauthorized</strong> - Missing or invalid access token</li>
                                <li><strong>403 Forbidden</strong> - PC profile is not active</li>
                                <li><strong>500 Server Error</strong> - Unexpected error during processing</li>
                            </ul>
                            <p>Notes:</p>
                            <ul>
                                <li>Orders are automatically marked as failed if they have an empty link_uid</li>
                                <li>Only orders with valid links, quantities, and prices are returned</li>
                                <li>Orders are grouped by service category (facebook/gmail)</li>
                                <li>For Facebook orders, only accounts with Chrome profiles and language set to 'en' are considered</li>
                                <li>For Gmail orders, only accounts with Chrome profiles are considered</li>
                                <li>If no unused accounts are found, all accounts are reset and tried again</li>
                                <li>Orders are filtered to avoid duplicates using link_uid</li>
                                <li>When orders are assigned, their status is updated to 'processing' and remains are decremented</li>
                                <li>If remains reaches 0, the order is marked as 'completed'</li>
                            </ul>
                        </div>
                    </div>

                    <div class="api-section">
                        <h5>Error Responses</h5>
                        <p>Error responses follow this format:</p>
                        <pre><code>{
    "success": false,
    "message": "Error description",
    "data": {
        "error_code": "ERROR_CODE",
        "suggestion": "Suggested action"
    }
}</code></pre>
                        <p>Common error codes:</p>
                        <ul>
                            <li><strong>MISSING_ACCESS_TOKEN</strong> - Access token is required in the Authorization header</li>
                            <li><strong>INVALID_ACCESS_TOKEN</strong> - Access token not found or invalid</li>
                            <li><strong>INACTIVE_PC_PROFILE</strong> - PC profile is not active</li>
                            <li><strong>NO_PENDING_ACCOUNTS</strong> - No pending accounts available</li>
                            <li><strong>PROCESSING_ERROR</strong> - Server error during processing</li>
                            <li><strong>HARDWARE_ID_CONFLICT</strong> - Hardware ID already registered with another profile</li>
                            <li><strong>PROFILE_BLOCKED</strong> - PC profile has been blocked</li>
                            <li><strong>PROFILE_DELETED</strong> - PC profile has been deleted</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.api-section {
    margin-bottom: 2rem;
}
.api-section h5 {
    color: #333;
    margin-bottom: 1rem;
}
.api-section h6 {
    color: #555;
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}
.endpoint {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 5px;
    margin-bottom: 1rem;
}
pre {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 5px;
    overflow-x: auto;
}
code {
    color: #e83e8c;
}
</style>
@endpush 