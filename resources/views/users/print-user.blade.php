<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Report - {{ $user->first_name }} {{ $user->last_name }} - SNHS Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; }
        body { font-family: 'Arial', sans-serif; background: #fff; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #1e3a8a; padding-bottom: 15px; page-break-after: avoid; page-break-inside: avoid; }
        .school-logo { width: 70px; height: 70px; object-fit: contain; margin-bottom: 8px; }
        .school-name { font-size: 22px; font-weight: bold; color: #1e3a8a; margin: 8px 0 3px 0; }
        .school-address { font-size: 12px; color: #555; margin: 0; }
        .report-title { font-size: 18px; font-weight: bold; color: #1e3a8a; margin: 10px 0 15px 0; }
        .report-meta { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 11px; color: #666; page-break-after: avoid; }
        .user-info { background: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px; page-break-inside: avoid; }
        .user-info h5 { color: #1e3a8a; margin-bottom: 10px; font-weight: bold; }
        .user-info-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px; }
        .user-info-item { font-size: 12px; }
        .user-info-item strong { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table thead { background-color: #1e3a8a; color: white; font-weight: bold; }
        table th { padding: 10px; text-align: left; font-size: 12px; border: 1px solid #ddd; }
        table td { padding: 8px 10px; font-size: 11px; border: 1px solid #ddd; }
        table tbody tr:nth-child(even) { background-color: #f9f9f9; }
        table tbody tr:hover { background-color: #f0f0f0; }
        .text-center { text-align: center; }
        .no-print { display: block; }
        .btn-group-print { display: flex; gap: 10px; margin-bottom: 20px; }
        .badge { padding: 4px 8px; font-size: 11px; }
        
        @page {
            size: A4;
            margin: 15mm;
            @bottom-center {
                content: "Page " counter(page) " of " counter(pages);
                font-size: 11px;
                color: #666;
            }
        }
        
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; }
            
            .header { 
                margin: 0;
                padding: 15px;
                page-break-after: avoid; 
                page-break-before: avoid;
                page-break-inside: avoid; 
                border-bottom: 2px solid #1e3a8a;
            }
            
            .school-logo { width: 60px; height: 60px; }
            .school-name { font-size: 18px; margin: 5px 0 3px 0; }
            .school-address { font-size: 11px; margin: 0; }
            .report-title { font-size: 16px; margin: 8px 0 10px 0; }
            .report-meta { font-size: 10px; margin-bottom: 12px; margin-top: 12px; page-break-after: avoid; }
            
            .user-info { background: #f9f9f9; padding: 12px; margin-bottom: 15px; page-break-inside: avoid; }
            .user-info h5 { font-size: 13px; margin-bottom: 8px; }
            .user-info-row { grid-template-columns: 1fr 1fr; gap: 8px; }
            .user-info-item { font-size: 11px; }
            
            table { page-break-inside: auto; margin-top: 10px; width: 100%; }
            table tr { page-break-inside: avoid; }
            table thead { display: table-header-group; background-color: #1e3a8a; color: white; }
            table tbody { display: table-row-group; }
            table th { padding: 8px; font-size: 11px; }
            table td { padding: 6px 8px; font-size: 10px; }
            
            body > * { margin: 0; }
        }
    </style>
</head>
<body>

{{-- Print Controls --}}
<div class="no-print btn-group-print">
    <a href="{{ route('users.show', $user->id) }}" class="btn btn-secondary btn-sm">← Back to Details</a>
    <button class="btn btn-primary btn-sm" onclick="window.print()"><i class="bi bi-printer"></i> Print This Page</button>
</div>

{{-- School Header --}}
<div class="header">
    <img src="{{ asset('images/snhs-logo.png') }}" alt="SNHS Logo" class="school-logo">
    <h1 class="school-name">Subic National High School</h1>
    <p class="school-address">Mangan-vaca, Subic, Zambales</p>
    <h2 class="report-title">Student Report - Borrowing History</h2>
</div>

{{-- Report Metadata --}}
<div class="report-meta">
    <div>
        <strong>Report Date:</strong> {{ now()->format('M d, Y') }}
    </div>
    <div>
        <strong>Time:</strong> <span id="current-time"></span>
    </div>
</div>

{{-- User Information --}}
<div class="user-info">
    <h5>Student Information</h5>
    <div class="user-info-row">
        <div class="user-info-item">
            <strong>Name:</strong> {{ $user->first_name }} {{ $user->last_name }}
        </div>
        <div class="user-info-item">
            <strong>LRN:</strong> {{ $user->lrn ?? '-' }}
        </div>
        <div class="user-info-item">
            <strong>Grade & Section:</strong> {{ $user->grade_section ?? '-' }}
        </div>
        <div class="user-info-item">
            <strong>Phone:</strong> {{ $user->phone_number ?? '-' }}
        </div>
        <div class="user-info-item">
            <strong>Address:</strong> {{ $user->address ?? '-' }}
        </div>
        <div class="user-info-item">
            <strong>Total Books Borrowed:</strong> {{ $user->borrows->count() }}
        </div>
    </div>
</div>

{{-- Borrowing History Table --}}
@if($user->borrows->count() > 0)
<h5 style="color: #1e3a8a; margin-bottom: 10px; font-weight: bold;">Borrowing History</h5>
<table>
    <thead>
        <tr>
            <th style="width: 40px;">#</th>
            <th style="width: 180px;">Book Title</th>
            <th style="width: 100px;">Author</th>
            <th style="width: 80px;">Borrow Date</th>
            <th style="width: 80px;">Due Date</th>
            <th style="width: 70px;">Status</th>
            <th style="width: 100px;">Remarks</th>
        </tr>
    </thead>
    <tbody>
    @php
        $today = \Carbon\Carbon::today();
        $counter = 1;
    @endphp
    @foreach($user->borrows as $borrow)
        @php
            $borrowDate = $borrow->borrowed_at;
            $dueDate = $borrow->due_date;
            $overdueDays = 0;
            
            if ($dueDate && $today->gt($dueDate)) {
                $overdueDays = (int) ceil($today->diffInDays($dueDate));
            }
            
            // Use stored remark if present
            if (!empty($borrow->remark)) {
                $remark = $borrow->remark;
            } else {
                $remark = $overdueDays > 0 ? "{$overdueDays} day(s) overdue" : 'Good Standing';
            }
        @endphp
        <tr>
            <td class="text-center">{{ $counter++ }}</td>
            <td><strong>{{ $borrow->book?->title ?? 'Book not found' }}</strong></td>
            <td>{{ $borrow->book?->author ?? '-' }}</td>
            <td>{{ $borrowDate ? \Carbon\Carbon::parse($borrowDate)->format('M d, Y') : '-' }}</td>
            <td>{{ $dueDate ? \Carbon\Carbon::parse($dueDate)->format('M d, Y') : '-' }}</td>
            <td class="text-center">
                <span class="badge bg-{{ $borrow->returned_at ? 'success' : 'warning' }}">
                    {{ $borrow->returned_at ? 'Returned' : 'Borrowed' }}
                </span>
            </td>
            <td>
                @php
                    $lowerRemark = strtolower($remark);
                    if (str_contains($lowerRemark, 'overdue') || $lowerRemark === 'lost' || $lowerRemark === 'damage') {
                        $rc = 'bg-danger';
                    } elseif ($lowerRemark === 'late return') {
                        $rc = 'bg-warning';
                    } else {
                        $rc = 'bg-success';
                    }
                @endphp
                <span class="badge {{ $rc }}">{{ $remark }}</span>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@else
    <p style="text-align: center; padding: 20px; color: #666; font-size: 13px;">No borrowing history found.</p>
@endif

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
    updateTime();
    setInterval(updateTime, 1000);
    
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
