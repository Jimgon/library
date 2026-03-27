@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h1 class="h2 mb-0">Teachers List</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('users.print-teacher') }}" target="_blank" class="btn btn-outline-secondary">
                <i class="bi bi-printer me-2"></i>Print All
            </a>
            <a href="{{ route('teachers.import.form') }}" class="btn btn-outline-secondary">
                <i class="bi bi-download me-2"></i>Import CSV
            </a>
            <a href="{{ route('teachers.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle me-2"></i>Add Teacher
            </a>
        </div>
    </div>

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Search Form --}}
    <form class="row g-3 mb-4" action="{{ route('teachers.index') }}" method="GET">
        <div class="col-md-12">
            <input class="form-control" type="search" name="search" value="{{ request('search') }}" placeholder="Search teachers by name, email..." onchange="this.form.submit()">
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0">Teachers Management</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 fw-semibold">Name</th>
                            <th class="border-0 fw-semibold">Email</th>
                            <th class="border-0 fw-semibold d-none d-md-table-cell">Gender</th>
                            <th class="border-0 fw-semibold d-none d-lg-table-cell">Address</th>
                            <th class="border-0 fw-semibold d-none d-lg-table-cell">Phone</th>
                            <th class="border-0 fw-semibold">Borrowed Books</th>
                            <th class="border-0 fw-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers as $teacher)
                            @php
                                $activeBorrows = $teacher->borrows->whereNull('returned_at');
                                $totalOverdue = 0;
                                $today = \Carbon\Carbon::today();
                                foreach($activeBorrows as $borrow) {
                                    $dueDate = $borrow->due_date;
                                    if ($dueDate && $today->gt($dueDate)) {
                                        $overdueDays = (int) ceil($today->diffInDays($dueDate));
                                        $totalOverdue += $overdueDays;
                                    }
                                }
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $teacher->name }}</div>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $teacher->email }}</small>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="badge bg-secondary">{{ ucfirst($teacher->gender) }}</span>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <small>{{ Str::limit($teacher->address, 30) }}</small>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <small>{{ $teacher->phone_number }}</small>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#borrowModal{{ $teacher->id }}">
                                        <i class="bi bi-book"></i> View Books
                                    </button>
                                </td>

                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('teachers.borrow-history', $teacher->id) }}" class="btn btn-sm btn-outline-dark" title="View Borrow History">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('teachers.edit', $teacher->id) }}" class="btn btn-sm btn-outline-dark" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if(Auth::user() && Auth::user()->role === 'admin')
                                        <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this teacher?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                                        No teachers found.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4 p-3">
                {{ $teachers->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    {{-- Borrowed Books Modal --}}
    @foreach($teachers as $teacher)
        <div class="modal fade" id="borrowModal{{ $teacher->id }}" tabindex="-1" aria-labelledby="borrowModalLabel{{ $teacher->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="borrowModalLabel{{ $teacher->id }}">
                            <i class="bi bi-book me-2"></i>All Borrowed Books
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h6 class="text-muted mb-3">Teacher: <strong>{{ $teacher->name }}</strong></h6>
                        @php $allBorrows = $teacher->borrows; @endphp
                        @if($allBorrows->count() > 0)
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>ISBN</th>
                                        <th>Borrowed At</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allBorrows as $borrow)
                                        @php
                                            $bookTitle = $borrow->book ? $borrow->book->title : 'Book not found';
                                            $bookAuthor = $borrow->book ? ($borrow->book->author ?? 'N/A') : 'N/A';
                                            $bookIsbn = $borrow->book ? ($borrow->book->isbn ?? 'N/A') : 'N/A';
                                            $borrowedAt = $borrow->borrowed_at ? \Carbon\Carbon::parse($borrow->borrowed_at)->format('M d, Y') : 'N/A';
                                            $dueDate = $borrow->due_date ? \Carbon\Carbon::parse($borrow->due_date)->format('M d, Y') : 'N/A';
                                            $status = $borrow->returned_at ? 'Returned' : 'Active';
                                        @endphp
                                        <tr>
                                            <td>{{ $bookTitle }}</td>
                                            <td>{{ $bookAuthor }}</td>
                                            <td>{{ $bookIsbn }}</td>
                                            <td>{{ $borrowedAt }}</td>
                                            <td>{{ $dueDate }}</td>
                                            <td>{{ $status }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-muted">No books borrowed.</div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

