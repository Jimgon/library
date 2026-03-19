@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold">Lost & Damaged</h3>
            <p class="text-muted mb-0">Track items marked lost or damaged.</p>
        </div>

        <a href="{{ route('books.lost-damage') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </a>
    </div>


    {{-- Stats --}}
    <div class="row mb-4">

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Lost</p>
                    <h4 class="fw-bold">{{ $lostCount ?? 0 }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Damaged</p>
                    <h4 class="fw-bold">{{ $damagedCount ?? 0 }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Total</p>
                    <h4 class="fw-bold">{{ $totalCount ?? 0 }}</h4>
                </div>
            </div>
        </div>

    </div>


    {{-- Active lost & damaged --}}
    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <h5 class="fw-bold">Active lost & damaged</h5>
            <p class="text-muted small">
                Items currently marked lost or damaged. Actions live here; search and filters apply to both tables.
            </p>

            {{-- Filters --}}
            <div class="mb-3">
                <form method="GET" action="{{ route('books.lost-damage') }}" id="searchForm">
                    <div class="row g-2 mb-3">
                        <div class="col-md-2">
                            <input type="text" name="ctrl_number" class="form-control form-control-sm"
                                   placeholder="Search Ctrl#..."
                                   value="{{ $ctrlNumberSearch ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="book" class="form-control form-control-sm"
                                   placeholder="Search Book Title..."
                                   value="{{ $bookSearch ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="borrower" class="form-control form-control-sm"
                                   placeholder="Search Borrower..."
                                   value="{{ $borrowerSearch ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="borrowed_date" class="form-control form-control-sm"
                                   value="{{ $borrowedDateSearch ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                <i class="bi bi-search me-1"></i>Search
                            </button>
                        </div>
                    </div>
                    @if($filterType)
                        <input type="hidden" name="type" value="{{ $filterType }}">
                    @endif
                </form>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div></div>

                <div>
                    <a href="{{ route('books.lost-damage') }}" class="btn btn-sm {{ !$filterType ? 'btn-primary' : 'btn-light' }}">All</a>
                    <a href="{{ route('books.lost-damage', ['type' => 'lost', 'ctrl_number' => $ctrlNumberSearch ?? '', 'book' => $bookSearch ?? '', 'borrower' => $borrowerSearch ?? '', 'borrowed_date' => $borrowedDateSearch ?? '']) }}" class="btn btn-sm {{ $filterType === 'lost' ? 'btn-primary' : 'btn-light' }}">Lost</a>
                    <a href="{{ route('books.lost-damage', ['type' => 'damaged', 'ctrl_number' => $ctrlNumberSearch ?? '', 'book' => $bookSearch ?? '', 'borrower' => $borrowerSearch ?? '', 'borrowed_date' => $borrowedDateSearch ?? '']) }}" class="btn btn-sm {{ $filterType === 'damaged' ? 'btn-primary' : 'btn-light' }}">Damaged</a>
                </div>

            </div>


            {{-- Table --}}
            <div class="table-responsive">

                <table class="table align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Ctrl Number</th>
                            <th>Book</th>
                            <th>Borrower</th>
                            <th>Borrowed Date</th>
                            <th>Due Date</th>
                            <th>Date Reported</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($records as $record)

                        <tr>

                            <td>
                                @if($record->type == 'lost')
                                    <span class="badge bg-danger">Lost</span>
                                @else
                                    <span class="badge bg-danger">Damaged</span>
                                @endif
                            </td>

                            <td>
                                <div class="fw-semibold">{{ $record->borrow?->copy_number ?? $record->copy_number ?? 'N/A' }}</div>
                            </td>

                            <td>
                                <div class="fw-semibold">{{ $record->book?->title ?? 'Unknown' }}</div>
                                <small class="text-muted">
                                    ISBN: {{ $record->book?->isbn ?? 'N/A' }}
                                </small>
                            </td>

                            <td>
                                <div>{{ $record->borrower_name ?? 'Unknown' }}</div>
                                <small class="text-muted">
                                    LRN: {{ $record->borrower_lrn ?? 'N/A' }}
                                </small>
                            </td>

                            <td>{{ $record->borrow?->borrowed_at ? $record->borrow->borrowed_at->format('M d, Y') : '—' }}</td>

                            <td>{{ $record->due_date ? $record->due_date->format('M d, Y') : '—' }}</td>

                            <td>
                                {{ $record->created_at->format('M d, Y h:i A') }}
                            </td>

                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <form action="{{ route('books.lost-damage.return', $record->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Mark this item as returned?');">
                                        @csrf
                                        <button class="btn btn-outline-success" type="submit" title="Mark as returned">
                                            <i class="bi bi-check-circle me-1"></i>Returned
                                        </button>
                                    </form>

                                    <form action="{{ route('books.lost-damage.replace', $record->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Mark this item as replaced?');">
                                        @csrf
                                        <button class="btn btn-outline-success" type="submit" title="Mark as replaced">
                                            <i class="bi bi-arrow-repeat me-1"></i>Replaced
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                No lost or damaged items found
                            </td>
                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>
    </div>



    {{-- History Logs --}}
    <div class="card shadow-sm">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-1">History logs</h5>
                    <p class="text-muted small mb-0">
                        Recent lost/damaged actions (read-only).
                    </p>
                </div>
                
                {{-- <form action="{{ route('books.lost-damage.clear-history') }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to clear all history logs? This action cannot be undone.');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="bi bi-trash me-1"></i>Clear History
                    </button>
                </form> --}}
            </div>

            <div class="table-responsive">

                <table class="table">

                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Action</th>
                            <th>Ctrl Number</th>
                            <th>Book</th>
                            <th>Borrower</th>
                            <th>Borrowed Date</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>

                    @foreach($history as $log)

                        <tr>

                            <td>
                                <span class="badge bg-danger">
                                    {{ ucfirst($log->type) }}
                                </span>
                            </td>

                            <td>
                                @if($log->action === 'Replaced' || $log->action === 'Returned')
                                    <span class="badge bg-success">{{ $log->action }}</span>
                                @else
                                    <span class="badge bg-danger">{{ $log->action }}</span>
                                @endif
                            </td>

                            <td>{{ $log->ctrl_number }}</td>

                            <td>{{ $log->book_title }}</td>

                            <td>{{ $log->borrower }}</td>

                            <td>{{ $log->borrowed_date ? \Carbon\Carbon::parse($log->borrowed_date)->format('M d, Y') : '—' }}</td>

                            <td>
                                {{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y h:i A') }}
                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>

        </div>
    </div>


</div>
@endsection