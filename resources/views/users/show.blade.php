@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>User Details: {{ $user->first_name }} {{ $user->last_name }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> {{ $user->first_name }} {{ $user->last_name }}</p>
                        <p><strong>Grade & Section:</strong> {{ $user->grade_section ?? '-' }}</p>
                        <p><strong>LRN:</strong> {{ $user->lrn ?? '-' }}</p>
                        <p><strong>Phone:</strong> {{ $user->phone_number ?? '-' }}</p>
                        <p><strong>Address:</strong> {{ $user->address ?? '-' }}</p>
                        <p><strong>Total Books Borrowed:</strong> {{ $user->borrows->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5>Borrowing History</h5>
            </div>
            <div class="card-body">
                @if($user->borrows->count() > 0)
                @php
                    $today = \Carbon\Carbon::today();
                    // Penalty removed — using remarks instead
                @endphp
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Book Title</th>
                                <th>Author</th>
                                <th>Borrow Date</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->borrows as $borrow)
                                @php
                                    $borrowDate = $borrow->borrowed_at;
                                    $dueDate    = $borrow->due_date;

                                    // Overdue days only if today is after due date
                                    $overdueDays = 0;
                                    if ($dueDate && $today->gt($dueDate)) {
                                        $overdueDays = (int) ceil($today->diffInDays($dueDate));
                                    }

                                    $penalty = 0;
                                    // Prefer stored admin remark if present
                                    if (!empty($borrow->remark)) {
                                        $remark = $borrow->remark;
                                    } else {
                                        $remark = $overdueDays > 0 ? "{$overdueDays} day(s) overdue" : 'Good Standing';
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $borrow->book?->title ?? 'Book not found' }}</td>
                                    <td>{{ $borrow->book?->author ?? '-' }}</td>
                                    <td>{{ $borrowDate ? \Carbon\Carbon::parse($borrowDate)->format('F j, Y') : '-' }}</td>
                                    <td>{{ $dueDate ? \Carbon\Carbon::parse($dueDate)->format('F j, Y') : '-' }}</td>
                                    <td>
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
                </div>
                @else
                <p class="text-muted">No borrowing history found.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


