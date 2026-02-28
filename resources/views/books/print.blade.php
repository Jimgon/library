<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book Inventory List - SNHS Library</title>
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
        table tbody tr:hover {
            background-color: #f0f0f0;
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
    </style>
</head>
<body>

{{-- Print Controls --}}
<div class="no-print btn-group-print">
    <a href="{{ route('books.index') }}" class="btn btn-secondary btn-sm">← Back to Inventory</a>
    <button class="btn btn-primary btn-sm" onclick="window.print()"><i class="bi bi-printer"></i> Print This Page</button>
</div>

{{-- School Header --}}
<div class="header">
    <img src="{{ asset('images/snhs-logo.png') }}" alt="SNHS Logo" class="school-logo">
    <h1 class="school-name">Subic National High School</h1>
    <p class="school-address">Mangan-vaca, Subic, Zambales</p>
    <h2 class="report-title">List of Book Report</h2>
</div>

{{-- Report Metadata --}}
<div class="report-meta">
    <div>
        <strong>Total Books:</strong> {{ count($books) }}
    </div>
    <div>
        <strong>Report Date:</strong> {{ now()->format('M d, Y') }}
    </div>
    <div>
        <strong>Time:</strong> <span id="current-time"></span>
    </div>
</div>

{{-- Books Table --}}
<table>
    <thead>
        <tr>
            <th style="width: 40px;">#</th>
            <th style="width: 50px;">Ctrl #</th>
            <th style="width: 200px;">Title</th>
            <th style="width: 120px;">Author</th>
            <th style="width: 100px;">Category</th>
            <th style="width: 90px;">ISBN</th>
            <th style="width: 80px;" class="text-center">Copies</th>
        </tr>
    </thead>
    <tbody>
    @forelse($books as $i => $book)
        @php
            $ctrlBase = '-';
            if (!empty($book->control_numbers) && is_array($book->control_numbers) && count($book->control_numbers) > 0) {
                $first = $book->control_numbers[0];
                $parts = explode('-', $first);
                $base = $parts[0] ?? ($book->call_number ?? '');
            } else {
                $base = $book->call_number ?? '';
            }
            if (preg_match('/^\d+$/', $base)) {
                $ctrlBase = str_pad(ltrim($base, '0') === '' ? '0' : $base, 3, '0', STR_PAD_LEFT);
            } elseif ($base !== '') {
                $ctrlBase = $base;
            }
        @endphp
        <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            <td class="text-center">{{ $ctrlBase }}</td>
            <td><strong>{{ $book->title }}</strong></td>
            <td>{{ $book->author }}</td>
            <td>{{ $book->category ?? '-' }}</td>
            <td>{{ $book->isbn }}</td>
            <td class="text-center">{{ $book->copies ?? 0 }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="text-center" style="padding: 20px;">No books found in the system.</td>
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

    // Auto-print when opened from the Print All button
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