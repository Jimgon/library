@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-0">Return Borrowed Books</h4>
        </div>
    </div>

    {{-- Success Notification --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Error Notification --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <style>
        @media print {
            .container-fluid > div:first-child,
            .mb-4,
            .bg-light,
            #returnSelectedBtn,
            #clearSelectionBtn,
            .modal,
            .modal-backdrop,
            input[type="checkbox"],
            .no-print {
                display: none !important;
            }
            .table {
                font-size: 11px;
            }
            .table th, .table td {
                padding: 6px 8px;
            }
            .btn {
                display: none !important;
            }
            a.btn {
                display: none !important;
            }
            body {
                margin: 0;
                padding: 10px;
                background: white;
            }
        }
    </style>

    {{-- Tabs for Student/Teacher Returns --}}
    <ul class="nav nav-tabs mb-4" id="returnTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="student-return-tab" data-bs-toggle="tab" data-bs-target="#student-returns" type="button" role="tab" aria-controls="student-returns" aria-selected="true">
                Student Returns
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="teacher-return-tab" data-bs-toggle="tab" data-bs-target="#teacher-returns" type="button" role="tab" aria-controls="teacher-returns" aria-selected="false">
                Teacher Returns
            </button>
        </li>
    </ul>

    <div class="tab-content" id="returnTabContent">
        {{-- Student Returns Tab --}}
        <div class="tab-pane fade show active" id="student-returns" role="tabpanel" aria-labelledby="student-return-tab">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Student Pending Returns</h5>
                </div>
                {{-- Student Returns Filters --}}
                <div class="p-2 bg-light border-bottom d-flex justify-content-between align-items-center gap-2">
                    <input type="search" class="form-control form-control-sm student-search" placeholder="Search borrower, book, or control #..." style="max-width: 300px;" aria-label="Search student returns">
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-primary student-filter-btn" data-filter="all">All</button>
                        <button class="btn btn-sm btn-outline-dark student-filter-btn" data-filter="personal">Personal</button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th class="border-0 fw-semibold" style="width: 40px;">
                    <input type="checkbox" id="selectAllCheckboxStudent" class="form-check-input" aria-label="Select all">
                </th>
                <th class="border-0 fw-semibold">Borrower</th>
                <th class="border-0 fw-semibold">Book</th>
                <th class="border-0 fw-semibold d-none d-lg-table-cell">Book Source</th>
                <th class="border-0 fw-semibold d-none d-md-table-cell">Borrow Date</th>
                <th class="border-0 fw-semibold d-none d-lg-table-cell">Due Date</th>
                <th class="border-0 fw-semibold">Control #</th>
                <th class="border-0 fw-semibold">Status</th>
                <th class="border-0 fw-semibold">Remarks</th>
                <th class="border-0 fw-semibold text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
        @php
            // Separate student and teacher borrows
            // Include NULL roles as students (for legacy borrow records)
            $studentBorrows = $borrows->filter(function($borrow) {
                return $borrow->role !== 'teacher';
            });
            $teacherBorrows = $borrows->where('role', 'teacher');
            
            // Group student borrows by user_id, book_id, borrowed_at date, AND origin
            $groupedStudents = $studentBorrows->groupBy(function($borrow) {
                $borrowDate = $borrow->borrowed_at ? \Carbon\Carbon::parse($borrow->borrowed_at)->format('Y-m-d') : 'unknown';
                return $borrow->user_id . '|' . $borrow->book_id . '|' . $borrowDate . '|' . ($borrow->origin ?? 'personal');
            })->map(function($group) {
                return [
                    'borrows' => $group,
                    'count' => $group->count(),
                    'firstBorrow' => $group->first()
                ];
            })->filter(function($transaction) {
                return $transaction['borrows']->whereNull('returned_at')->count() > 0;
            });
            
            $grouped = $groupedStudents;
        @endphp
        
        @forelse($grouped as $transaction)
            @php
                // Only show unreturned borrows in this transaction
                $unreturned = $transaction['borrows']->whereNull('returned_at');
                $borrow = $unreturned->first();
                $quantity = $unreturned->count();
                
                // Skip if no unreturned borrows
                if (!$borrow) continue;
                
                // Use borrowed_at if available, otherwise use created_at as fallback
                $borrowedAt = null;
                if ($borrow->borrowed_at) {
                    $borrowedAt = \Carbon\Carbon::parse($borrow->borrowed_at);
                } elseif ($borrow->created_at) {
                    $borrowedAt = \Carbon\Carbon::parse($borrow->created_at);
                }
                
                // Use due_date if available, otherwise calculate from borrowed_at
                $dueDate = null;
                if ($borrow->due_date) {
                    $dueDate = \Carbon\Carbon::parse($borrow->due_date);
                } elseif ($borrowedAt) {
                    // Check if this is a distribution book to determine default duration
                    $isDistribution = $borrow->book ? false : \App\Models\DistributedBook::find($borrow->book_id);
                    $dueDate = $isDistribution ? $borrowedAt->addMonths(12) : $borrowedAt->addDays(14);
                }
                
                $today = \Carbon\Carbon::today();

                $overdueDays = 0;
                $computedRemark = 'No Remarks';
                if ($dueDate && $today->gt($dueDate)) {
                    $overdueDays = $today->diffInDays($dueDate);
                    $computedRemark = "{$overdueDays} day(s) overdue";
                }

                $student = $borrow->user;
                $remark = !empty($borrow->remark) ? $borrow->remark : $computedRemark;

                $lower = strtolower($remark);
                // Red for overdue, lost, damage; Green for everything else
                if (str_contains($lower, 'overdue') || $lower === 'lost' || $lower === 'damage') {
                    $badgeClass = 'bg-danger';
                } else {
                    $badgeClass = 'bg-success';
                }
            @endphp

            <tr class="borrow-row" data-origin="{{ $borrow->origin ?? 'personal' }}">
                <td>
                    <input type="checkbox" class="borrow-checkbox form-check-input" data-borrow-id="{{ $borrow->id }}" data-quantity="{{ $quantity }}" aria-label="Select this transaction">
                </td>
                <td>
                    @php
                        $borrower = \App\Models\User::find($borrow->user_id);
                    @endphp
                    @if($borrower)
                        {{ $borrower->name ?? (($borrower->first_name ?? 'Unknown') . ' ' . ($borrower->last_name ?? '')) }}
                    @else
                        Unknown
                    @endif
                </td>
                <td>
                    @php
                        $bookTitle = 'Book not found';
                        $bookSource = '';
            
                        if ($borrow->book) {
                            $bookTitle = $borrow->book->title;
                            $bookSource = ($borrow->origin ?? '') === 'distribution' ? 'Distribution' : 'Personal';
                        } else {
                            $distBook = \App\Models\DistributedBook::find($borrow->book_id);
                            if ($distBook) {
                                $bookTitle = $distBook->title;
                                $bookSource = 'Distribution';
                            }
                        }
                    @endphp
                    {{ $bookTitle }}
                </td>
                <td class="d-none d-lg-table-cell"><small>{{ $bookSource }}</small></td>
                <td class="d-none d-md-table-cell"><small>{{ $borrowedAt ? $borrowedAt->format('Y-m-d') : 'N/A' }}</small></td>
                <td class="d-none d-lg-table-cell"><small>{{ $dueDate ? $dueDate->format('Y-m-d') : 'N/A' }}</small></td>

                {{-- Control # column --}}
                <td>
                    @if($quantity > 1)
                    <button type="button" class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#ctrlModal_{{ $borrow->id }}">
                        <i class="bi bi-list-check me-1"></i>Show ({{ $quantity }})
                    </button>
                    @else
                    {{-- For single copy, show control number directly --}}
                    @if($borrow->copy_number)
                        <span class="text-black"><span style="font-family: monospace;">Ctrl#: {{ $borrow->copy_number }}</span></span>
                    @else
                        <span class="text-muted">N/A</span>
                    @endif
                    @endif
                </td>

                {{-- Status & Remarks --}}
                <td>
                    @php
                        $statusClass = 'text-success';
                        $statusText = 'On Time';
                        
                        if ($dueDate && $today->gt($dueDate)) {
                            $statusClass = 'text-danger';
                            $statusText = 'Overdue';
                        }
                    @endphp
                    <span class="{{ $statusClass }} fw-semibold">{{ $statusText }}</span>
                </td>

                {{-- Remarks Column --}}
                <td>
                    @php $selected = old('remark', $borrow->remark ?? ''); @endphp
                    <select name="remark" class="form-select form-select-sm remark-select" aria-label="Set remark">
                        <option value="No Remarks" {{ $selected === 'No Remarks' ? 'selected' : '' }}>No Remarks</option>
                        <option value="On Time" {{ $selected === 'On Time' ? 'selected' : '' }}>On Time</option>
                        <option value="Late Return" {{ $selected === 'Late Return' ? 'selected' : '' }}>Late Return</option>
                        <option value="Lost" {{ $selected === 'Lost' ? 'selected' : '' }}>Lost</option>
                        <option value="Damage" {{ $selected === 'Damage' ? 'selected' : '' }}>Damage</option>
                    </select>
                </td>

                {{-- Actions --}}
                <td class="text-center">
                    <form action="{{ route('borrow.return.process', $borrow->id) }}" method="POST" class="d-flex gap-1 justify-content-center flex-wrap return-form" data-quantity="{{ $quantity }}">
                        @csrf
                        
                        {{-- Hidden checkboxes for form submission (synced with modal) --}}
                        <div style="display: none;">
                            @php $ctrlIndex = 0; @endphp
                            @foreach($unreturned as $b)
                                <input type="checkbox" class="borrow-id-checkbox" name="borrow_ids[]" value="{{ $b->id }}" checked>
                                @php $ctrlIndex++; @endphp
                            @endforeach
                        </div>
                        
                        {{-- Hidden input for quantity being returned --}}
                        <input type="hidden" name="quantity_returned" class="quantity-returned-input" value="{{ $quantity }}">
                        <input type="hidden" name="remark" class="remark-hidden-input" value="{{ $selected }}">
                        
                        <button type="submit" class="btn btn-sm btn-success return-btn" title="Process return">
                            <i class="bi bi-check-circle me-1"></i>Return
                        </button>
                        <a href="{{ route('borrow.receipt', $borrow->id) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Print receipt">
                            <i class="bi bi-printer me-1"></i>Print
                        </a>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center py-4">
                    <div class="text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        No student books to return.
                    </div>
                </td>
            </tr>
        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center p-3 border-top">
            <div>
                <button id="clearSelectionBtnStudent" type="button" class="btn btn-outline-secondary" style="display: none;">
                    <i class="bi bi-x-circle me-1"></i>Clear Selection
                </button>
            </div>
            <div>
                <button id="returnSelectedBtnStudent" type="button" class="btn btn-success" style="display: none;">
                    <i class="bi bi-check-circle me-1"></i>Return Selected (<span id="selectedCountStudent">0</span>)
                </button>
            </div>
        </div>
            </div>
        </div>
        </div>

        {{-- Teacher Returns Tab --}}
        <div class="tab-pane fade" id="teacher-returns" role="tabpanel" aria-labelledby="teacher-return-tab">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Teacher Pending Returns</h5>
                </div>
                {{-- Teacher Returns Filters --}}
                <div class="p-2 bg-light border-bottom d-flex justify-content-between align-items-center gap-2">
                    <input type="search" class="form-control form-control-sm teacher-search" placeholder="Search borrower, book, or control #..." style="max-width: 300px;" aria-label="Search teacher returns">
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-primary teacher-filter-btn" data-filter="all">All</button>
                        <button class="btn btn-sm btn-outline-dark teacher-filter-btn" data-filter="personal">Personal</button>
                        <button class="btn btn-sm btn-outline-dark teacher-filter-btn" data-filter="distribution">Distribution</button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th class="border-0 fw-semibold" style="width: 40px;">
                    <input type="checkbox" id="selectAllCheckboxTeacher" class="form-check-input" aria-label="Select all">
                </th>
                <th class="border-0 fw-semibold">Borrower</th>
                <th class="border-0 fw-semibold">Book</th>
                <th class="border-0 fw-semibold d-none d-lg-table-cell">Book Source</th>
                <th class="border-0 fw-semibold d-none d-md-table-cell">Borrow Date</th>
                <th class="border-0 fw-semibold d-none d-lg-table-cell">Due Date</th>
                <th class="border-0 fw-semibold">Control #</th>
                <th class="border-0 fw-semibold">Status</th>
                <th class="border-0 fw-semibold">Remarks</th>
                <th class="border-0 fw-semibold text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
        @php
            // Group teacher borrows by user_id, book_id, borrowed_at date, AND origin
            $groupedTeachers = $teacherBorrows->groupBy(function($borrow) {
                $borrowDate = $borrow->borrowed_at ? \Carbon\Carbon::parse($borrow->borrowed_at)->format('Y-m-d') : 'unknown';
                return $borrow->user_id . '|' . $borrow->book_id . '|' . $borrowDate . '|' . ($borrow->origin ?? 'personal');
            })->map(function($group) {
                return [
                    'borrows' => $group,
                    'count' => $group->count(),
                    'firstBorrow' => $group->first()
                ];
            })->filter(function($transaction) {
                return $transaction['borrows']->whereNull('returned_at')->count() > 0;
            });
            
            $grouped = $groupedTeachers;
        @endphp
        
        @forelse($grouped as $transaction)
            @php
                // Only show unreturned borrows in this transaction
                $unreturned = $transaction['borrows']->whereNull('returned_at');
                $borrow = $unreturned->first();
                $quantity = $unreturned->count();
                
                // Skip if no unreturned borrows
                if (!$borrow) continue;
                
                // Use borrowed_at if available, otherwise use created_at as fallback
                $borrowedAt = null;
                if ($borrow->borrowed_at) {
                    $borrowedAt = \Carbon\Carbon::parse($borrow->borrowed_at);
                } elseif ($borrow->created_at) {
                    $borrowedAt = \Carbon\Carbon::parse($borrow->created_at);
                }
                
                // Use due_date if available, otherwise calculate from borrowed_at
                $dueDate = null;
                if ($borrow->due_date) {
                    $dueDate = \Carbon\Carbon::parse($borrow->due_date);
                } elseif ($borrowedAt) {
                    // Teachers get 12 months to return
                    $dueDate = $borrowedAt->addMonths(12);
                }
                
                $today = \Carbon\Carbon::today();

                $overdueDays = 0;
                $computedRemark = 'No Remarks';
                if ($dueDate && $today->gt($dueDate)) {
                    $overdueDays = $today->diffInDays($dueDate);
                    $computedRemark = "{$overdueDays} day(s) overdue";
                }

                $teacher = \App\Models\Teacher::find($borrow->user_id);
                $remark = !empty($borrow->remark) ? $borrow->remark : $computedRemark;

                $lower = strtolower($remark);
                // Red for overdue, lost, damage; Green for everything else
                if (str_contains($lower, 'overdue') || $lower === 'lost' || $lower === 'damage') {
                    $badgeClass = 'bg-danger';
                } else {
                    $badgeClass = 'bg-success';
                }
            @endphp

            <tr class="borrow-row-teacher" data-origin="{{ $borrow->origin ?? 'personal' }}">
                <td>
                    <input type="checkbox" class="borrow-checkbox-teacher form-check-input" data-borrow-id="{{ $borrow->id }}" data-quantity="{{ $quantity }}" aria-label="Select this transaction">
                </td>
                <td>
                    @if($teacher)
                        {{ $teacher->name ?? 'Unknown' }}
                    @else
                        Unknown
                    @endif
                </td>
                <td>
                    @php
                        $bookTitle = 'Book not found';
                        $bookSource = '';
            
                        if ($borrow->book) {
                            $bookTitle = $borrow->book->title;
                            $bookSource = ($borrow->origin ?? '') === 'distribution' ? 'Distribution' : 'Personal';
                        } else {
                            $distBook = \App\Models\DistributedBook::find($borrow->book_id);
                            if ($distBook) {
                                $bookTitle = $distBook->title;
                                $bookSource = 'Distribution';
                            }
                        }
                    @endphp
                    {{ $bookTitle }}
                </td>
                <td class="d-none d-lg-table-cell"><small>{{ $bookSource }}</small></td>
                <td class="d-none d-md-table-cell"><small>{{ $borrowedAt ? $borrowedAt->format('Y-m-d') : 'N/A' }}</small></td>
                <td class="d-none d-lg-table-cell"><small>{{ $dueDate ? $dueDate->format('Y-m-d') : 'N/A' }}</small></td>

                {{-- Control # column --}}
                <td>
                    @if($quantity > 1)
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#ctrlModal_{{ $borrow->id }}">
                            View ({{ $quantity }})
                        </button>
                    @else
                        <span class="badge bg-light text-dark">{{ $borrow->copy_number ?? 'N/A' }}</span>
                    @endif
                </td>

                {{-- Status & Remarks --}}
                <td>
                    <span class="badge {{ $badgeClass }}">
                        @if(str_contains(strtolower($remark), 'overdue'))
                            Overdue
                        @elseif($remark === 'Lost')
                            Lost
                        @elseif($remark === 'Damage')
                            Damaged
                        @else
                            Normal
                        @endif
                    </span>
                </td>

                {{-- Remarks Column --}}
                <td>
                    <span class="text-muted small">{{ $remark }}</span>
                </td>

                {{-- Actions --}}
                <td class="text-center">
                    <form action="{{ route('borrow.return.process', $borrow->id) }}" method="POST" class="d-flex gap-1 justify-content-center flex-wrap return-form" data-quantity="{{ $quantity }}">
                        @csrf
                        
                        {{-- Hidden checkboxes for form submission (synced with modal) --}}
                        <div style="display: none;">
                            @php $ctrlIndex = 0; @endphp
                            @foreach($unreturned as $b)
                                <input type="checkbox" class="borrow-id-checkbox" name="borrow_ids[]" value="{{ $b->id }}" checked>
                                @php $ctrlIndex++; @endphp
                            @endforeach
                        </div>
                        
                        {{-- Hidden input for quantity being returned --}}
                        <input type="hidden" name="quantity_returned" class="quantity-returned-input" value="{{ $quantity }}">
                        
                        {{-- Remark selector --}}
                        @php $selected = old('remark', $borrow->remark ?? ''); @endphp
                        <select name="remark" class="form-select form-select-sm remark-select" aria-label="Set remark" style="width: auto;">
                            <option value="No Remarks" {{ $selected === 'No Remarks' ? 'selected' : '' }}>No Remarks</option>
                            <option value="On Time" {{ $selected === 'On Time' ? 'selected' : '' }}>On Time</option>
                            <option value="Late Return" {{ $selected === 'Late Return' ? 'selected' : '' }}>Late Return</option>
                            <option value="Lost" {{ $selected === 'Lost' ? 'selected' : '' }}>Lost</option>
                            <option value="Damage" {{ $selected === 'Damage' ? 'selected' : '' }}>Damage</option>
                        </select>
                        
                        <button type="submit" class="btn btn-sm btn-success return-btn" title="Process return">
                            <i class="bi bi-check-circle me-1"></i>Return
                        </button>
                        <a href="{{ route('borrow.receipt', $borrow->id) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Print receipt">
                            <i class="bi bi-printer me-1"></i>Print
                        </a>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center py-4">
                    <div class="text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        No teacher books to return.
                    </div>
                </td>
            </tr>
        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center p-3 border-top">
            <div>
                <button id="clearSelectionBtnTeacher" type="button" class="btn btn-outline-secondary" style="display: none;">
                    <i class="bi bi-x-circle me-1"></i>Clear Selection
                </button>
            </div>
            <div>
                <button id="returnSelectedBtnTeacher" type="button" class="btn btn-success" style="display: none;">
                    <i class="bi bi-check-circle me-1"></i>Return Selected (<span id="selectedCountTeacher">0</span>)
                </button>
            </div>
        </div>
            </div>
        </div>
        </div>
    </div>
    {{-- Control Numbers Modals for Both Student and Teacher Borrows --}}
    @php
        // Collect all borrows from both student and teacher collections
        $allBorrows = $borrows->all();
    @endphp
    
    @foreach($allBorrows as $borrow)
        @php
            // Get the quantity of unreturned borrows with same user, book, and date
            $borrowDate = $borrow->borrowed_at ? \Carbon\Carbon::parse($borrow->borrowed_at)->format('Y-m-d') : 'unknown';
            $similarBorrows = collect($allBorrows)->filter(function($b) use ($borrow, $borrowDate) {
                $bDate = $b->borrowed_at ? \Carbon\Carbon::parse($b->borrowed_at)->format('Y-m-d') : 'unknown';
                return $b->user_id === $borrow->user_id 
                    && $b->book_id === $borrow->book_id 
                    && $bDate === $borrowDate
                    && is_null($b->returned_at);
            });
            $quantity = $similarBorrows->count();
        @endphp
        
        @if($quantity > 1)
        <div class="modal fade" id="ctrlModal_{{ $borrow->id }}" tabindex="-1" aria-labelledby="ctrlModalLabel_{{ $borrow->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ctrlModalLabel_{{ $borrow->id }}">
                            <i class="bi bi-list-check me-2"></i>Select Control Numbers
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @foreach($similarBorrows as $b)
                            @php
                                $ctrlNum = $b->copy_number ?? 'N/A';
                            @endphp
                            <div class="card mb-3">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-start gap-2 mb-3">
                                        <input class="form-check-input borrow-id-checkbox modal-checkbox mt-1" type="checkbox" 
                                               name="borrow_ids[]" value="{{ $b->id }}" id="borrow_{{ $b->id }}" checked>
                                        <div class="grow">
                                            <label for="borrow_{{ $b->id }}" class="form-check-label fw-semibold mb-2 d-block">
                                                <span class="badge bg-primary">Ctrl#: {{ $ctrlNum }}</span>
                                            </label>
                                        </div>
                                    </div>
                                    <select class="form-select form-select-sm modal-remark-input" 
                                            data-borrow-id="{{ $b->id }}">
                                        <option value="No Remarks">No Remarks</option>
                                        <option value="On Time">On Time</option>
                                        <option value="Late Return">Late Return</option>
                                        <option value="Lost">Lost</option>
                                        <option value="Damage">Damage</option>
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-primary confirm-modal" data-modal-id="ctrlModal_{{ $borrow->id }}">
                            <i class="bi bi-check-circle me-1"></i>Confirm
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endforeach

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize checkboxes and button handlers for both tabs
            const tabs = [
                {
                    type: 'student',
                    checkboxSelector: 'input.borrow-checkbox',
                    rowSelector: 'tr.borrow-row',
                    selectAllId: 'selectAllCheckboxStudent',
                    clearBtnId: 'clearSelectionBtnStudent',
                    returnBtnId: 'returnSelectedBtnStudent',
                    countId: 'selectedCountStudent'
                },
                {
                    type: 'teacher',
                    checkboxSelector: 'input.borrow-checkbox-teacher',
                    rowSelector: 'tr.borrow-row-teacher',
                    selectAllId: 'selectAllCheckboxTeacher',
                    clearBtnId: 'clearSelectionBtnTeacher',
                    returnBtnId: 'returnSelectedBtnTeacher',
                    countId: 'selectedCountTeacher'
                }
            ];
            
            tabs.forEach(tabConfig => {
                const selectAllCheckbox = document.getElementById(tabConfig.selectAllId);
                const clearBtn = document.getElementById(tabConfig.clearBtnId);
                const returnBtn = document.getElementById(tabConfig.returnBtnId);
                const countSpan = document.getElementById(tabConfig.countId);
                
                if (!selectAllCheckbox) return;
                
                const rows = Array.from(document.querySelectorAll(tabConfig.rowSelector));
                const transactionCheckboxes = Array.from(document.querySelectorAll(tabConfig.rowSelector + ' ' + tabConfig.checkboxSelector));
                
                function updateCount() {
                    const checked = transactionCheckboxes.filter(cb => cb.checked).length;
                    if (countSpan) countSpan.textContent = checked;
                    if (checked > 0) {
                        if (clearBtn) clearBtn.style.display = 'inline-block';
                        if (returnBtn) returnBtn.style.display = 'inline-block';
                    } else {
                        if (clearBtn) clearBtn.style.display = 'none';
                        if (returnBtn) returnBtn.style.display = 'none';
                    }
                }
                
                // Select all checkbox
                selectAllCheckbox.addEventListener('change', function() {
                    transactionCheckboxes.forEach(cb => cb.checked = this.checked);
                    updateCount();
                });
                
                // Individual checkboxes
                transactionCheckboxes.forEach(cb => {
                    cb.addEventListener('change', updateCount);
                });
                
                // Clear button
                clearBtn?.addEventListener('click', function() {
                    selectAllCheckbox.checked = false;
                    transactionCheckboxes.forEach(cb => cb.checked = false);
                    updateCount();
                });
                
                // Return Selected button - submit all selected row forms in sequence
                returnBtn?.addEventListener('click', function() {
                    const checkedRows = rows.filter(row => {
                        const checkbox = row.querySelector(tabConfig.checkboxSelector);
                        return checkbox && checkbox.checked;
                    });
                    
                    if (checkedRows.length === 0) {
                        alert('Please select at least one item to return');
                        return;
                    }
                    
                    if (!confirm('Are you sure you want to return the selected items?')) {
                        return;
                    }
                    
                    // Get all the forms from checked rows
                    const forms = checkedRows.map(row => row.querySelector('.return-form')).filter(form => form);
                    
                    if (forms.length === 0) {
                        alert('No forms found for selected items');
                        return;
                    }
                    
                    // Submit the first form, then chain the rest
                    let currentFormIndex = 0;
                    
                    const submitNext = () => {
                        if (currentFormIndex < forms.length) {
                            const form = forms[currentFormIndex];
                            currentFormIndex++;
                            
                            // Update remarks before submitting
                            const remarkSelect = form.closest('tr').querySelector('.remark-select');
                            if (remarkSelect) {
                                const remarkInput = form.querySelector('.remark-hidden-input');
                                if (remarkInput) {
                                    remarkInput.value = remarkSelect.value;
                                }
                            }
                            
                            // Submit via fetch to avoid page reload until last form
                            const formData = new FormData(form);
                            fetch(form.action, {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => {
                                if (response.ok) {
                                    submitNext();
                                } else {
                                    alert('Error submitting return. Please try again.');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Error submitting return. Please try again.');
                            });
                        } else {
                            // All forms submitted, reload page
                            window.location.reload();
                        }
                    };
                    
                    submitNext();
                });
            });

            // Tab-specific filtering
            const studentFilterBtns = document.querySelectorAll('.student-filter-btn');
            const teacherFilterBtns = document.querySelectorAll('.teacher-filter-btn');
            const studentSearchInput = document.querySelector('.student-search');
            const teacherSearchInput = document.querySelector('.teacher-search');
            let studentFilter = 'all';
            let teacherFilter = 'all';
            let studentQuery = '';
            let teacherQuery = '';

            const rowSearchCache = new WeakMap();

            const normalizeSearchText = (value) => {
                return (value ?? '')
                    .toString()
                    .toLowerCase()
                    .replace(/\s+/g, ' ')
                    .trim();
            };

            const getRowSearchText = (row) => {
                if (rowSearchCache.has(row)) return rowSearchCache.get(row);

                let text = row?.textContent ?? '';

                // Include control numbers from the modal (when a transaction has multiple copies).
                const modalTrigger = row?.querySelector('[data-bs-target^="#ctrlModal_"]');
                const modalSelector = modalTrigger?.getAttribute('data-bs-target');
                if (modalSelector) {
                    const modal = document.querySelector(modalSelector);
                    if (modal) {
                        text += ' ' + (modal.textContent ?? '');
                    }
                }

                const normalized = normalizeSearchText(text);
                rowSearchCache.set(row, normalized);
                return normalized;
            };

            const debounce = (fn, delayMs = 100) => {
                let timeoutId = null;
                return (...args) => {
                    if (timeoutId) clearTimeout(timeoutId);
                    timeoutId = setTimeout(() => fn(...args), delayMs);
                };
            };

            const applyStudentRowVisibility = () => {
                const term = normalizeSearchText(studentQuery);
                const studentRows = document.querySelectorAll('#student-returns tr.borrow-row');

                studentRows.forEach(row => {
                    const origin = row.dataset.origin || 'personal';
                    const matchesOrigin = (studentFilter === 'all' || studentFilter === origin);
                    const matchesSearch = (!term || getRowSearchText(row).includes(term));
                    row.style.display = (matchesOrigin && matchesSearch) ? '' : 'none';
                });
            };

            const applyTeacherRowVisibility = () => {
                const term = normalizeSearchText(teacherQuery);
                const teacherRows = document.querySelectorAll('#teacher-returns tr.borrow-row-teacher');

                teacherRows.forEach(row => {
                    const origin = row.dataset.origin || 'personal';
                    const matchesOrigin = (teacherFilter === 'all' || teacherFilter === origin);
                    const matchesSearch = (!term || getRowSearchText(row).includes(term));
                    row.style.display = (matchesOrigin && matchesSearch) ? '' : 'none';
                });
            };
            
            // Student filters
            studentFilterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    studentFilter = this.dataset.filter;
                    
                    studentFilterBtns.forEach(b => {
                        b.classList.toggle('btn-primary', b.dataset.filter === studentFilter);
                        b.classList.toggle('btn-outline-dark', b.dataset.filter !== studentFilter);
                    });

                    applyStudentRowVisibility();
                });
            });
            
            // Teacher filters
            teacherFilterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    teacherFilter = this.dataset.filter;
                    
                    teacherFilterBtns.forEach(b => {
                        b.classList.toggle('btn-primary', b.dataset.filter === teacherFilter);
                        b.classList.toggle('btn-outline-dark', b.dataset.filter !== teacherFilter);
                    });

                    applyTeacherRowVisibility();
                });
            });

            // Search bars (per tab)
            const onStudentSearch = debounce(() => {
                studentQuery = studentSearchInput?.value ?? '';
                applyStudentRowVisibility();
            }, 80);

            const onTeacherSearch = debounce(() => {
                teacherQuery = teacherSearchInput?.value ?? '';
                applyTeacherRowVisibility();
            }, 80);

            studentSearchInput?.addEventListener('input', onStudentSearch);
            teacherSearchInput?.addEventListener('input', onTeacherSearch);

            // Sync remarks for single form submissions
            document.querySelectorAll('.return-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    // Get the remark select from the row
                    const row = this.closest('tr');
                    const remarkSelect = row?.querySelector('.remark-select');
                    
                    if (remarkSelect) {
                        // Update the hidden input with the current dropdown value
                        const remarkInput = this.querySelector('.remark-hidden-input');
                        if (remarkInput) {
                            remarkInput.value = remarkSelect.value;
                        }
                    }
                });
            });
            
            // Handle modal confirm buttons
            document.querySelectorAll('.confirm-modal').forEach(confirmBtn => {
                confirmBtn.addEventListener('click', function() {
                    const modalId = this.dataset.modalId;
                    const modal = document.getElementById(modalId);
                    
                    if (!modal) return;
                    
                    // Get all checkboxes in this modal
                    const allModalCheckboxes = Array.from(modal.querySelectorAll('.modal-checkbox'));
                    const checkedCheckboxes = allModalCheckboxes.filter(cb => cb.checked);
                    
                    if (checkedCheckboxes.length === 0) {
                        alert('Please select at least one item');
                        return;
                    }
                    
                    // Find the table row that triggered this modal
                    // Look for any form that references this modal in the page
                    const rows = document.querySelectorAll('tr.borrow-row, tr.borrow-row-teacher');
                    let targetForm = null;
                    
                    rows.forEach(row => {
                        const button = row.querySelector('[data-bs-target="#' + modalId + '"]');
                        if (button) {
                            targetForm = row.querySelector('.return-form') || row.querySelector('form');
                        }
                    });
                    
                    if (targetForm) {
                        // Update the form's hidden checkboxes with only the checked ones
                        const hiddenCheckboxes = targetForm.querySelectorAll('.borrow-id-checkbox');
                        hiddenCheckboxes.forEach(hc => {
                            hc.checked = checkedCheckboxes.some(cb => cb.value === hc.value);
                        });
                        
                        // Update remarks for each checked item
                        checkedCheckboxes.forEach(checkbox => {
                            const borrowId = checkbox.value;
                            const remarkSelect = modal.querySelector(`.modal-remark-input[data-borrow-id="${borrowId}"]`);
                            if (remarkSelect) {
                                // Store the remark value in a data attribute for later use
                                checkbox.dataset.remark = remarkSelect.value;
                            }
                        });
                        
                        // Capture remarks and auto-submit the form
                        const remarkSelects = modal.querySelectorAll('.modal-remark-input');
                        let firstRemark = 'No Remarks';
                        if (remarkSelects.length > 0) {
                            firstRemark = remarkSelects[0].value;
                        }
                        
                        const remarkInput = targetForm.querySelector('.remark-hidden-input');
                        if (remarkInput) {
                            remarkInput.value = firstRemark;
                        }
                        
                        // Close the modal
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        if (bsModal) {
                            bsModal.hide();
                        }
                        
                        // Auto-submit the form after a short delay to allow modal to close
                        setTimeout(() => {
                            targetForm.submit();
                        }, 300);
                    }
                });
            });
        });
    </script>
</div>
@endsection
