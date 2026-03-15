<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color:#111;">Activity Logs</h2>
    </div>

    <!-- Search form -->
    <form method="GET" action="<?php echo e(route('utilities.logs')); ?>" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search logs..." value="<?php echo e(request('search')); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>

    <?php if($logs->count() > 0): ?>
        <div class="table-responsive rounded shadow-sm border">
        <table class="table align-middle mb-0" style="background:#fff;">
            <thead style="background:#f3f4f6;">
                <tr>
                    <th>#</th>
                    <th>Staff/Admin</th>
                    <th>Role</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $counter = ($logs->currentPage() - 1) * $logs->perPage() + 1;
                ?>

                <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($counter++); ?></td>
                        <td>
                            <?php if($log->user): ?>
                                <?php echo e($log->user->email ?? 'System'); ?>

                            <?php else: ?>
                                System
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($log->user): ?>
                                <span class="badge bg-<?php echo e($log->user->role === 'admin' ? 'danger' : 'info'); ?>">
                                    <?php echo e(ucfirst($log->user->role ?? 'N/A')); ?>

                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary">System</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($log->action ?? 'N/A'); ?></td>
                        <td><?php echo e($log->details ?? 'No details available'); ?></td>
                        <td><?php echo e($log->created_at ? $log->created_at->format('Y-m-d H:i:s') : '-'); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        </div>
        <!-- Pagination links -->
        <div class="d-flex justify-content-center mt-4">
            <?php echo e($logs->links()); ?>

        </div>
    <?php else: ?>
        <div class="alert alert-info rounded shadow-sm border">No activity logs found.</div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Herd\library\resources\views/utilities/activity-log.blade.php ENDPATH**/ ?>