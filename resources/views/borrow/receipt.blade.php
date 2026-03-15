<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Borrow Receipt - SNHS Library</title>
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
        .details-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .details-section h5 {
            font-size: 14px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 10px;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 8px;
        }
        .details-section p {
            font-size: 12px;
            margin-bottom: 6px;
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
            .details-section {
                page-break-inside: avoid;
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
    </style>
</head>
<body>

{{-- Print Controls --}}
<div class="no-print btn-group-print">
    <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">← Back</a>
    <button class="btn btn-primary btn-sm" onclick="window.print()"><i class="bi bi-printer"></i> Print </button>
</div>

{{-- School Header --}}
<div class="header">
    <img src="{{ asset('images/snhs-logo.png') }}" alt="SNHS Logo" class="school-logo">
    <h1 class="school-name">Subic National High School</h1>
    <p class="school-address">Mangan-vaca, Subic, Zambales</p>
    <h2 class="report-title">Library Borrow Receipt</h2>
</div>

{{-- Report Metadata --}}
<div class="report-meta">
    <div>
        <strong>Receipt Date:</strong> {{ now()->format('M d, Y') }}
    </div>
    <div>
        <strong>Time:</strong> <span id="current-time"></span>
    </div>
    <div>
        <strong>Receipt ID:</strong> #{{ $borrow->id }}
    </div>
</div>

@php
    $borrowedAt = $borrow->borrowed_at ? \Carbon\Carbon::parse($borrow->borrowed_at) : null;
    $dueDate = $borrow->due_date ? \Carbon\Carbon::parse($borrow->due_date) : null;
    $today = \Carbon\Carbon::today(); // Only date, ignore time
    $overdueDays = 0;
    $penalty = 0;

    // Prefer stored remark (admin comment) if present, otherwise compute
    if (!empty($borrow->remark)) {
        $remark = $borrow->remark;
    } else {
        $remark = 'No Remarks';
        if ($dueDate && $today->gt($dueDate)) {
            $overdueDays = $today->diffInDays($dueDate);
            $remark = "{$overdueDays} day(s) overdue";
        }
    }

    // Determine borrower type and get appropriate model
    $borrowerType = 'Student';
    $borrower = null;
    
    if (!empty($borrow->role) && $borrow->role === 'teacher') {
        $borrowerType = 'Teacher';
        $borrower = \App\Models\Teacher::find($borrow->user_id);
    } else {
        $borrowerType = 'Student';
        $borrower = \App\Models\User::find($borrow->user_id);
    }
@endphp

{{-- Borrower Details --}}
<div class="details-section">
    <h5>{{ $borrowerType }} Information</h5>
    @if($borrower)
        @if($borrowerType === 'Teacher')
            <p><strong>Name:</strong> {{ $borrower->name ?? (($borrower->first_name ?? '') . ' ' . ($borrower->last_name ?? '')) }}</p>
            {{-- <p><strong>Department:</strong> {{ $borrower->department ?? 'N/A' }}</p> --}}
            @if($borrower->phone)
                <p><strong>Phone:</strong> {{ $borrower->phone }}</p>
            @endif
            @if($borrower->address)
                <p><strong>Address:</strong> {{ $borrower->address }}</p>
            @endif
        @else
            <p><strong>Name:</strong> {{ $borrower->first_name ?? '' }} {{ $borrower->last_name ?? '' }}</p>
            @if($borrower->grade_section)
                <p><strong>Grade & Section:</strong> {{ $borrower->grade_section }}</p>
            @endif
            @if($borrower->lrn)
                <p><strong>LRN:</strong> {{ $borrower->lrn }}</p>
            @endif
            @if($borrower->phone_number)
                <p><strong>Phone:</strong> {{ $borrower->phone_number }}</p>
            @endif
            @if($borrower->address)
                <p><strong>Address:</strong> {{ $borrower->address }}</p>
            @endif
        @endif
    @else
        <p class="text-danger"><strong>Error:</strong> {{ $borrowerType }} record not found (ID: {{ $borrow->user_id }})</p>
    @endif
</div>

{{-- Book Details --}}
<div class="details-section">
    <h5>Book Information</h5>
    <p><strong>Book Title:</strong> {{ $borrow->book?->title ?? 'Book not found' }}</p>
    @if($borrow->book?->isbn)
        <p><strong>ISBN:</strong> {{ $borrow->book->isbn }}</p>
    @endif
    @if($borrow->book?->author)
        <p><strong>Author:</strong> {{ $borrow->book->author }}</p>
    @endif
    @if($borrow->book?->publisher)
        <p><strong>Publisher:</strong> {{ $borrow->book->publisher }}</p>
    @endif
    @if($borrow->book?->publication_year)
        <p><strong>Publication Year:</strong> {{ $borrow->book->publication_year }}</p>
    @endif
    @if($borrow->book?->pages)
        <p><strong>Pages:</strong> {{ $borrow->book->pages }}</p>
    @endif
    @if($borrow->book?->subject)
        <p><strong>Subject/Category:</strong> {{ $borrow->book->subject }}</p>
    @endif
    @if($borrow->copy_number)
        <p><strong>Control Number:</strong> 
            <span style="font-family: monospace; font-weight: 500;">{{ $borrow->copy_number }}</span>
        </p>
    @endif
    @if($borrow->book?->status)
        <p><strong>Status:</strong> {{ $borrow->book->status }}</p>
    @endif
    @if($borrow->book?->quantity)
        <p><strong>Total Copies:</strong> {{ $borrow->book->quantity }}</p>
    @endif
</div>

{{-- Transaction Details Table --}}
<table>
    <thead>
        <tr>
            <th style="width: 50%;">Attribute</th>
            <th style="width: 50%;">Details</th>
        </tr>
    </thead>
    <tbody>
        @if($borrowedAt)
            <tr>
                <td><strong>Borrowed Date</strong></td>
                <td>{{ $borrowedAt->format('F j, Y') }}</td>
            </tr>
        @endif
        @if($dueDate)
            <tr>
                <td><strong>Due Date</strong></td>
                <td>{{ $dueDate->format('F j, Y') }}</td>
            </tr>
        @endif
        <tr>
            <td><strong>Overdue Days</strong></td>
            <td>{{ $overdueDays }}</td>
        </tr>
        <tr>
            <td><strong>Remarks</strong></td>
            <td>{{ $remark }}</td>
        </tr>
        <tr>
            <td><strong>Notes</strong></td>
            <td>{{ $borrow->notes ?? 'No additional notes' }}</td>
        </tr>
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
