<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Database Backups</h1>
        <form id="backupForm" action="<?php echo e(route('utilities.backup')); ?>" method="POST" style="margin-bottom:0;">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-dark">
                <i class="fas fa-plus"></i> Create New Backup
            </button>
        </form>
    </div>
    <div class="card">
        <div class="card-body">
            <?php if(count($backups) > 0): ?>
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Size</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $backups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $backup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($backup['name']); ?></td>
                                <td><?php echo e(number_format($backup['size'] / 1024, 2)); ?> KB</td>
                                <td><?php echo e($backup['date']); ?></td>
                                <td>
                                    <a href="<?php echo e(route('utilities.downloadBackup', $backup['name'])); ?>" class="btn btn-primary btn-sm">
                                        Download
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info mb-0">No backup files found.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
    const backupForm = document.getElementById('backupForm');
    if (backupForm) {
        backupForm.addEventListener('submit', function(e) {
            if(!confirm("Are you sure you want to create a new backup?")) {
                e.preventDefault();
            }
        });
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Herd\library\resources\views/utilities/backups.blade.php ENDPATH**/ ?>