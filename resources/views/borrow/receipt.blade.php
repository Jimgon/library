<!DOCTYPE html>
<html>
<head>
    <title>Borrow Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; }
        .center { text-align: center; }
        .print-btn { margin-top: 20px; text-align: center; }
    </style>
</head>
<body>
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

        $student = $borrow->user;
    @endphp

    <h2>Subic National High School Library Borrow Receipt</h2>

    <h4>Student Details</h4>
    <p><strong>Name:</strong> {{ $student?->first_name ?? 'N/A' }} {{ $student?->last_name ?? '' }}</p>
    <p><strong>Grade & Section:</strong> {{ $student?->grade_section ?? 'N/A' }}</p>
    <p><strong>LRN:</strong> {{ $student?->lrn ?? 'N/A' }}</p>
    <p><strong>Phone:</strong> {{ $student?->phone_number ?? 'N/A' }}</p>
    <p><strong>Address:</strong> {{ $student?->address ?? 'N/A' }}</p>

    <h4>Book Details</h4>
    <p><strong>Book:</strong> {{ $borrow->book?->title ?? 'Book not found' }}</p>

    <table>
        <tr>
            <th>Borrowed At</th>
            <td>{{ $borrowedAt ? $borrowedAt->format('F j, Y') : 'N/A' }}</td>
        </tr>
        <tr>
            <th>Due Date</th>
            <td>{{ $dueDate ? $dueDate->format('F j, Y') : 'N/A' }}</td>
        </tr>
        <tr>
            <th>Overdue Days</th>
            <td>{{ $overdueDays }}</td>
        </tr>
        <tr>
            <th>Remarks</th>
            <td>{{ $remark }}</td>
        </tr>
        <tr>
            <th>Notes</th>
            <td>{{ $borrow->notes ?? 'No additional notes' }}</td>
        </tr>
    </table>

    <div class="print-btn">
        <button onclick="window.print()">Print Receipt</button>
    </div>
</body>
</html>
