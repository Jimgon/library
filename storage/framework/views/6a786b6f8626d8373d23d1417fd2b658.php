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
                <div style="border-bottom:1px solid #e5e7eb;padding:0.75rem 1.25rem;font-weight:600;font-size:1.05rem;">Most Borrowed Book/s (Top 10)</div>
                <div style="padding:1rem;flex:1;display:flex;flex-direction:column;">
                    <div style="height: 280px; margin-bottom: 1rem;">
                        <canvas id="mostBorrowedBooksChart"></canvas>
                    </div>
                    
                    <!-- Mini Statistics -->
                    <div style="padding-top: 0.5rem; border-top: 1px solid #e5e7eb; font-size: 0.9rem;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                            <div>
                                <div style="color:#6b7280;font-size:0.8rem;">Books Borrowed</div>
                                <div style="font-weight:700;color:#1f2937;"><?php echo e($totalUniqueBooksBorrowed); ?></div>
                            </div>
                            <div>
                                <div style="color:#6b7280;font-size:0.8rem;">Avg per Book</div>
                                <div style="font-weight:700;color:#1f2937;"><?php echo e($avgBorrowsPerBook); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <div class="row g-3 mt-3">
        <div class="col-lg-12">
            <div class="p-3 rounded shadow-sm bg-white">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h5 class="mb-0">Monthly Activity (Last 12 Months)</h5>
                    <div style="font-size: 0.9rem; color: #6b7280;">
                        <span style="margin-right: 1.5rem;">
                            <span style="display: inline-block; width: 12px; height: 12px; background-color: #3b82f6; border-radius: 2px; margin-right: 0.5rem;"></span>
                            Total
                        </span>
                        <span style="margin-right: 1.5rem;">
                            <span style="display: inline-block; width: 12px; height: 12px; background-color: #10b981; border-radius: 2px; margin-right: 0.5rem;"></span>
                            Completed Returns
                        </span>
                        <span>
                            <span style="display: inline-block; width: 12px; height: 12px; background-color: #f59e0b; border-radius: 2px; margin-right: 0.5rem;"></span>
                            Avg Monthly
                        </span>
                    </div>
                </div>
                <div style="height:310px; margin-bottom: 1rem;">
                    <canvas id="monthlyActivityChart"></canvas>
                </div>
                
                <!-- Statistics Summary -->
                <div style="display: flex; gap: 1rem; flex-wrap: wrap; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                    <div style="flex: 1; min-width: 150px;">
                        <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 0.25rem;">Average Monthly</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #1f2937;"><?php echo e(round($avgMonthlyActivity, 1)); ?></div>
                    </div>
                    <div style="flex: 1; min-width: 150px;">
                        <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 0.25rem;">Peak Month Activity</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #1f2937;"><?php echo e($peakMonthActivity); ?> transactions</div>
                    </div>
                    <div style="flex: 1; min-width: 150px;">
                        <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 0.25rem;">Lowest Activity</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #1f2937;"><?php echo e($lowestMonthActivity); ?> transactions</div>
                    </div>
                    <div style="flex: 1; min-width: 150px;">
                        <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 0.25rem;">Total 12-Month Activity</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #1f2937;"><?php echo e(array_sum($monthlyDataSafe)); ?></div>
                    </div>
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

    <!-- Most Borrowed Books Detailed Table -->
    <div class="mt-4">
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.75rem;box-shadow:0 1px 2px 0 #0001;">
            <div style="border-bottom:1px solid #e5e7eb;padding:0.75rem 1.25rem;font-weight:600;font-size:1.05rem;">Most Borrowed Book/s Details</div>
            <div style="padding:1rem;max-height:500px;overflow-y:auto;">
                <?php if(count($mostBorrowedBookDetails) > 0): ?>
                    <table style="width:100%;border-collapse:collapse;font-size:0.98rem;">
                        <thead>
                            <tr style="background:#f9fafb;">
                                <th style="padding:0.5rem 0.75rem;text-align:left;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">Rank</th>
                                <th style="padding:0.5rem 0.75rem;text-align:left;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">Title</th>
                                <th style="padding:0.5rem 0.75rem;text-align:left;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">Author</th>
                                <th style="padding:0.5rem 0.75rem;text-align:center;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">Borrows</th>
                                <th style="padding:0.5rem 0.75rem;text-align:center;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">% of Total</th>
                                <th style="padding:0.5rem 0.75rem;text-align:center;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">Available</th>
                                <th style="padding:0.5rem 0.75rem;text-align:center;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $mostBorrowedBookDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr style="border-bottom:1px solid #f3f4f6;">
                                    <td style="padding:0.5rem 0.75rem;text-align:center;font-weight:600;color:#0066cc;"><?php echo e($index + 1); ?></td>
                                    <td style="padding:0.5rem 0.75rem;font-weight:500;"><?php echo e($detail['title']); ?></td>
                                    <td style="padding:0.5rem 0.75rem;"><?php echo e($detail['author']); ?></td>
                                    <td style="padding:0.5rem 0.75rem;text-align:center;font-weight:700;color:#059669;"><?php echo e($detail['borrows']); ?></td>
                                    <td style="padding:0.5rem 0.75rem;text-align:center;">
                                        <span style="background:#dbeafe;color:#0066cc;padding:0.25rem 0.5rem;border-radius:0.375rem;font-weight:500;"><?php echo e($detail['percentage']); ?>%</span>
                                    </td>
                                    <td style="padding:0.5rem 0.75rem;text-align:center;color:#6b7280;"><?php echo e($detail['available_copies']); ?></td>
                                    <td style="padding:0.5rem 0.75rem;text-align:center;font-weight:600;"><?php echo e($detail['total_copies']); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color:#6b7280;margin-bottom:0;">No borrow data available.</p>
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
    // Most Borrowed Books Chart with enhanced styling
    const borrowedData = <?php echo json_encode($mostBorrowedBookData, 15, 512) ?>;
    const borrowedColors = borrowedData.map((value, index) => {
        // Color gradient: top book in bold blue, others in lighter shades
        if (index === 0) return '#1e40af'; // Dark blue for #1
        if (index === 1) return '#2563eb'; // Blue for #2
        if (index === 2) return '#3b82f6'; // Light blue for #3
        return '#60a5fa';                   // Lighter blue for rest
    });
    
    const ctx = document.getElementById('mostBorrowedBooksChart') ? document.getElementById('mostBorrowedBooksChart').getContext('2d') : null;
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($mostBorrowedBookLabels, 15, 512) ?>,
                datasets: [{
                    label: 'Times Borrowed',
                    data: borrowedData,
                    backgroundColor: borrowedColors,
                    borderColor: borrowedColors.map(c => c),
                    borderWidth: 1.5,
                    borderRadius: 8,
                    maxBarThickness: 45
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { 
                        beginAtZero: true, 
                        ticks: { precision: 0 },
                        title: {
                            display: true,
                            text: 'Number of Borrows'
                        }
                    },
                    y: { 
                        ticks: { autoSkip: false }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { 
                        callbacks: { 
                            label: function(context) { 
                                const percentage = ((context.parsed.x / <?php echo json_encode(array_sum($mostBorrowedBookData), 15, 512) ?>) * 100).toFixed(1);
                                return context.dataset.label + ': ' + context.parsed.x + ' (' + percentage + '%)'; 
                            } 
                        } 
                    }
                }
            }
        });
    }

    // Monthly Activity with enhanced data
    const monthlyCtx = document.getElementById('monthlyActivityChart')?.getContext('2d');
    if(monthlyCtx){
        const avgValue = <?php echo json_encode($avgMonthlyActivity, 15, 512) ?>;
        
        // Create average line data
        const avgData = Array(<?php echo json_encode($monthlyLabelsSafe, 15, 512) ?>.length).fill(avgValue);
        
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($monthlyLabelsSafe, 15, 512) ?>,
                datasets: [
                    {
                        label: 'Total Transactions',
                        data: <?php echo json_encode($monthlyDataSafe, 15, 512) ?>,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.08)',
                        borderWidth: 2.5,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Completed Returns',
                        data: <?php echo json_encode($monthlyCompletedData, 15, 512) ?>,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 1.5,
                        pointHoverRadius: 5
                    },
                    {
                        label: 'Average Monthly Activity',
                        data: avgData,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0)',
                        borderWidth: 2.5,
                        borderDash: [10, 5],
                        tension: 0,
                        fill: false,
                        pointRadius: 0,
                        pointHoverRadius: 0
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Transactions'
                        },
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12
                            },
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        callbacks: {
                            title: function(context) {
                                return context[0].label;
                            },
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += Math.round(context.parsed.y * 10) / 10;
                                return label;
                            },
                            afterLabel: function(context) {
                                if (context.dataset.label === 'Total Transactions' && context.parsed.y > 0) {
                                    const percentage = ((context.parsed.y / <?php echo json_encode(array_sum($monthlyDataSafe), 15, 512) ?>) * 100).toFixed(1);
                                    return `(${percentage}% of total)`;
                                }
                                return '';
                            }
                        }
                    }
                }
            }
        });
    }

</script>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Herd\library\resources\views/dashboard.blade.php ENDPATH**/ ?>