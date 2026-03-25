@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-3">Reports & Analytics</h1>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="p-3 rounded shadow-sm bg-white">
                <div class="small text-muted">Total Transactions</div>
                <div class="h4 mb-0">{{ $totalTransactions ?? 0 }}</div>
                <div class="small text-muted">All time</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 rounded shadow-sm bg-white">
                <div class="small text-muted">Students</div>
                <div class="h4 mb-0">{{ $totalStudents ?? 0 }}</div>
                <div class="small text-muted">Registered</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 rounded shadow-sm bg-white">
                <div class="small text-muted">Teachers</div>
                <div class="h4 mb-0">{{ $totalTeachers ?? 0 }}</div>
                <div class="small text-muted">Registered</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 rounded shadow-sm bg-white">
                <div class="small text-muted">Books in Circulation</div>
                <div class="h4 mb-0">{{ $booksInCirculation ?? 0 }}</div>
                <div class="small text-muted">Currently borrowed</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 rounded shadow-sm bg-white">
                <div class="small text-muted">Overdue Items</div>
                <div class="h4 mb-0 text-danger">{{ $overdueItems ?? 0 }}</div>
                <div class="small text-muted">Need attention</div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="p-3 rounded shadow-sm bg-white h-100">
                <h5 class="mb-3">Popular Books</h5>
                <div style="height:320px;">
                    <canvas id="popularBooksChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="p-3 rounded shadow-sm bg-white h-100">
                <h5 class="mb-3">Books by Category</h5>
                <div style="height:320px;">
                    <canvas id="booksCategoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="row g-3 mt-3">
        <div class="col-lg-12">
            <div class="p-3 rounded shadow-sm bg-white">
                <h5 class="mb-3">Monthly Activity</h5>
                <div style="height:240px;">
                    <canvas id="monthlyActivityChart"></canvas>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Detailed Transactions Section -->
    <div class="row g-3 mt-4">
        <div class="col-lg-12">
            <div class="p-3 rounded shadow-sm bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">All Transactions</h5>
                    <div class="small text-muted">Total: {{ $totalTransactions }}</div>
                </div>

                <!-- Filter and Sort Controls -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form id="filterForm" class="d-flex gap-2">
                            <select id="statusFilter" name="status" class="form-select form-select-sm" style="max-width:150px;">
                                <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All Status</option>
                                <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Active (Borrowed)</option>
                                <option value="completed" {{ $statusFilter === 'completed' ? 'selected' : '' }}>Completed (Returned)</option>
                            </select>

                            <select id="sortBy" name="sort" class="form-select form-select-sm" style="max-width:140px;">
                                <option value="borrowed_at" {{ $sortBy === 'borrowed_at' ? 'selected' : '' }}>Sort by Date Borrowed</option>
                                <option value="due_date" {{ $sortBy === 'due_date' ? 'selected' : '' }}>Sort by Due Date</option>
                                <option value="returned_at" {{ $sortBy === 'returned_at' ? 'selected' : '' }}>Sort by Return Date</option>
                                <option value="id" {{ $sortBy === 'id' ? 'selected' : '' }}>Sort by ID</option>
                            </select>

                            <select id="sortOrder" name="order" class="form-select form-select-sm" style="max-width:110px;">
                                <option value="desc" {{ $sortOrder === 'desc' ? 'selected' : '' }}>Newest First</option>
                                <option value="asc" {{ $sortOrder === 'asc' ? 'selected' : '' }}>Oldest First</option>
                            </select>

                            <button type="button" id="applyFilterBtn" class="btn btn-sm btn-outline-secondary">Apply</button>
                        </form>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;">Txn ID</th>
                                <th style="width: 140px;">Borrower</th>
                                <th style="width: 180px;">Book Title</th>
                                <th style="width: 100px;">Date Borrowed</th>
                                <th style="width: 100px;">Due Date</th>
                                <th style="width: 85px;">Type</th>
                                <th style="width: 100px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                @php
                                    // Determine status display
                                    $isOverdue = is_null($transaction->returned_at) && \Carbon\Carbon::parse($transaction->due_date)->isPast();
                                    
                                    // If transaction has a return_status, use it; otherwise, compute based on returned_at
                                    if (!is_null($transaction->returned_at) && $transaction->return_status) {
                                        $statusText = $transaction->return_status;
                                        $statusLabel = \App\Models\Borrow::getStatusLabel($statusText);
                                        $statusClass = \App\Models\Borrow::getStatusColor($statusText);
                                    } else if (is_null($transaction->returned_at)) {
                                        $statusLabel = $isOverdue ? 'Overdue' : 'Active';
                                        $statusClass = $isOverdue ? 'danger' : 'warning';
                                        $statusText = '';
                                    } else {
                                        // Fallback for old records without return_status
                                        $statusLabel = 'Returned';
                                        $statusClass = 'success';
                                        $statusText = '';
                                    }
                                @endphp
                                <tr>
                                    <td><span class="badge bg-light text-dark">{{ $transaction->id }}</span></td>
                                    <td>
                                        <small title="{{ $transaction->borrower_type }}">{{ $transaction->borrower_name ?: 'Unknown' }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $transaction->book->title ?? 'Deleted Book' }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $transaction->borrowed_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $transaction->due_date->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        @if(is_null($transaction->returned_at))
                                            <span class="badge bg-primary">Borrow</span>
                                        @else
                                            <span class="badge bg-secondary">Return</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $statusClass }}">{{ $statusLabel }}</span>
                                        @if($transaction->return_status === 'damaged_for_repair')
                                            <i class="bi bi-exclamation-circle" title="Marked for Repair"></i>
                                        @elseif($transaction->return_status === 'lost_and_found')
                                            <i class="bi bi-question-circle" title="Lost Item"></i>
                                        @elseif($transaction->return_status === 'late_return')
                                            <i class="bi bi-clock" title="Late Return"></i>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">
                                        <small>No transactions found.</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Custom Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3 gap-2">
                    <div class="text-muted small">
                        Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} results
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            {{-- Previous Page Link --}}
                            @if ($transactions->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link" aria-label="Previous">
                                        <span aria-hidden="true">&lsaquo;</span> Previous
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $transactions->appends(request()->query())->previousPageUrl() }}" rel="prev" aria-label="Previous">
                                        <span aria-hidden="true">&lsaquo;</span> Previous
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
                                @if ($page == $transactions->currentPage())
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link">
                                            {{ $page }}
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($transactions->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $transactions->appends(request()->query())->nextPageUrl() }}" rel="next" aria-label="Next">
                                        Next <span aria-hidden="true">&rsaquo;</span>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link" aria-label="Next">
                                        Next <span aria-hidden="true">&rsaquo;</span>
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
@php
    // Monochrome defaults for a clean black & white dashboard look
    $popularLabelsSafe = $popularLabels ?? [];
    $popularDataSafe = $popularData ?? [];
    $popularColorsSafe = $popularColors ?? array_fill(0, max(1, count($popularDataSafe)), '#000000');

    $categoryLabelsSafe = $categoryLabels ?? [];
    $categoryDataSafe = $categoryData ?? [];
    // Black theme for horizontal bar chart
    $categoryColorsSafe = array_fill(0, max(1, count($categoryDataSafe)), '#000000');
    $categoryBorderColorsSafe = array_fill(0, max(1, count($categoryDataSafe)), '#111111');

    $monthlyLabelsSafe = $monthlyLabels ?? [];
    $monthlyDataSafe = $monthlyData ?? [];
