@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h1 class="h2 mb-0">Teachers List</h1>
        <div class="gap-2 d-flex">
            <a href="{{ route('teachers.import.form') }}" class="btn btn-dark">
                <i class="bi bi-upload me-2"></i>Import Teachers
            </a>
            <a href="{{ route('teachers.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add New Teacher
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
                            <th class="border-0 fw-semibold">Current Borrowed Books</th>
                            <th class="border-0 fw-semibold">Remarks</th>
                            <th class="border-0 fw-semibold d-none d-xl-table-cell">Notes</th>
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
                                    @if($activeBorrows->count() > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($activeBorrows as $borrow)
                                                <button type="button" class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#borrowModal{{ $borrow->id }}">
                                                    <i class="bi bi-book"></i>
                                                </button>
                                            @endforeach
                                        </div>
                                        <small class="text-muted">{{ $activeBorrows->count() }} book(s)</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $displayRemark = $teacher->remark;
                                        if (!$displayRemark) {
                                            $displayRemark = $totalOverdue > 0 ? "{$totalOverdue} day(s) overdue" : 'No Remarks';
                                        }
                                        // Red if overdue, lost, damage, or Not Cleared; Green otherwise
                                        $lower = strtolower($teacher->remark ?? '');
                                        $remarkColor = ($totalOverdue > 0 || str_contains($lower, 'lost') || str_contains($lower, 'damage') || $teacher->remark === 'Not Cleared') ? 'text-danger' : 'text-success';
                                    @endphp

                                    <span class="fw-semibold {{ $remarkColor }}">
                                        {{ $displayRemark }}
                                    </span>
                                </td>

                                <td class="d-none d-xl-table-cell">
                                    @php
                                        // Get recent notes from returned books (last 30 days)
                                        $recentReturns = $teacher->borrows->whereNotNull('returned_at')
                                            ->where('returned_at', '>=', \Carbon\Carbon::now()->subDays(30))
                                            ->whereNotNull('notes')
                                            ->where('notes', '!=', '')
                                            ->sortByDesc('returned_at')
                                            ->take(1); // Show last 1 note
                                    @endphp

                                    @if($recentReturns->count() > 0)
                                        <div>
                                            @foreach($recentReturns as $return)
                                                <div class="small text-muted">
                                                    <em>{{ Str::limit($return->notes, 40) }}</em>
                                                    <br><small>{{ $return->returned_at->format('M j') }}</small>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('teachers.edit', $teacher->id) }}" class="btn btn-sm btn-outline-dark" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this teacher?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
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

    {{-- Borrow Details Modals --}}
    @foreach($teachers as $teacher)
        @php
            $activeBorrows = $teacher->borrows->whereNull('returned_at');
        @endphp
        @foreach($activeBorrows as $borrow)
            @php
                // Determine book type and get book info
                $bookTitle = 'Book not found';
                $bookAuthor = 'N/A';
                $bookIsbn = 'N/A';
                $bookType = 'Regular';
                
                if ($borrow->book) {
                    $bookTitle = $borrow->book->title;
                    $bookAuthor = $borrow->book->author ?? 'N/A';
                    $bookIsbn = $borrow->book->isbn ?? 'N/A';
                } else {
                    $distBook = \App\Models\DistributedBook::find($borrow->book_id);
                    if ($distBook) {
                        $bookTitle = $distBook->title;
                        $bookAuthor = $distBook->author ?? 'N/A';
                        $bookIsbn = $distBook->isbn ?? 'N/A';
                        $bookType = 'Distribution';
                    }
                }
                
                // Get borrow dates
                $borrowedAt = null;
                $dueDate = null;
                
                if ($borrow->borrowed_at) {
                    $borrowedAt = \Carbon\Carbon::parse($borrow->borrowed_at);
                } elseif ($borrow->created_at) {
                    $borrowedAt = \Carbon\Carbon::parse($borrow->created_at);
                }
                
                if ($borrow->due_date) {
                    $dueDate = \Carbon\Carbon::parse($borrow->due_date);
                } elseif ($borrowedAt) {
                    // Calculate default due date based on book type
                    $dueDate = $bookType === 'Distribution' ? $borrowedAt->clone()->addMonths(12) : $borrowedAt->clone()->addDays(14);
                }
                
                // Check if overdue
                $today = \Carbon\Carbon::today();
                $isOverdue = $dueDate && $today->gt($dueDate);
                $overdueDays = 0;
                if ($isOverdue) {
                    $overdueDays = (int) ceil($today->diffInDays($dueDate));
                }
            @endphp
            <div class="modal fade" id="borrowModal{{ $borrow->id }}" tabindex="-1" aria-labelledby="borrowModalLabel{{ $borrow->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="borrowModalLabel{{ $borrow->id }}">
                                <i class="bi bi-book me-2"></i>Borrow Details
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-3">Teacher Information</h6>
                                    <p class="mb-2"><strong>Name:</strong> {{ $teacher->name }}</p>
                                    <p class="mb-2"><strong>Email:</strong> {{ $teacher->email }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-3">Book Information</h6>
                                    <p class="mb-2"><strong>Title:</strong> {{ $bookTitle }}</p>
                                    <p class="mb-2"><strong>Author:</strong> {{ $bookAuthor }}</p>
                                    <p class="mb-2"><strong>ISBN:</strong> {{ $bookIsbn }}</p>
                                    <p class="mb-2"><strong>Type:</strong> <span class="badge {{ $bookType === 'Distribution' ? 'bg-info' : 'bg-secondary' }}">{{ $bookType }}</span></p>
                                </div>
                            </div>
                            <hr>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-3">Borrow Details</h6>
                                    <p class="mb-2"><strong>Borrowed At:</strong> {{ $borrowedAt ? $borrowedAt->format('M d, Y') : 'N/A' }}</p>
                                    <p class="mb-2"><strong>Due Date:</strong> {{ $dueDate ? $dueDate->format('M d, Y') : 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-3">Status</h6>
                                    <p class="mb-2">
                                        <strong>Status:</strong>
                                        @if($isOverdue)
                                            <span class="badge bg-danger">Overdue by {{ $overdueDays }} day(s)</span>
                                        @else
                                            <span class="badge bg-success">On Time</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach
</div>
@endsection

