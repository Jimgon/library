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

    <div class="row g-3 mt-3">
        <div class="col-lg-12">
            <div class="p-3 rounded shadow-sm bg-white">
                <h5 class="mb-3">Monthly Activity</h5>
                <div style="height:240px;">
                    <canvas id="monthlyActivityChart"></canvas>
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

    // Monthly Activity (line)
    const monthlyCtx = document.getElementById('monthlyActivityChart')?.getContext('2d');
    if(monthlyCtx){
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($monthlyLabelsSafe, 15, 512) ?>,
                datasets: [{ label:'Activity', data:<?php echo json_encode($monthlyDataSafe, 15, 512) ?>, borderColor:'#000000', backgroundColor:'rgba(0,0,0,0.06)', tension:0.3, fill:true }]
            },
            options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}} }
        });
    }
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Herd\library\resources\views/reports.blade.php ENDPATH**/ ?>