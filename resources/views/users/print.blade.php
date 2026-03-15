<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student List - SNHS Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; }
        body { font-family: 'Arial', sans-serif; background: #fff; color: #333; }
        .search-form { background: #f8f9fa; padding: 20px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #dee2e6; }
        .search-form h3 { font-size: 18px; font-weight: bold; margin-bottom: 15px; color: #333; }
        .search-form .form-group { margin-bottom: 10px; }
        .search-form label { font-weight: 600; font-size: 12px; margin-bottom: 5px; display: block; }
        .search-form input, .search-form select { padding: 8px 12px; font-size: 12px; border: 1px solid #ced4da; border-radius: 3px; width: 100%; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #1e3a8a; padding-bottom: 15px; page-break-after: avoid; page-break-inside: avoid; }
        .school-logo { width: 70px; height: 70px; object-fit: contain; margin-bottom: 8px; }
        .school-name { font-size: 22px; font-weight: bold; color: #1e3a8a; margin: 8px 0 3px 0; }
        .school-address { font-size: 12px; color: #555; margin: 0; }
        .report-title { font-size: 18px; font-weight: bold; color: #1e3a8a; margin: 10px 0 15px 0; }
        .report-meta { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 11px; color: #666; page-break-after: avoid; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table thead { background-color: #1e3a8a; color: white; font-weight: bold; }
        table th { padding: 10px; text-align: left; font-size: 12px; border: 1px solid #ddd; }
        table td { padding: 8px 10px; font-size: 11px; border: 1px solid #ddd; }
        table tbody tr:nth-child(even) { background-color: #f9f9f9; }
        table tbody tr:hover { background-color: #f0f0f0; }
        .text-center { text-align: center; }
        .no-print { display: block; }
        .btn-group-print { display: flex; gap: 10px; margin-bottom: 20px; }
        .summary-footer { margin-top: 20px; padding-top: 15px; border-top: 2px solid #1e3a8a; font-size: 12px; text-align: right; color: #555; }
        
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
            .search-form { display: none !important; }
            body { margin: 0; padding: 0; }
            
            /* Make header repeat on every page */
            .header { 
                margin: 0;
                padding: 15px;
                page-break-after: avoid; 
                page-break-before: avoid;
                page-break-inside: avoid; 
                border-bottom: 2px solid #1e3a8a;
                position: running(header);
            }
            
            @page {
                @top-center {
                    content: element(header);
                }
            }
            
            .school-logo { width: 60px; height: 60px; }
            .school-name { font-size: 18px; margin: 5px 0 3px 0; }
            .school-address { font-size: 11px; margin: 0; }
            .report-title { font-size: 16px; margin: 8px 0 10px 0; }
            .report-meta { font-size: 10px; margin-bottom: 12px; margin-top: 12px; page-break-after: avoid; display: none; }
            
            table { page-break-inside: auto; margin-top: 10px; width: 100%; }
            table tr { page-break-inside: avoid; }
            table thead { display: table-header-group; background-color: #1e3a8a; color: white; }
            table tbody { display: table-row-group; }
            table th { padding: 8px; font-size: 11px; }
            table td { padding: 6px 8px; font-size: 10px; }
            
            .summary-footer { display: none !important; }
            
            /* Ensure proper page layout */
            body > * {
                margin: 0;
            }
        }
    </style>
</head>
<body>

{{-- Print Controls --}}
<div class="no-print btn-group-print">
    <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">← Back to Students</a>
    <button class="btn btn-primary btn-sm" onclick="window.print()"><i class="bi bi-printer"></i> Print This Page</button>
</div>



{{-- School Header --}}
<div class="header">
    <img src="{{ asset('images/snhs-logo.png') }}" alt="SNHS Logo" class="school-logo">
    <h1 class="school-name">Subic National High School</h1>
    <p class="school-address">Mangan-vaca, Subic, Zambales</p>
    <h2 class="report-title">List of Students Report</h2>
</div>

{{-- Report Metadata --}}
<div class="report-meta">
    <div>
        <strong>Total Students:</strong> {{ count($students) }}
    </div>
    <div>
        <strong>Report Date:</strong> {{ now()->format('M d, Y') }}
    </div>
    <div>
        <strong>Time:</strong> <span id="current-time"></span>
    </div>
</div>

{{-- Students Table --}}
<table>
    <thead>
        <tr>
            <th style="width: 40px;">#</th>
            <th style="width: 180px;">Name</th>
            <th style="width: 80px;">Grade</th>
            <th style="width: 100px;">Section</th>
            <th style="width: 100px;">Strand</th>
            <th style="width: 120px;">LRN</th>
            <th style="width: 100px;">Phone</th>
            <th style="width: 180px;">Address</th>
        </tr>
    </thead>
    <tbody>
    @forelse($students as $i => $student)
        @php
            $gradeDisplay = '-';
            $sectionDisplay = '-';
            $strandDisplay = '-';
            $knownStrands = ['ABM','GAS','STEM','HUMSS','ICT','TVL'];
            $gsRaw = trim($student->grade_section ?? '');
            if (empty($gsRaw)) {
                $gsRaw = trim($student->grade ?? '') ?: trim($student->year_level ?? '') ?: '';
            }
            if ($gsRaw !== '') {
                $gs = preg_replace('/[\-\/_,]+/', ' ', $gsRaw);
                $gs = preg_replace('/\s+/', ' ', $gs);
                $gs = trim($gs);
                $g = null;
                if (preg_match('/\b(7|8|9|10|11|12)\b/', $gs, $m)) {
                    $g = $m[1];
                } elseif (preg_match('/^\s*(\d{1,2})\b/', $gs, $m)) {
                    $g = $m[1];
                }
                $strandFound = null;
                foreach ($knownStrands as $st) {
                    if (preg_match('/\b' . preg_quote($st, '/') . '\b/i', $gs)) { $strandFound = strtoupper($st); break; }
                }
                $tmp = $gs;
                if (!empty($g)) {
                    $tmp = preg_replace('/\b' . preg_quote($g, '/') . '\b/', '', $tmp);
                }
                if (!empty($strandFound)) {
                    $tmp = preg_replace('/\b' . preg_quote($strandFound, '/') . '\b/i', '', $tmp);
                }
                $tmp = preg_replace('/\s+/', ' ', trim($tmp));
                $gradeDisplay = $g ?? '-';
                $sectionDisplay = $tmp !== '' ? $tmp : '-';
                $strandDisplay = $strandFound ?? '-';
            }
            $gradeDisplay = $gradeDisplay ?: '-';
            $sectionDisplay = $sectionDisplay ?: '-';
            $strandDisplay = $strandDisplay ?: '-';
        @endphp
        <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            <td><strong>{{ $student->last_name }}, {{ $student->first_name }}</strong></td>
            <td class="text-center">{{ $gradeDisplay }}</td>
            <td>{{ $sectionDisplay }}</td>
            <td>{{ $strandDisplay }}</td>
            <td>{{ $student->lrn ?? '-' }}</td>
            <td>{{ $student->phone_number ?? '-' }}</td>
            <td>{{ Str::limit($student->address, 30) ?? '-' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="text-center" style="padding: 20px;">No students found in the system.</td>
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
