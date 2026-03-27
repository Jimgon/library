<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color:#111;">Activity Logs</h2>
    </div>

    <!-- Search and Filter form -->
    <form method="GET" action="<?php echo e(route('utilities.logs')); ?>" class="mb-3">
        <div class="row g-2">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search logs..." value="<?php echo e(request('search')); ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
            <div class="col-md-3">
                <select name="year" class="form-select" onchange="this.form.submit()">
                    <option value="">All Years</option>
                    <?php
                        $currentYear = now()->year;
                        for ($year = $currentYear; $year >= $currentYear - 10; $year--) {
                            $selected = request('year') == $year ? 'selected' : '';
                            echo "<option value=\"$year\" $selected>$year</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="action_filter" class="form-select" onchange="this.form.submit()">
                    <option value="">All Actions</option>
                    <option value="created" <?php echo e(request('action_filter') === 'created' ? 'selected' : ''); ?>>Created</option>
                    <option value="updated" <?php echo e(request('action_filter') === 'updated' ? 'selected' : ''); ?>>Updated</option>
                    <option value="deleted" <?php echo e(request('action_filter') === 'deleted' ? 'selected' : ''); ?>>Deleted</option>
                    <option value="viewed" <?php echo e(request('action_filter') === 'viewed' ? 'selected' : ''); ?>>Viewed</option>
                </select>
            </div>
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
                                <?php echo e($log->user->name ?? $log->user->email ?? 'System'); ?>

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
            <?php echo e($logs->appends(request()->query())->links()); ?>

        </div>
    <?php else: ?>
        <div class="alert alert-info rounded shadow-sm border">No activity logs found.</div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\jimmu\Herd\library\resources\views/utilities/activity-log.blade.php ENDPATH**/ ?>