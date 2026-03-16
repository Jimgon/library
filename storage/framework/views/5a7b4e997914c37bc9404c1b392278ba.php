<?php $__env->startSection('content'); ?>
<div class="container">
    <h3 class="mb-4">Edit Staff</h3>

    
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <?php if(session('error')): ?>
        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <form action="<?php echo e(route('staff.update', $user->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input 
                type="email" 
                name="email" 
                class="form-control" 
                value="<?php echo e(old('email', $user->email)); ?>" 
                required
            >
        </div>

        
        <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-control" required>
                <option value="staff" <?php echo e(old('role', $user->role) === 'staff' ? 'selected' : ''); ?>>Staff</option>
                <option value="admin" <?php echo e(old('role', $user->role) === 'admin' ? 'selected' : ''); ?>>Admin</option>
            </select>
        </div>

        
        <div class="mb-3">
            <label class="form-label">Old Password 
                <small class="text-muted">(required if changing password)</small>
            </label>
            <input 
                type="password" 
                name="old_password" 
                class="form-control" 
                placeholder="Enter old password"
            >
        </div>

        <div class="mb-3">
            <label class="form-label">New Password</label>
            <input 
                type="password" 
                name="new_password" 
                class="form-control" 
                placeholder="Enter new password"
            >
        </div>

        
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Update Staff</button>
            <a href="<?php echo e(route('staff.index')); ?>" class="btn btn-secondary ms-2">Cancel</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Herd\library\resources\views/staff/edit.blade.php ENDPATH**/ ?>