@endphp

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Popular Books (bar)
    const popularCtx = document.getElementById('popularBooksChart')?.getContext('2d');
    if(popularCtx){
        new Chart(popularCtx, {
            type: 'bar',
            data: {
                labels: @json($popularLabelsSafe),
                datasets: [{
                    label: 'Borrow Count',
                    data: @json($popularDataSafe),
                    backgroundColor: @json($popularColorsSafe),
                    borderColor: @json(array_map(fn($c) => '#111111', $popularColorsSafe)),
                    borderRadius: 8,
                    maxBarThickness: 48
                }]
            },
            options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}} }
        });
    }

    // Books by Category (horizontal bar)
    const catCtx = document.getElementById('booksCategoryChart')?.getContext('2d');
    if(catCtx){
        new Chart(catCtx, {
            type: 'bar',
            data: {
                labels: @json($categoryLabelsSafe),
                datasets: [{
                    label: 'Count',
                    data: @json($categoryDataSafe),
                    backgroundColor: @json($categoryColorsSafe),
                    borderColor: @json($categoryBorderColorsSafe),
                    borderRadius: 6
                }]
            },
            options: { indexAxis:'y', responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}} }
        });
    }

    // // Monthly Activity (line)
    // const monthlyCtx = document.getElementById('monthlyActivityChart')?.getContext('2d');
    // if(monthlyCtx){
    //     new Chart(monthlyCtx, {
    //         type: 'line',
    //         data: {
    //             labels: @json($monthlyLabelsSafe),
    //             datasets: [{ label:'Activity', data:@json($monthlyDataSafe), borderColor:'#000000', backgroundColor:'rgba(0,0,0,0.06)', tension:0.3, fill:true }]
    //         },
    //         options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}} }
    //     });
    // }
</script>

<script>
    // AJAX-based filtering without page refresh
    document.getElementById('applyFilterBtn').addEventListener('click', function(e) {
        e.preventDefault();
        
        const status = document.getElementById('statusFilter').value;
        const sort = document.getElementById('sortBy').value;
        const order = document.getElementById('sortOrder').value;
        
        // Build URL with query parameters
        const url = new URL(window.location);
        url.searchParams.set('status', status);
        url.searchParams.set('sort', sort);
        url.searchParams.set('order', order);
        
        // Fetch filtered data
        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Parse the response HTML
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Extract and update the transactions table
            const newTable = doc.querySelector('table tbody');
            const newPagination = doc.querySelector('.d-flex.justify-content-between.align-items-center.mt-3.gap-2');
            
            if (newTable) {
                document.querySelector('table tbody').innerHTML = newTable.innerHTML;
            }
            
            if (newPagination) {
                document.querySelector('.d-flex.justify-content-between.align-items-center.mt-3.gap-2').innerHTML = newPagination.innerHTML;
                
                // Re-attach pagination click handlers
                attachPaginationHandlers();
            }
            
            // Update URL without page refresh
            window.history.replaceState(null, '', url.toString());
        })
        .catch(error => console.error('Filter error:', error));
    });

    // Handle pagination links without page refresh
    function attachPaginationHandlers() {
        document.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const url = this.href;
                
                // Fetch paginated data
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    const newTable = doc.querySelector('table tbody');
                    const newPagination = doc.querySelector('.d-flex.justify-content-between.align-items-center.mt-3.gap-2');
                    
                    if (newTable) {
                        document.querySelector('table tbody').innerHTML = newTable.innerHTML;
                    }
                    
                    if (newPagination) {
                        document.querySelector('.d-flex.justify-content-between.align-items-center.mt-3.gap-2').innerHTML = newPagination.innerHTML;
                        attachPaginationHandlers();
                    }
                    
                    window.history.replaceState(null, '', url);
                    
                    // Scroll to table
                    document.querySelector('table').scrollIntoView({ behavior: 'smooth' });
                })
                .catch(error => console.error('Pagination error:', error));
            });
        });
    }

    // Initialize pagination handlers on page load
    attachPaginationHandlers();
</script>

@endsection
