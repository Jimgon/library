@extends('layouts.app')

@section('content')
<div class="container">
        @if($nearDueBorrows->count() > 0)
            <div class="alert alert-warning mb-4">
                <strong>⚠️ Upcoming Due Dates:</strong><br>
                The following users have books due within 3 days:<br>
                <ul class="mb-0">
                    @foreach($nearDueBorrows as $borrow)
                        <li>
                            <strong>{{ $borrow->user->first_name }} {{ $borrow->user->last_name }}</strong> -
                            <span class="text-dark">{{ $borrow->book->title ?? 'Unknown Book' }}</span>
                            <span class="text-muted">(Due: {{ \Carbon\Carbon::parse($borrow->due_date)->format('M d, Y') }})</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    <!-- Top 3 Boxes (shadcn style) -->
    <div class="d-flex gap-4 mb-4" style="flex-wrap:wrap;">
        <div class="flex-fill min-w-0" style="min-width:200px;">
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.75rem;padding:1.5rem 1rem;box-shadow:0 1px 2px 0 #0001;">
                <div style="font-size:1.1rem;font-weight:600;color:#111;">Total Books</div>
                <div style="font-size:2rem;font-weight:700;color:#00000;">{{ $totalBooks }}</div>
            </div>
        </div>
        <div class="flex-fill min-w-0" style="min-width:200px;">
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.75rem;padding:1.5rem 1rem;box-shadow:0 1px 2px 0 #0001;">
                <div style="font-size:1.1rem;font-weight:600;color:#111;">Total Users</div>
                <div style="font-size:2rem;font-weight:700;color:#00000;">{{ $totalUsers }}</div>
            </div>
        </div>
        <div class="flex-fill min-w-0" style="min-width:200px;">
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.75rem;padding:1.5rem 1rem;box-shadow:0 1px 2px 0 #0001;">
                <div style="font-size:1.1rem;font-weight:600;color:#111;">Total Borrows</div>
                <div style="font-size:2rem;font-weight:700;color:#00000;">{{ $totalBorrows }}</div>
            </div>
        </div>
    </div>

    <!-- Row 1: Students + Chart (shadcn style) -->
    <div class="d-flex gap-4 mb-4" style="flex-wrap:wrap;">
        <!-- Students with Unreturned Books -->
        <div class="flex-fill min-w-0" style="min-width:340px;">
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.75rem;box-shadow:0 1px 2px 0 #0001;display:flex;flex-direction:column;height:100%;">
                <div style="border-bottom:1px solid #e5e7eb;padding:0.75rem 1.25rem;font-weight:600;font-size:1.05rem;">Students with Unreturned Books</div>
                <div style="padding:1rem;max-height:400px;overflow-y:auto;">
                    @if($studentsWithUnreturned->count() > 0)
                        <ul style="list-style:none;padding:0;margin-bottom:1rem;">
                            @foreach($studentsWithUnreturned as $student)
                                <li style="border-bottom:1px solid #f3f4f6;padding:0.75rem 0;">
                                    <div style="font-weight:600;color:#111;">{{ $student->first_name }} {{ $student->last_name }}</div>
                                    <ul style="margin:0.5rem 0 0 1rem;padding:0;list-style:disc;">
                                        @foreach($student->borrows->whereNull('returned_at')->sortBy(fn($borrow) => $borrow->book->title) as $borrow)
                                            @php
                                                $borrowedAt = null;
                                                if ($borrow->borrowed_at) {
                                                    $borrowedAt = \Carbon\Carbon::parse($borrow->borrowed_at);
                                                } elseif ($borrow->created_at) {
                                                    $borrowedAt = \Carbon\Carbon::parse($borrow->created_at);
                                                }
                                            @endphp
                                            <li style="font-size:0.97rem;">
                                                <span style="color:#00000;">📚 {{ $borrow->book->title ?? 'Unknown Book' }}</span>
                                                <span style="color:#6b7280;font-size:0.92em;">(Borrowed: {{ $borrowedAt ? $borrowedAt->format('M d, Y') : 'N/A' }})</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach
                        </ul>
                        <div class="d-flex justify-content-center">{{ $studentsWithUnreturned->links('pagination::bootstrap-5') }}</div>
                    @else
                        <p class="text-muted">All students have returned their books.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Most Borrowed Books Chart -->
        <div class="flex-fill min-w-0" style="min-width:340px;">
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.75rem;box-shadow:0 1px 2px 0 #0001;display:flex;flex-direction:column;height:100%;">
                <div style="border-bottom:1px solid #e5e7eb;padding:0.75rem 1.25rem;font-weight:600;font-size:1.05rem;">Most Borrowed Books</div>
                <div style="padding:1rem;">
                    <div style="height: 300px;">
                        <canvas id="mostBorrowedBooksChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Available Books (shadcn style) -->
    <div class="mt-4">
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.75rem;box-shadow:0 1px 2px 0 #0001;">
            <div style="border-bottom:1px solid #e5e7eb;padding:0.75rem 1.25rem;font-weight:600;font-size:1.05rem;">Available Books</div>
            <div style="padding:1rem;max-height:400px;overflow-y:auto;">
                @if($availableBooks->count() > 0)
                    <table style="width:100%;border-collapse:collapse;font-size:0.98rem;">
                        <thead>
                            <tr style="background:#f9fafb;">
                                <th style="padding:0.5rem 0.75rem;text-align:left;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">Title</th>
                                <th style="padding:0.5rem 0.75rem;text-align:left;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">Author</th>
                                <th style="padding:0.5rem 0.75rem;text-align:left;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">ISBN</th>
                                <th style="padding:0.5rem 0.75rem;text-align:left;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">Category</th>
                                <th style="padding:0.5rem 0.75rem;text-align:left;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">Available Copies</th>
                                <th style="padding:0.5rem 0.75rem;text-align:left;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">Total Copies</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($availableBooks as $book)
                                <tr style="border-bottom:1px solid #f3f4f6;">
                                    <td style="padding:0.5rem 0.75rem;">{{ $book->title }}</td>
                                    <td style="padding:0.5rem 0.75rem;">{{ $book->author }}</td>
                                    <td style="padding:0.5rem 0.75rem;">{{ $book->isbn }}</td>
                                    <td style="padding:0.5rem 0.75rem;">{{ $book->category }}</td>
                                    <td style="padding:0.5rem 0.75rem;">{{ $book->available_copies }}</td>
                                    <td style="padding:0.5rem 0.75rem;">{{ $book->copies }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-3">{{ $availableBooks->links('pagination::bootstrap-5') }}</div>
                @else
                    <p style="color:#6b7280;margin-bottom:0;">No books available.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@php
    $count = count($mostBorrowedBookData);
    $borrowedColors = array_fill(0, $count, '#1e3a8a');
@endphp

<script>
    const borrowedColors = @json($borrowedColors);
    const ctx = document.getElementById('mostBorrowedBooksChart') ? document.getElementById('mostBorrowedBooksChart').getContext('2d') : null;
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($mostBorrowedBookLabels),
                datasets: [{
                    label: 'Borrow Count',
                    data: @json($mostBorrowedBookData),
                    backgroundColor: borrowedColors,
                    borderColor: borrowedColors,
                    borderWidth: 1,
                    borderRadius: 8,
                    maxBarThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                    x: { ticks: { autoSkip: false } }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: function(context) { return context.dataset.label + ': ' + context.parsed.y; } } }
                }
            }
        });
    }
</script>
</div>
@endsection

