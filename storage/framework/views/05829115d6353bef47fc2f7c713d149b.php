  

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Edit Book</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('books.update', $book)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo e($book->title); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="author" class="form-label">Author</label>
                        <input type="text" class="form-control" id="author" name="author" value="<?php echo e($book->author); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="publisher" class="form-label">Publisher</label>
                        <input type="text" class="form-control" id="publisher" name="publisher" value="<?php echo e($book->publisher); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="isbn" class="form-label">ISBN</label>
                        <input type="text" class="form-control" id="isbn" name="isbn" value="<?php echo e($book->isbn); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="call_number" class="form-label">Control Number Base</label>
                        <input type="text" class="form-control <?php $__errorArgs = ['call_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="call_number" name="call_number" value="<?php echo e(old('call_number', $book->call_number ?? $nextCtrlBase)); ?>">
                        <?php $__errorArgs = ['call_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <?php
                        $selectedCat = old('category', $book->category);
                        $isOther = $selectedCat === 'other' || (!$categories->contains($selectedCat) && $selectedCat !== '');
                    ?>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select name="category" id="category" class="form-select <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">-- Select Category --</option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cat); ?>" <?php echo e($selectedCat === $cat ? 'selected' : ''); ?>><?php echo e($cat); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <option value="other" <?php echo e($selectedCat === 'other' ? 'selected' : ''); ?>>Other</option>
                        </select>
                        <input type="text" name="other_category" id="other_category" class="form-control mt-2 <?php $__errorArgs = ['other_category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Enter category" value="" style="display: none;">
                        <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <?php $__errorArgs = ['other_category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="published_year" class="form-label">Published Year</label>
                            <input
                                type="number"
                                name="published_year"
                                id="published_year"
                                class="form-control <?php $__errorArgs = ['published_year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('published_year', $book->published_year)); ?>"
                                min="1900"
                                max="<?php echo e(date('Y') + 1); ?>"
                            >
                            <?php $__errorArgs = ['published_year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="pages" class="form-label">Pages</label>
                            <input
                                type="number"
                                name="pages"
                                id="pages"
                                class="form-control <?php $__errorArgs = ['pages'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('pages', $book->pages)); ?>"
                                min="1"
                            >
                            <?php $__errorArgs = ['pages'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edition" class="form-label">Edition</label>
                            <input
                                type="text"
                                name="edition"
                                id="edition"
                                class="form-control <?php $__errorArgs = ['edition'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('edition', $book->edition)); ?>"
                                placeholder="e.g., 3rd Edition"
                            >
                            <?php $__errorArgs = ['edition'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="condition" class="form-label">Condition</label>
                            <select
                                name="condition"
                                id="condition"
                                class="form-select <?php $__errorArgs = ['condition'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            >
                                <option value="">-- Select Condition --</option>
                                <option value="Brand New" <?php echo e(old('condition', $book->condition) === 'Brand New' ? 'selected' : ''); ?>>Brand New</option>
                                <option value="Old" <?php echo e(old('condition', $book->condition) === 'Old' ? 'selected' : ''); ?>>Old</option>
                            </select>
                            <?php $__errorArgs = ['condition'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="acquisition_type" class="form-label">Acquisition Type</label>
                            <select
                                name="acquisition_type"
                                id="acquisition_type"
                                class="form-select <?php $__errorArgs = ['acquisition_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            >
                                <option value="">-- Select Type --</option>
                                <option value="purchase" <?php echo e(old('acquisition_type', $book->acquisition_type) === 'purchase' ? 'selected' : ''); ?>>Purchase</option>
                                <option value="donation" <?php echo e(old('acquisition_type', $book->acquisition_type) === 'donation' ? 'selected' : ''); ?>>Donation</option>
                                <option value="exchange" <?php echo e(old('acquisition_type', $book->acquisition_type) === 'exchange' ? 'selected' : ''); ?>>Exchange</option>
                                <option value="grant" <?php echo e(old('acquisition_type', $book->acquisition_type) === 'grant' ? 'selected' : ''); ?>>Grant</option>
                            </select>
                            <?php $__errorArgs = ['acquisition_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="source_of_funds" class="form-label">Source of Funds</label>
                            <input
                                type="text"
                                name="source_of_funds"
                                id="source_of_funds"
                                class="form-control <?php $__errorArgs = ['source_of_funds'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('source_of_funds', $book->source_of_funds)); ?>"
                                placeholder="e.g., School Budget, PTA Fund"
                            >
                            <?php $__errorArgs = ['source_of_funds'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="purchase_price" class="form-label">Purchase Price</label>
                            <input
                                type="number"
                                name="purchase_price"
                                id="purchase_price"
                                class="form-control <?php $__errorArgs = ['purchase_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('purchase_price', $book->purchase_price)); ?>"
                                min="0"
                                step="0.01"
                            >
                            <?php $__errorArgs = ['purchase_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    

                    <div class="mb-3">
                        <label for="copies" class="form-label">Add Number of Copies</label>
                        <input type="number" class="form-control" id="copies" name="copies" value="0" min="0" required>
                        <small class="form-text text-muted">Current copies: <?php echo e($book->copies); ?>. Enter a number to add more copies.</small>
                    </div>

                    
                    <div class="card bg-light mb-4 mt-3">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Physical Copies Details</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addCopyBtn">
                                    <i class="bi bi-plus me-1"></i>Add Copy
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0" id="copiesTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Ctrl #</th>
                                            <th style="width: 20%;">Acquisition Year</th>
                                            <th style="width: 20%;">Status</th>
                                            <th style="width: 20%;">Condition</th>
                                            <th style="width: 20%;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="copiesContainer">
                                        <?php $__empty_1 = true; $__currentLoopData = $copies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $copy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr data-copy-id="<?php echo e($copy->id); ?>">
                                                <td><input type="text" name="control_numbers[]" class="form-control form-control-sm ctrl-number" value="<?php echo e($copy->control_number); ?>" readonly></td>
                                                <td><input type="number" name="copy_year[]" class="form-control form-control-sm copy-year-input" min="1900" max="2100" value="<?php echo e($copy->acquisition_year); ?>" placeholder="Enter year"></td>
                                                <td><input type="text" name="copy_status[]" class="form-control form-control-sm" value="<?php echo e($copy->status); ?>" readonly></td>
                                                <td>
                                                    <select name="copy_condition[]" data-copy-id="<?php echo e($copy->id); ?>" class="form-select form-select-sm copy-condition-select">
                                                        <option value="Brand New" <?php echo e($copy->condition === 'Brand New' ? 'selected' : ''); ?>>Brand New</option>
                                                        <option value="Old" <?php echo e($copy->condition === 'Old' ? 'selected' : ''); ?>>Old</option>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-danger deleteCopyBtn" data-copy-id="<?php echo e($copy->id); ?>" data-book-id="<?php echo e($book->id); ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">No copies yet. Add copies using the button above.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Book</button>
                    <a href="<?php echo e(route('books.catalog')); ?>" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category');
        const otherInput = document.getElementById('other_category');
        const addCopyBtn = document.getElementById('addCopyBtn');
        const copiesContainer = document.getElementById('copiesContainer');
        const copiesInput = document.getElementById('copies');
        const bookId = document.querySelector('form').action.split('/')[2]; // Extract book ID from form action

        function toggleOther() {
            if (categorySelect.value === 'other') {
                otherInput.style.display = 'block';
                otherInput.required = true;
                otherInput.disabled = false;
            } else {
                otherInput.style.display = 'none';
                otherInput.required = false;
                otherInput.disabled = true;
                otherInput.value = '';
            }
        }

        function syncOtherCategory() {
            const val = otherInput.value.trim();
            if (!val) return;
            let existing = Array.from(categorySelect.options).find(o => o.value === val);
            if (!existing) {
                const opt = document.createElement('option');
                opt.value = val;
                opt.text = val;
                categorySelect.add(opt, categorySelect.options[categorySelect.options.length-1]);
                categorySelect.value = val;
            }
        }

        // Handle delete copy button clicks
        copiesContainer.addEventListener('click', function(e) {
            if (e.target.closest('.deleteCopyBtn')) {
                const btn = e.target.closest('.deleteCopyBtn');
                const copyId = btn.getAttribute('data-copy-id');
                const bookIdAttr = btn.getAttribute('data-book-id');
                const controlNumber = btn.closest('tr').querySelector('.ctrl-number').value;
                
                if (!confirm(`Delete copy ${controlNumber}?`)) {
                    return;
                }
                
                const formData = new FormData();
                formData.append('control_number', controlNumber);
                
                fetch(`/books/${bookIdAttr}/delete-copy`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to delete copy'));
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert('Error deleting copy: ' + err.message);
                });
            }
        });

        // Handle add copy button - open modal or redirect to add copies
        addCopyBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const toAdd = prompt('Enter number of copies to add (1-1000):', '1');
            if (toAdd === null) return;
            
            const count = parseInt(toAdd) || 0;
            if (count < 1 || count > 1000) {
                alert('Please enter a number between 1 and 1000');
                return;
            }
            
            // Show acquisition year input
            const acquisitionYear = prompt('Enter acquisition year (optional):', new Date().getFullYear().toString());
            
            const formData = new FormData();
            formData.append('additional_copies', count);
            if (acquisitionYear) {
                formData.append('acquisition_year', acquisitionYear);
            }
            
            fetch(`/books/${bookId}/add-copies`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to add copies'));
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Error adding copies: ' + err.message);
            });
        });

        // On page load, if category is custom (not in dropdown), add it as an option
        const currentCatValue = categorySelect.value;
        if (currentCatValue && currentCatValue !== 'other' && currentCatValue !== '') {
            const optionExists = Array.from(categorySelect.options).some(o => o.value === currentCatValue);
            if (!optionExists) {
                const opt = document.createElement('option');
                opt.value = currentCatValue;
                opt.text = currentCatValue;
                opt.selected = true;
                const otherOption = categorySelect.querySelector('option[value="other"]');
                categorySelect.insertBefore(opt, otherOption);
            }
        }

        categorySelect.addEventListener('change', toggleOther);
        otherInput.addEventListener('input', syncOtherCategory);

        // Handle form submission - ensure custom category is properly selected
        const formElement = document.querySelector('form');
        if (formElement) {
            formElement.addEventListener('submit', function(e) {
                const selectedValue = categorySelect.value;
                const customValue = otherInput.value.trim();
                
                // If "other" is selected, custom value is required
                if (selectedValue === 'other') {
                    if (!customValue) {
                        e.preventDefault();
                        otherInput.classList.add('is-invalid');
                        alert('Please enter a custom category');
                        return false;
                    }
                    // Find or create the option
                    let option = Array.from(categorySelect.options).find(o => o.value === customValue);
                    if (!option) {
                        const opt = document.createElement('option');
                        opt.value = customValue;
                        opt.textContent = customValue;
                        opt.text = customValue;
                        const otherOption = categorySelect.querySelector('option[value="other"]');
                        categorySelect.insertBefore(opt, otherOption);
                    }
                    // Select it
                    categorySelect.value = customValue;
                } else {
                    // If "other" is NOT selected, clear the custom value
                    otherInput.value = '';
                }
            });
        }

        toggleOther();
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Herd\library\resources\views/books/edit.blade.php ENDPATH**/ ?>