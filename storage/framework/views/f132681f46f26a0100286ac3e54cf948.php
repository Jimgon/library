<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-1">Staff Management</h4>
            <p class="text-muted mb-0">Manage library staff accounts and permissions</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('staff.create')); ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Add Staff
            </a>
        </div>
    </div>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <form class="row g-3 mb-4" action="<?php echo e(route('staff.index')); ?>" method="GET">
        <div class="col-md-8">
            <input class="form-control" type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search staff by email or role..." onchange="this.form.submit()">
        </div>
        <div class="col-md-4">
            <button class="btn btn-primary w-100" type="submit">
                <i class="bi bi-search me-1"></i>Search
            </button>
        </div>
    </form>

    
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0">Staff Accounts</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 fw-semibold">Email</th>
                            <th class="border-0 fw-semibold">Role</th>
                            <th class="border-0 fw-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?php echo e($user->email ?? '-'); ?></div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-secondary"><?php echo e(ucfirst($user->role ?? 'N/A')); ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo e(route('staff.edit', $user->id)); ?>" class="btn btn-sm btn-outline-dark" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="<?php echo e(route('staff.destroy', $user->id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this staff account? This action cannot be undone.');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                                        No staff accounts found.
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <?php if($users instanceof \Illuminate\Pagination\LengthAwarePaginator): ?>
                <div class="d-flex justify-content-center mt-4 p-3">
                    <?php echo e($users->appends(request()->query())->links('pagination::bootstrap-5')); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Herd\library\resources\views/staff/index.blade.php ENDPATH**/ ?>