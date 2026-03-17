<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color:#111;">Archive</h2>
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

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3 border-0" id="archiveTabs" role="tablist" style="gap:0.5rem;">
        <li class="nav-item" role="presentation">
            <button class="nav-link active px-4 py-2 fw-semibold" id="books-tab" data-bs-toggle="tab" data-bs-target="#books" type="button" role="tab" style="border-radius:0.375rem 0.375rem 0 0;background:#f3f4f6;color:#111;">Books</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-2 fw-semibold" id="teachers-tab" data-bs-toggle="tab" data-bs-target="#teachers" type="button" role="tab" style="border-radius:0.375rem 0.375rem 0 0;background:#f3f4f6;color:#111;">Teachers</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-2 fw-semibold" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button" role="tab" style="border-radius:0.375rem 0.375rem 0 0;background:#f3f4f6;color:#111;">Students</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-2 fw-semibold" id="staff-tab" data-bs-toggle="tab" data-bs-target="#staff" type="button" role="tab" style="border-radius:0.375rem 0.375rem 0 0;background:#f3f4f6;color:#111;">Staff</button>
        </li>
        
    </ul>

    <div class="tab-content bg-white p-4 rounded shadow-sm border" id="archiveTabsContent" style="min-height:350px;">
        <!-- Books Tab -->
        <div class="tab-pane fade show active" id="books" role="tabpanel">
            <?php echo $__env->make('utilities.archive-table', ['items' => $books, 'type' => 'book'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        <!-- Teachers Tab -->
        <div class="tab-pane fade" id="teachers" role="tabpanel">
            <?php echo $__env->make('utilities.archive-table', ['items' => $teachers ?? collect(), 'type' => 'teacher'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        <!-- Students Tab -->
        <div class="tab-pane fade" id="students" role="tabpanel">
            <?php echo $__env->make('utilities.archive-table', ['items' => $students, 'type' => 'student'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        <!-- Staff Tab -->
        <div class="tab-pane fade" id="staff" role="tabpanel">
            <?php echo $__env->make('utilities.archive-table', ['items' => $staff, 'type' => 'staff'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Row click for all archive tables (books, teachers, students, staff)
        document.querySelectorAll('.archive-table table tr[data-href]').forEach(function(row) {
            row.addEventListener('click', function() {
                // You can customize this to show a modal or details page
                // For now, just highlight the row
                this.classList.toggle('table-active');
            });
        });
        // Tab hover effect
        document.querySelectorAll('.nav-tabs .nav-link').forEach(function(tab) {
            tab.addEventListener('mouseenter', function() {
                this.style.background = '#dbeafe';
            });
            tab.addEventListener('mouseleave', function() {
                if (!this.classList.contains('active')) {
                    this.style.background = '#f3f4f6';
                }
            });
            tab.addEventListener('click', function() {
                document.querySelectorAll('.nav-tabs .nav-link').forEach(function(other) {
                    other.style.background = '#f3f4f6';
                });
                this.style.background = '#e0e7ff';
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Herd\library\resources\views/utilities/archive.blade.php ENDPATH**/ ?>