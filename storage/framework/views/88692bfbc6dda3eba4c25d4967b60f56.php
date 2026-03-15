<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h1 class="h2 mb-0">Teachers List</h1>
        <div class="gap-2 d-flex">
            <a href="<?php echo e(route('teachers.import.form')); ?>" class="btn btn-dark">
                <i class="bi bi-download"></i>  Import Teachers
            </a>
            <a href="<?php echo e(route('teachers.create')); ?>" class="btn btn-success">
                <i class="bi bi-plus-circle me-2"></i>Add New Teacher
            </a>
                        <a href="<?php echo e(route('users.print-teacher')); ?>" target="_blank" class="btn btn-primary">
                <i class="bi bi-printer me-2"></i>Print All Teachers
            </a>
        </div>
    </div>

    <?php if(session('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?php echo e(session('warning')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

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

    
    <form class="row g-3 mb-4" action="<?php echo e(route('teachers.index')); ?>" method="GET">
        <div class="col-md-12">
            <input class="form-control" type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search teachers by name, email..." onchange="this.form.submit()">
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0">Teachers Management</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 fw-semibold">Name</th>
                            <th class="border-0 fw-semibold">Email</th>
                            <th class="border-0 fw-semibold d-none d-md-table-cell">Gender</th>
                            <th class="border-0 fw-semibold d-none d-lg-table-cell">Address</th>
                            <th class="border-0 fw-semibold d-none d-lg-table-cell">Phone</th>
                            <th class="border-0 fw-semibold">Borrowed Books</th>
                            <th class="border-0 fw-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $activeBorrows = $teacher->borrows->whereNull('returned_at');
                                $totalOverdue = 0;
                                $today = \Carbon\Carbon::today();
                                foreach($activeBorrows as $borrow) {
                                    $dueDate = $borrow->due_date;
                                    if ($dueDate && $today->gt($dueDate)) {
                                        $overdueDays = (int) ceil($today->diffInDays($dueDate));
                                        $totalOverdue += $overdueDays;
                                    }
                                }
                            ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?php echo e($teacher->name); ?></div>
                                </td>
                                <td>
                                    <small class="text-muted"><?php echo e($teacher->email); ?></small>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="badge bg-secondary"><?php echo e(ucfirst($teacher->gender)); ?></span>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <small><?php echo e(Str::limit($teacher->address, 30)); ?></small>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <small><?php echo e($teacher->phone_number); ?></small>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#borrowModal<?php echo e($teacher->id); ?>">
                                        <i class="bi bi-book"></i> View Books
                                    </button>
                                </td>

                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo e(route('teachers.borrow-history', $teacher->id)); ?>" class="btn btn-sm btn-outline-dark" title="View Borrow History">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('teachers.edit', $teacher->id)); ?>" class="btn btn-sm btn-outline-dark" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="<?php echo e(route('teachers.destroy', $teacher->id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this teacher?');">
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
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                                        No teachers found.
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4 p-3">
                <?php echo e($teachers->withQueryString()->links('pagination::bootstrap-5')); ?>

            </div>
        </div>
    </div>

    
    <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="modal fade" id="borrowModal<?php echo e($teacher->id); ?>" tabindex="-1" aria-labelledby="borrowModalLabel<?php echo e($teacher->id); ?>" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="borrowModalLabel<?php echo e($teacher->id); ?>">
                            <i class="bi bi-book me-2"></i>All Borrowed Books
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h6 class="text-muted mb-3">Teacher: <strong><?php echo e($teacher->name); ?></strong></h6>
                        <?php $allBorrows = $teacher->borrows; ?>
                        <?php if($allBorrows->count() > 0): ?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>ISBN</th>
                                        <th>Borrowed At</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $allBorrows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $bookTitle = $borrow->book ? $borrow->book->title : 'Book not found';
                                            $bookAuthor = $borrow->book ? ($borrow->book->author ?? 'N/A') : 'N/A';
                                            $bookIsbn = $borrow->book ? ($borrow->book->isbn ?? 'N/A') : 'N/A';
                                            $borrowedAt = $borrow->borrowed_at ? \Carbon\Carbon::parse($borrow->borrowed_at)->format('M d, Y') : 'N/A';
                                            $dueDate = $borrow->due_date ? \Carbon\Carbon::parse($borrow->due_date)->format('M d, Y') : 'N/A';
                                            $status = $borrow->returned_at ? 'Returned' : 'Active';
                                        ?>
                                        <tr>
                                            <td><?php echo e($bookTitle); ?></td>
                                            <td><?php echo e($bookAuthor); ?></td>
                                            <td><?php echo e($bookIsbn); ?></td>
                                            <td><?php echo e($borrowedAt); ?></td>
                                            <td><?php echo e($dueDate); ?></td>
                                            <td><?php echo e($status); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-muted">No books borrowed.</div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Herd\library\resources\views/users/teachers.blade.php ENDPATH**/ ?>