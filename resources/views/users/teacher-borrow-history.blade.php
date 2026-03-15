@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="{{ route('teachers.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Teachers
        </a>
        <h1 class="h3 mb-0">Borrow History - {{ $teacher->name }}</h1>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>Name:</strong> {{ $teacher->name }}
                            </p>
                            <p class="mb-2">
                                <strong>Email:</strong> {{ $teacher->email }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>Gender:</strong> {{ ucfirst($teacher->gender) }}
                            </p>
                            <p class="mb-2">
                                <strong>Phone:</strong> {{ $teacher->phone_number }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white text-black">
            <h5 class="mb-0">
                <i class="bi bi-book me-2"></i>All Borrow History
            </h5>
        </div>
        <div class="card-body p-0">
            @if($borrows->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 fw-semibold" style="width: 40px;">#</th>
                                <th class="border-0 fw-semibold">Book Title</th>
                                <th class="border-0 fw-semibold">Author</th>
                                <th class="border-0 fw-semibold">ISBN</th>
                                <th class="border-0 fw-semibold">Borrowed On</th>
                                <th class="border-0 fw-semibold">Due Date</th>
                                <th class="border-0 fw-semibold">Returned On</th>
                                <th class="border-0 fw-semibold">Status</th>
                                <th class="border-0 fw-semibold">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($borrows as $index => $borrow)
                                @php
                                    $bookTitle = $borrow->book ? $borrow->book->title : 'Book not found';
                                    $bookAuthor = $borrow->book ? ($borrow->book->author ?? 'N/A') : 'N/A';
                                    $bookIsbn = $borrow->book ? ($borrow->book->isbn ?? 'N/A') : 'N/A';
                                    $borrowedAt = $borrow->borrowed_at ? \Carbon\Carbon::parse($borrow->borrowed_at)->format('M d, Y') : 'N/A';
                                    $dueDate = $borrow->due_date ? \Carbon\Carbon::parse($borrow->due_date)->format('M d, Y') : 'N/A';
                                    $returnedAt = $borrow->returned_at ? \Carbon\Carbon::parse($borrow->returned_at)->format('M d, Y') : '-';
                                    $status = $borrow->returned_at ? 'Returned' : 'Active';
                                    $statusBadgeClass = $borrow->returned_at ? 'bg-success' : 'bg-warning';
                                    $remark = $borrow->remark ?? '-';
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $bookTitle }}</strong>
                                    </td>
                                    <td>{{ $bookAuthor }}</td>
                                    <td>
                                        <small class="text-muted">{{ $bookIsbn }}</small>
                                    </td>
                                    <td>{{ $borrowedAt }}</td>
                                    <td>{{ $dueDate }}</td>
                                    <td>{{ $returnedAt }}</td>
                                    <td>
                                        <span class="badge {{ $statusBadgeClass }}">
                                            {{ $status }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $remarkLower = strtolower($remark);
                                            $remarkBadgeClass = 'bg-secondary';
                                            
                                            if (str_contains($remarkLower, 'lost')) {
                                                $remarkBadgeClass = 'bg-danger';
                                            } elseif (str_contains($remarkLower, 'damage') || str_contains($remarkLower, 'damaged')) {
                                                $remarkBadgeClass = 'bg-danger text-white';
                                            } elseif (str_contains($remarkLower, 'good') || $remark === '-') {
                                                $remarkBadgeClass = 'bg-success';
                                            }
                                        @endphp
                                        @if($remark !== '-')
                                            <span class="badge {{ $remarkBadgeClass }}">
                                                {{ $remark }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-light">
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-0">
                                <strong>Total Borrowed:</strong> {{ $borrows->count() }}
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-0">
                                <strong>Active:</strong> {{ $borrows->whereNull('returned_at')->count() }}
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-0">
                                <strong>Returned:</strong> {{ $borrows->whereNotNull('returned_at')->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <p>No borrow history found for this teacher.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
