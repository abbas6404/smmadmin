<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>AIO Innovation Ltd - Batch Report #{{ $submissionBatch->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 40px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .company-info {
            font-size: 12px;
            color: #666;
            margin-bottom: 15px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .report-title {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
        }
        .batch-info {
            margin-bottom: 30px;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .label {
            font-weight: bold;
            width: 180px;
            display: inline-block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .status-section {
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
            color: #333;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            opacity: 0.07;
            font-size: 100px;
            font-weight: bold;
            color: #000;
            z-index: -1;
            white-space: nowrap;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="watermark">AIO Innovation Ltd</div>
    
    <div class="header">
        <div class="company-name">AIO Innovation Ltd</div>
        <div class="company-info">
            Social Media Marketing Solutions<br>
            Software Solutions, and IT Training
        </div>
        <h1 class="report-title">Submission Batch Report</h1>
        <p>Generated on {{ now()->format('F d, Y H:i:s') }}</p>
    </div>

    <div class="batch-info">
        <div class="info-row">
            <span class="label">Batch ID:</span>
            <span>#{{ $submissionBatch->id }}</span>
        </div>
        <div class="info-row">
            <span class="label">Batch Name:</span>
            <span>{{ $submissionBatch->name }}</span>
        </div>
        <div class="info-row">
            <span class="label">Submission Type:</span>
            <span>{{ ucfirst(str_replace('_', ' & ', $submissionBatch->submission_type)) }}</span>
        </div>
        <div class="info-row">
            <span class="label">Created By:</span>
            <span>{{ $submissionBatch->user->name }}</span>
        </div>
        <div class="info-row">
            <span class="label">Created Date:</span>
            <span>{{ $submissionBatch->created_at->format('F d, Y H:i:s') }}</span>
        </div>
        <div class="info-row">
            <span class="label">Status:</span>
            <span>{{ $submissionBatch->approved ? 'Approved' : 'Not Approved' }}</span>
        </div>
    </div>

    <h2>Submission Statistics</h2>
    <div class="batch-info">
        <div class="info-row">
            <span class="label">Total Submissions:</span>
            <span>{{ number_format($submissionBatch->total_submissions) }}</span>
        </div>
        <div class="info-row">
            <span class="label">Accurate Submissions:</span>
            <span>{{ number_format($submissionBatch->accurate_submissions) }}</span>
        </div>
        <div class="info-row">
            <span class="label">Incorrect Submissions:</span>
            <span>{{ number_format($submissionBatch->total_submissions - $submissionBatch->accurate_submissions) }}</span>
        </div>
        <div class="info-row">
            <span class="label">Success Rate:</span>
            <span>{{ $submissionBatch->total_submissions > 0 
                ? number_format(($submissionBatch->accurate_submissions / $submissionBatch->total_submissions) * 100, 2) 
                : 0 }}%</span>
        </div>
    </div>

    @if($submissionBatch->submission_type === 'facebook_and_gmail')
        <h2>Facebook Accounts Summary</h2>
        
        @php
            $inactiveAccounts = $submissionBatch->facebookAccounts->where('status', 'inactive');
            $activeAccounts = $submissionBatch->facebookAccounts->where('status', 'active');
            $pendingAccounts = $submissionBatch->facebookAccounts->where('status', 'pending');
            $processingAccounts = $submissionBatch->facebookAccounts->where('status', 'processing');
            $logoutAccounts = $submissionBatch->facebookAccounts->where('status', 'logout');
            $removeAccounts = $submissionBatch->facebookAccounts->where('status', 'remove');
        @endphp

        @if($inactiveAccounts->count() > 0)
            <div class="status-section">Inactive Accounts</div>
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Have Page</th>
                        <th>Total Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inactiveAccounts as $account)
                        <tr>
                            <td>{{ $account->email }}</td>
                            <td>Inactive</td>
                            <td>{{ $account->have_page ? 'Yes' : 'No' }}</td>
                            <td>{{ number_format($account->total_count) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if($pendingAccounts->count() > 0)
            <div class="status-section">Pending Accounts</div>
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Have Page</th>
                        <th>Total Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingAccounts as $account)
                        <tr>
                            <td>{{ $account->email }}</td>
                            <td>Pending</td>
                            <td>{{ $account->have_page ? 'Yes' : 'No' }}</td>
                            <td>{{ number_format($account->total_count) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if($processingAccounts->count() > 0)
            <div class="status-section">Processing Accounts</div>
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Have Page</th>
                        <th>Total Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($processingAccounts as $account)
                        <tr>
                            <td>{{ $account->email }}</td>
                            <td>Processing</td>
                            <td>{{ $account->have_page ? 'Yes' : 'No' }}</td>
                            <td>{{ number_format($account->total_count) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if($logoutAccounts->count() > 0)
            <div class="status-section">Logout Accounts</div>
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Have Page</th>
                        <th>Total Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logoutAccounts as $account)
                        <tr>
                            <td>{{ $account->email }}</td>
                            <td>Logout</td>
                            <td>{{ $account->have_page ? 'Yes' : 'No' }}</td>
                            <td>{{ number_format($account->total_count) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if($removeAccounts->count() > 0)
            <div class="status-section">Remove Accounts</div>
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Have Page</th>
                        <th>Total Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($removeAccounts as $account)
                        <tr>
                            <td>{{ $account->email }}</td>
                            <td>Remove</td>
                            <td>{{ $account->have_page ? 'Yes' : 'No' }}</td>
                            <td>{{ number_format($account->total_count) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @elseif($submissionBatch->submission_type === 'facebook')
        <h2>Facebook Accounts Summary</h2>
        
        @php
            $inactiveAccounts = $submissionBatch->facebookAccounts->where('status', 'inactive');
            $activeAccounts = $submissionBatch->facebookAccounts->where('status', 'active');
            $pendingAccounts = $submissionBatch->facebookAccounts->where('status', 'pending');
            $processingAccounts = $submissionBatch->facebookAccounts->where('status', 'processing');
            $logoutAccounts = $submissionBatch->facebookAccounts->where('status', 'logout');
            $removeAccounts = $submissionBatch->facebookAccounts->where('status', 'remove');
        @endphp

        @if($inactiveAccounts->count() > 0)
            <div class="status-section">Inactive Accounts</div>
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Have Page</th>
                        <th>Total Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inactiveAccounts as $account)
                        <tr>
                            <td>{{ $account->email }}</td>
                            <td>Inactive</td>
                            <td>{{ $account->have_page ? 'Yes' : 'No' }}</td>
                            <td>{{ number_format($account->total_count) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if($pendingAccounts->count() > 0)
            <div class="status-section">Pending Accounts</div>
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Have Page</th>
                        <th>Total Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingAccounts as $account)
                        <tr>
                            <td>{{ $account->email }}</td>
                            <td>Pending</td>
                            <td>{{ $account->have_page ? 'Yes' : 'No' }}</td>
                            <td>{{ number_format($account->total_count) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if($processingAccounts->count() > 0)
            <div class="status-section">Processing Accounts</div>
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Have Page</th>
                        <th>Total Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($processingAccounts as $account)
                        <tr>
                            <td>{{ $account->email }}</td>
                            <td>Processing</td>
                            <td>{{ $account->have_page ? 'Yes' : 'No' }}</td>
                            <td>{{ number_format($account->total_count) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if($logoutAccounts->count() > 0)
            <div class="status-section">Logout Accounts</div>
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Have Page</th>
                        <th>Total Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logoutAccounts as $account)
                        <tr>
                            <td>{{ $account->email }}</td>
                            <td>Logout</td>
                            <td>{{ $account->have_page ? 'Yes' : 'No' }}</td>
                            <td>{{ number_format($account->total_count) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if($removeAccounts->count() > 0)
            <div class="status-section">Remove Accounts</div>
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Have Page</th>
                        <th>Total Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($removeAccounts as $account)
                        <tr>
                            <td>{{ $account->email }}</td>
                            <td>Remove</td>
                            <td>{{ $account->have_page ? 'Yes' : 'No' }}</td>
                            <td>{{ number_format($account->total_count) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @elseif($submissionBatch->submission_type === 'gmail')
        <h2>Gmail Accounts Summary</h2>
        
        @php
            $inactiveAccounts = $submissionBatch->gmailAccounts->where('status', 'inactive');
            $activeAccounts = $submissionBatch->gmailAccounts->where('status', 'active');
            $pendingAccounts = $submissionBatch->gmailAccounts->whereIn('status', ['pending', 'processing']);
        @endphp

        @if($inactiveAccounts->count() > 0)
            <div class="status-section">Inactive Accounts</div>
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Total Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inactiveAccounts as $account)
                        <tr>
                            <td>{{ $account->email }}</td>
                            <td>Inactive</td>
                            <td>{{ number_format($account->total_count) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if($activeAccounts->count() > 0)
            <div class="status-section">Active Accounts</div>
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Total Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeAccounts as $account)
                        <tr>
                            <td>{{ $account->email }}</td>
                            <td>Active</td>
                            <td>{{ number_format($account->total_count) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if($pendingAccounts->count() > 0)
            <div class="status-section">Pending Accounts</div>
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Total Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingAccounts as $account)
                        <tr>
                            <td>{{ $account->email }}</td>
                            <td>{{ ucfirst($account->status) }}</td>
                            <td>{{ number_format($account->total_count) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endif

    @if($submissionBatch->notes)
        <h2>Notes</h2>
        <p>{{ $submissionBatch->notes }}</p>
    @endif

    <div class="footer">
        <p>
            <strong>AIO Innovation Ltd</strong><br>
            Social Media Marketing Solutions<br>
            This is an automatically generated report. Generated on {{ now()->format('F d, Y H:i:s') }}
        </p>
    </div>
</body>
</html> 