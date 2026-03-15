<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>All Borrow Receipts - SNHS Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #fff;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #1e3a8a;
            padding-bottom: 20px;
        }
        .school-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 10px;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 10px 0 5px 0;
        }
        .school-address {
            font-size: 14px;
            color: #555;
            margin: 0;
        }
        .report-title {
            font-size: 20px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 15px 0 20px 0;
        }
        .report-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 12px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table thead {
            background-color: #1e3a8a;
            color: white;
            font-weight: bold;
        }
        table th {
            padding: 10px;
            text-align: left;
            font-size: 12px;
            border: 1px solid #ddd;
        }
        table td {
            padding: 8px 10px;
            font-size: 11px;
            border: 1px solid #ddd;
        }
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .no-print {
            display: block;
        }
        @media print {
            .no-print { 
                display: none !important; 
            }
            body {
                margin: 0;
                padding: 10px;
            }
            .header {
                margin-bottom: 20px;
                padding-bottom: 15px;
                break-after: avoid;
            }
            table {
                page-break-inside: avoid;
                margin-top: 10px;
            }
            table tbody tr {
                page-break-inside: avoid;
            }
        }
        .btn-group-print {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .summary-footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #1e3a8a;
            font-size: 12px;
            text-align: right;
            color: #555;
        }
        .badge-status {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-overdue {
            background-color: #dc3545;
            color: white;
        }
        .badge-ontime {
            background-color: #28a745;
            color: white;
        }
    </style>
</head>
<body>

{{-- Print Controls --}}
<div class="no-print btn-group-print">
    <a href="{{ route('borrow.return.index') }}" class="btn btn-secondary btn-sm">← Back</a>
    <button class="btn btn-primary btn-sm" onclick="window.print()">🖨 Print All</button>
</div>

{{-- School Header --}}
<div class="header">
    <img src="{{ asset('images/snhs-logo.png') }}" alt="SNHS Logo" class="school-logo">
    <h1 class="school-name">Subic National High School</h1>
    <p class="school-address">Mangan-vaca, Subic, Zambales</p>
    <h2 class="report-title">Library Borrow List - All Active Borrows</h2>
</div>

{{-- Report Metadata --}}
<div class="report-meta">
    <div>
        <strong>Report Date:</strong> {{ now()->format('M d, Y') }}
    </div>
    <div>
        <strong>Time:</strong> <span id="current-time"></span>
    </div>
    <div>
        <strong>Total Records:</strong> {{ $borrows->count() }}
    </div>
</div>

{{-- Main Table --}}
<table>
    <thead>
        <tr>
            <th style="width: 8%;">Receipt ID</th>
            <th style="width: 14%;">Borrower Name</th>
            <th style="width: 10%;">Type</th>
            <th style="width: 16%;">Book Title</th>
            <th style="width: 9%;">Control #</th>
            <th style="width: 10%;">Borrowed Date</th>
            <th style="width: 10%;">Due Date</th>
            <th style="width: 8%;">Status</th>
            <th style="width: 15%;">Remarks</th>
        </tr>
    </thead>
    <tbody>
        @forelse($borrows as $borrow)
            @php
                $borrower = null;
                $borrowerType = 'Student';
                
                // Use the role field from Borrow to determine type
                if (!empty($borrow->role) && $borrow->role === 'teacher') {
                    $borrowerType = 'Teacher';
                    $borrower = \App\Models\Teacher::find($borrow->user_id);
                } else {
                    $borrowerType = 'Student';
                    $borrower = \App\Models\User::find($borrow->user_id);
                }
                
                $borrowedAt = $borrow->borrowedAt;
                $dueDate = $borrow->dueDate;
                $overdueDays = $borrow->overdueDays;
                $remark = $borrow->remark;
                
                // Get control number from copy_number field
                $ctrlNum = $borrow->copy_number ?? 'N/A';
                
                // Determine status badge
                $statusClass = 'badge-ontime';
                $statusText = 'Borrowed';
                if (strpos(strtolower($remark), 'overdue') !== false) {
                    $statusClass = 'badge-overdue';
                    $statusText = 'Overdue';
                }
            @endphp
            <tr>
                <td class="text-center">#{{ $borrow->id }}</td>
                <td>
                    @if($borrower)
                        {{ $borrower->name ?? (($borrower->first_name ?? 'Unknown') . ' ' . ($borrower->last_name ?? '')) }}
                    @else
                        Unknown
                    @endif
                </td>
                <td class="text-center">{{ $borrowerType }}</td>
                <td>{{ $borrow->book?->title ?? 'Book not found' }}</td>
                <td class="text-center" style="font-family: monospace; font-weight: 500;">{{ $ctrlNum }}</td>
                <td class="text-center">{{ $borrowedAt ? $borrowedAt->format('M d, Y') : 'N/A' }}</td>
                <td class="text-center">{{ $dueDate ? $dueDate->format('M d, Y') : 'N/A' }}</td>
                <td class="text-center">
                    <span class="badge-status {{ $statusClass }}">{{ $statusText }}</span>
                </td>
                <td>{{ $remark }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center">No active borrowings found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- Summary Footer --}}
<div class="summary-footer no-print">
    Generated by SNHS Library System
</div>

<script>
    // Display current time in 12-hour format with AM/PM
    function updateTime() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const ampm = now.getHours() >= 12 ? 'PM' : 'AM';
        const displayHours = now.getHours() % 12 || 12;
        const timeString = `${displayHours}:${minutes} ${ampm}`;
        document.getElementById('current-time').textContent = timeString;
    }

    // Update time on load and every second
    updateTime();
    setInterval(updateTime, 1000);

    // Auto-print when opened from the Print button
    window.addEventListener('load', function() {
        setTimeout(function() {
            if (!new URLSearchParams(window.location.search).has('noauto')) {
                window.print();
            }
        }, 300);
    });
</script>

</body>
</html>
