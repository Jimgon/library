<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h1 class="h3 mb-3">Reports & Analytics</h1>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="p-3 rounded shadow-sm bg-white">
                <div class="small text-muted">Total Transactions</div>
                <div class="h4 mb-0"><?php echo e($totalTransactions ?? 0); ?></div>
                <div class="small text-muted">All time</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 rounded shadow-sm bg-white">
                <div class="small text-muted">Students</div>
                <div class="h4 mb-0"><?php echo e($totalStudents ?? 0); ?></div>
                <div class="small text-muted">Registered</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 rounded shadow-sm bg-white">
                <div class="small text-muted">Teachers</div>
                <div class="h4 mb-0"><?php echo e($totalTeachers ?? 0); ?></div>
                <div class="small text-muted">Registered</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 rounded shadow-sm bg-white">
                <div class="small text-muted">Books in Circulation</div>
                <div class="h4 mb-0"><?php echo e($booksInCirculation ?? 0); ?></div>
                <div class="small text-muted">Currently borrowed</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 rounded shadow-sm bg-white">
                <div class="small text-muted">Overdue Items</div>
                <div class="h4 mb-0 text-danger"><?php echo e($overdueItems ?? 0); ?></div>
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

    

    <!-- Detailed Transactions Section -->
    <div class="row g-3 mt-4">
        <div class="col-lg-12">
            <div class="p-3 rounded shadow-sm bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">All Transactions</h5>
                    <div class="small text-muted">Total: <?php echo e($totalTransactions); ?></div>
                </div>

                <!-- Filter and Sort Controls -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form id="filterForm" class="d-flex gap-2">
                            <select id="statusFilter" name="status" class="form-select form-select-sm" style="max-width:150px;">
                                <option value="all" <?php echo e($statusFilter === 'all' ? 'selected' : ''); ?>>All Status</option>
                                <option value="active" <?php echo e($statusFilter === 'active' ? 'selected' : ''); ?>>Active (Borrowed)</option>
                                <option value="completed" <?php echo e($statusFilter === 'completed' ? 'selected' : ''); ?>>Completed (Returned)</option>
                            </select>

                            <select id="sortBy" name="sort" class="form-select form-select-sm" style="max-width:140px;">
                                <option value="borrowed_at" <?php echo e($sortBy === 'borrowed_at' ? 'selected' : ''); ?>>Sort by Date Borrowed</option>
                                <option value="due_date" <?php echo e($sortBy === 'due_date' ? 'selected' : ''); ?>>Sort by Due Date</option>
                                <option value="returned_at" <?php echo e($sortBy === 'returned_at' ? 'selected' : ''); ?>>Sort by Return Date</option>
                                <option value="id" <?php echo e($sortBy === 'id' ? 'selected' : ''); ?>>Sort by ID</option>
                            </select>

                            <select id="sortOrder" name="order" class="form-select form-select-sm" style="max-width:110px;">
                                <option value="desc" <?php echo e($sortOrder === 'desc' ? 'selected' : ''); ?>>Newest First</option>
                                <option value="asc" <?php echo e($sortOrder === 'asc' ? 'selected' : ''); ?>>Oldest First</option>
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
                            <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
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
                                ?>
                                <tr>
                                    <td><span class="badge bg-light text-dark"><?php echo e($transaction->id); ?></span></td>
                                    <td>
                                        <small title="<?php echo e($transaction->borrower_type); ?>"><?php echo e($transaction->borrower_name ?: 'Unknown'); ?></small>
                                    </td>
                                    <td>
                                        <small><?php echo e($transaction->book->title ?? 'Deleted Book'); ?></small>
                                    </td>
                                    <td>
                                        <small><?php echo e($transaction->borrowed_at->format('M d, Y')); ?></small>
                                    </td>
                                    <td>
                                        <small><?php echo e($transaction->due_date->format('M d, Y')); ?></small>
                                    </td>
                                    <td>
                                        <?php if(is_null($transaction->returned_at)): ?>
                                            <span class="badge bg-primary">Borrow</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Return</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo e($statusClass); ?>"><?php echo e($statusLabel); ?></span>
                                        <?php if($transaction->return_status === 'damaged_for_repair'): ?>
                                            <i class="bi bi-exclamation-circle" title="Marked for Repair"></i>
                                        <?php elseif($transaction->return_status === 'lost_and_found'): ?>
                                            <i class="bi bi-question-circle" title="Lost Item"></i>
                                        <?php elseif($transaction->return_status === 'late_return'): ?>
                                            <i class="bi bi-clock" title="Late Return"></i>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">
                                        <small>No transactions found.</small>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Custom Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3 gap-2">
                    <div class="text-muted small">
                        Showing <?php echo e($transactions->firstItem() ?? 0); ?> to <?php echo e($transactions->lastItem() ?? 0); ?> of <?php echo e($transactions->total()); ?> results
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            
                            <?php if($transactions->onFirstPage()): ?>
                                <li class="page-item disabled">
                                    <span class="page-link" aria-label="Previous">
                                        <span aria-hidden="true">&lsaquo;</span> Previous
                                    </span>
                                </li>
                            <?php else: ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo e($transactions->appends(request()->query())->previousPageUrl()); ?>" rel="prev" aria-label="Previous">
                                        <span aria-hidden="true">&lsaquo;</span> Previous
                                    </a>
                                </li>
                            <?php endif; ?>

                            
                            <?php $__currentLoopData = $transactions->getUrlRange(1, $transactions->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($page == $transactions->currentPage()): ?>
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link">
                                            <?php echo e($page); ?>

                                        </span>
                                    </li>
                                <?php else: ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo e($url); ?>"><?php echo e($page); ?></a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            
                            <?php if($transactions->hasMorePages()): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo e($transactions->appends(request()->query())->nextPageUrl()); ?>" rel="next" aria-label="Next">
                                        Next <span aria-hidden="true">&rsaquo;</span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link" aria-label="Next">
                                        Next <span aria-hidden="true">&rsaquo;</span>
                                    </span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
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
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Popular Books (bar)
    const popularCtx = document.getElementById('popularBooksChart')?.getContext('2d');
    if(popularCtx){
        new Chart(popularCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($popularLabelsSafe, 15, 512) ?>,
                datasets: [{
                    label: 'Borrow Count',
                    data: <?php echo json_encode($popularDataSafe, 15, 512) ?>,
                    backgroundColor: <?php echo json_encode($popularColorsSafe, 15, 512) ?>,
                    borderColor: <?php echo json_encode(array_map(fn($c) => '#111111', $popularColorsSafe), 512) ?>,
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
                labels: <?php echo json_encode($categoryLabelsSafe, 15, 512) ?>,
                datasets: [{
                    label: 'Count',
                    data: <?php echo json_encode($categoryDataSafe, 15, 512) ?>,
                    backgroundColor: <?php echo json_encode($categoryColorsSafe, 15, 512) ?>,
                    borderColor: <?php echo json_encode($categoryBorderColorsSafe, 15, 512) ?>,
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
    //             labels: <?php echo json_encode($monthlyLabelsSafe, 15, 512) ?>,
    //             datasets: [{ label:'Activity', data:<?php echo json_encode($monthlyDataSafe, 15, 512) ?>, borderColor:'#000000', backgroundColor:'rgba(0,0,0,0.06)', tension:0.3, fill:true }]
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

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\jimmu\Herd\library\resources\views/reports.blade.php ENDPATH**/ ?>