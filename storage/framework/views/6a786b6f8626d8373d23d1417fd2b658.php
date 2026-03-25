<?php $__env->startSection('content'); ?>
<div class="container">
        <?php if($nearDueBorrows->count() > 0): ?>
            <div class="alert alert-warning mb-4">
                <strong>⚠️ Upcoming Due Dates:</strong><br>
                The following users have books due within 3 days:<br>
                <ul class="mb-0">
                    <?php $__currentLoopData = $nearDueBorrows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <strong><?php echo e($borrow->user->first_name); ?> <?php echo e($borrow->user->last_name); ?></strong> -
                            <span class="text-dark"><?php echo e($borrow->book->title ?? 'Unknown Book'); ?></span>
                            <span class="text-muted">(Due: <?php echo e(\Carbon\Carbon::parse($borrow->due_date)->format('M d, Y')); ?>)</span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
    <!-- Top 3 Boxes (shadcn style) -->
    <div class="d-flex gap-4 mb-4" style="flex-wrap:wrap;">
        <div class="flex-fill min-w-0" style="min-width:200px;">
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.75rem;padding:1.5rem 1rem;box-shadow:0 1px 2px 0 #0001;">
                <div style="font-size:1.1rem;font-weight:600;color:#111;">Total Book/s</div>
                <div style="font-size:2rem;font-weight:700;color:#00000;"><?php echo e($totalBooks); ?></div>
            </div>
        </div>
        <div class="flex-fill min-w-0" style="min-width:200px;">
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.75rem;padding:1.5rem 1rem;box-shadow:0 1px 2px 0 #0001;">
                <div style="font-size:1.1rem;font-weight:600;color:#111;">Total User/s</div>
                <div style="font-size:2rem;font-weight:700;color:#00000;"><?php echo e($totalUsers); ?></div>
            </div>
        </div>
        <div class="flex-fill min-w-0" style="min-width:200px;">
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.75rem;padding:1.5rem 1rem;box-shadow:0 1px 2px 0 #0001;">
                <div style="font-size:1.1rem;font-weight:600;color:#111;">Total Borrow/s</div>
                <div style="font-size:2rem;font-weight:700;color:#00000;"><?php echo e($totalBorrows); ?></div>
            </div>
        </div>
    </div>

    <!-- Row 1: Students + Chart (shadcn style) -->
    <div class="d-flex gap-4 mb-4" style="flex-wrap:wrap;">
        <!-- Students with Unreturned Books -->
        <div class="flex-fill min-w-0" style="min-width:340px;">
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.75rem;box-shadow:0 1px 2px 0 #0001;display:flex;flex-direction:column;height:100%;">
                <div style="border-bottom:1px solid #e5e7eb;padding:0.75rem 1.25rem;font-weight:600;font-size:1.05rem;">Students with Unreturned Book/s</div>
                <div style="padding:1rem;max-height:400px;overflow-y:auto;">
                    <?php if($studentsWithUnreturned->count() > 0): ?>
                        <ul style="list-style:none;padding:0;margin-bottom:1rem;">
                            <?php $__currentLoopData = $studentsWithUnreturned; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li style="border-bottom:1px solid #f3f4f6;padding:0.75rem 0;">
                                    <div style="font-weight:600;color:#111;"><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></div>
                                    <ul style="margin:0.5rem 0 0 1rem;padding:0;list-style:disc;">
                                        <?php $__currentLoopData = $student->borrows->whereNull('returned_at')->sortBy(fn($borrow) => $borrow->book->title); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $borrowedAt = null;
                                                if ($borrow->borrowed_at) {
                                                    $borrowedAt = \Carbon\Carbon::parse($borrow->borrowed_at);
                                                } elseif ($borrow->created_at) {
                                                    $borrowedAt = \Carbon\Carbon::parse($borrow->created_at);
                                                }
                                            ?>
                                            <li style="font-size:0.97rem;">
                                                <span style="color:#00000;">📚 <?php echo e($borrow->book->title ?? 'Unknown Book'); ?></span>
                                                <span style="color:#6b7280;font-size:0.92em;">(Borrowed: <?php echo e($borrowedAt ? $borrowedAt->format('M d, Y') : 'N/A'); ?>)</span>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                        <div class="d-flex justify-content-center"><?php echo e($studentsWithUnreturned->links('pagination::bootstrap-5')); ?></div>
                    <?php else: ?>
                        <p class="text-muted">All students have returned their books.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Most Borrowed Books Chart -->
        <div class="flex-fill min-w-0" style="min-width:340px;">
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.75rem;box-shadow:0 1px 2px 0 #0001;display:flex;flex-direction:column;height:100%;">
                <div style="border-bottom:1px solid #e5e7eb;padding:0.75rem 1.25rem;font-weight:600;font-size:1.05rem;">Most Borrowed Book/s</div>
                <div style="padding:1rem;">
                    <div style="height: 300px;">
                        <canvas id="mostBorrowedBooksChart"></canvas>
                    </div>
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

    <!-- Row 2: Available Books (shadcn style) -->
    <div class="mt-4">
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.75rem;box-shadow:0 1px 2px 0 #0001;">
            <div style="border-bottom:1px solid #e5e7eb;padding:0.75rem 1.25rem;font-weight:600;font-size:1.05rem;">Available Book/s</div>
            <div style="padding:1rem;max-height:400px;overflow-y:auto;">
                <?php if($availableBooks->count() > 0): ?>
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
                            <?php $__currentLoopData = $availableBooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr style="border-bottom:1px solid #f3f4f6;">
                                    <td style="padding:0.5rem 0.75rem;"><?php echo e($book->title); ?></td>
                                    <td style="padding:0.5rem 0.75rem;"><?php echo e($book->author); ?></td>
                                    <td style="padding:0.5rem 0.75rem;"><?php echo e($book->isbn); ?></td>
                                    <td style="padding:0.5rem 0.75rem;"><?php echo e($book->category); ?></td>
                                    <td style="padding:0.5rem 0.75rem;"><?php echo e($book->available_copies_actual ?? $book->available_copies); ?></td>
                                    <td style="padding:0.5rem 0.75rem;"><?php echo e($book->total_copies_actual ?? $book->copies); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-3"><?php echo e($availableBooks->links('pagination::bootstrap-5')); ?></div>
                <?php else: ?>
                    <p style="color:#6b7280;margin-bottom:0;">No books available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php
    $count = count($mostBorrowedBookData);
    $borrowedColors = array_fill(0, $count, '#1e3a8a');
?>

<script>
    const borrowedColors = <?php echo json_encode($borrowedColors, 15, 512) ?>;
    const ctx = document.getElementById('mostBorrowedBooksChart') ? document.getElementById('mostBorrowedBooksChart').getContext('2d') : null;
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($mostBorrowedBookLabels, 15, 512) ?>,
                datasets: [{
                    label: 'Borrow Count',
                    data: <?php echo json_encode($mostBorrowedBookData, 15, 512) ?>,
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
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Herd\library\resources\views/dashboard.blade.php ENDPATH**/ ?>