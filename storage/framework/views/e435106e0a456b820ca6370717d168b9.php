<?php $__env->startSection('content'); ?>

<div class="mb-3">
    <a href="<?php echo e(route('users.index')); ?>" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back to Students
    </a>
    <a href="<?php echo e(route('users.print-user', $user->id)); ?>" class="btn btn-primary btn-sm" target="_blank">
        <i class="bi bi-printer"></i> Print
    </a>
</div>

<div class="row">
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>User Details: <?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?></h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> <?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?></p>
                        <p><strong>Grade & Section:</strong> <?php echo e($user->grade_section ?? '-'); ?></p>
                        <p><strong>LRN:</strong> <?php echo e($user->lrn ?? '-'); ?></p>
                        <p><strong>Phone:</strong> <?php echo e($user->phone_number ?? '-'); ?></p>
                        <p><strong>Address:</strong> <?php echo e($user->address ?? '-'); ?></p>
                        <p><strong>Total Books Borrowed:</strong> <?php echo e($user->borrows->count()); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5>Borrowing History</h5>
            </div>
            <div class="card-body">
                <?php if($user->borrows->count() > 0): ?>
                <?php
                    $today = \Carbon\Carbon::today();
                    // Penalty removed — using remarks instead
                ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Book Title</th>
                                <th>Author</th>
                                <th>Borrow Date</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $user->borrows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $borrowDate = $borrow->borrowed_at;
                                    $dueDate    = $borrow->due_date;

                                    // Overdue days only if today is after due date
                                    $overdueDays = 0;
                                    if ($dueDate && $today->gt($dueDate)) {
                                        $overdueDays = (int) ceil($today->diffInDays($dueDate));
                                    }

                                    $penalty = 0;
                                    // Prefer stored admin remark if present
                                    if (!empty($borrow->remark)) {
                                        $remark = $borrow->remark;
                                    } else {
                                        $remark = $overdueDays > 0 ? "{$overdueDays} day(s) overdue" : 'Good Standing';
                                    }
                                ?>
                                <tr>
                                    <td><?php echo e($borrow->book?->title ?? 'Book not found'); ?></td>
                                    <td><?php echo e($borrow->book?->author ?? '-'); ?></td>
                                    <td><?php echo e($borrowDate ? \Carbon\Carbon::parse($borrowDate)->format('F j, Y') : '-'); ?></td>
                                    <td><?php echo e($dueDate ? \Carbon\Carbon::parse($dueDate)->format('F j, Y') : '-'); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo e($borrow->returned_at ? 'success' : 'warning'); ?>">
                                            <?php echo e($borrow->returned_at ? 'Returned' : 'Borrowed'); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                            $lowerRemark = strtolower($remark);
                                            if (str_contains($lowerRemark, 'overdue') || $lowerRemark === 'lost' || $lowerRemark === 'damage') {
                                                $rc = 'bg-danger';
                                            } elseif ($lowerRemark === 'late return') {
                                                $rc = 'bg-warning';
                                            } else {
                                                $rc = 'bg-success';
                                            }
                                        ?>
                                        <span class="badge <?php echo e($rc); ?>"><?php echo e($remark); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">No borrowing history found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\jimmu\Herd\library\resources\views/users/show.blade.php ENDPATH**/ ?>