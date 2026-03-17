  

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
                                        <?php $__currentLoopData = $book->control_numbers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $copyYears = $book->copy_years ?? [];
                                                $copyYear = isset($copyYears[$loop->index]) ? $copyYears[$loop->index] : '';
                                                $copyConditions = $book->copy_conditions ?? [];
                                                $copyCondition = isset($copyConditions[$loop->index]) ? $copyConditions[$loop->index] : 'Brand New';
                                            ?>
                                            <tr>
                                                <td><input type="text" name="control_numbers[]" class="form-control form-control-sm ctrl-number" value="<?php echo e($cn); ?>" readonly></td>
                                                <td><input type="number" name="copy_year[]" class="form-control form-control-sm copy-year-input" min="1900" max="2100" value="<?php echo e($copyYear); ?>" placeholder="Enter year"></td>
                                                <td><input type="text" name="copy_status[]" class="form-control form-control-sm" value="available"></td>
                                                <td>
                                                    <select name="copy_condition[]" class="form-select form-select-sm">
                                                        <option value="Brand New" <?php echo e($copyCondition == 'Brand New' ? 'selected' : ''); ?>>Brand New</option>
                                                        <option value="Old" <?php echo e($copyCondition == 'Old' ? 'selected' : ''); ?>>Old</option>
                                                    </select>
                                                </td>
                                                <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeCopyBtn">&times;</button></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
        const callNumberInput = document.getElementById('call_number');
        const copiesInput = document.getElementById('copies');

        function toggleOther() {
            if (categorySelect.value === 'other') {
                otherInput.style.display = 'block';
                otherInput.required = true;
                otherInput.disabled = false;
            } else {
                otherInput.style.display = 'none';
                otherInput.required = false;
                otherInput.disabled = true;
                // clear the input when hiding
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

        function generateBase() {
            let base = callNumberInput.value.trim();
            if (!base) {
                base = String(Math.floor(Math.random() * 1000)).padStart(3, '0');
            }
            return base;
        }

        function updateControlNumbers() {
            const base = generateBase();
            const rows = copiesContainer.querySelectorAll('tr');
            rows.forEach((row, idx) => {
                const input = row.querySelector('input.ctrl-number');
                if (input) {
                    input.value = base + '-' + String(idx + 1).padStart(3, '0');
                }
            });
        }

        function addCopyRow(ctrlValue = '', yearValue = '') {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" name="control_numbers[]" class="form-control form-control-sm ctrl-number" value="${ctrlValue}" readonly></td>
                <td><input type="number" name="copy_year[]" class="form-control form-control-sm copy-year-input" min="1900" max="2100" value="${yearValue}" placeholder="Enter year"></td>
                <td><input type="text" name="copy_status[]" class="form-control form-control-sm" value="available"></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeCopyBtn">&times;</button></td>
            `;
            copiesContainer.appendChild(row);
            copiesInput.value = copiesContainer.querySelectorAll('tr').length;
            updateControlNumbers();

            // Add event listener to auto-fill other rows
            const yearInput = row.querySelector('.copy-year-input');
            yearInput.addEventListener('input', function() {
                const yearValue = this.value;
                // Fill all copy_year inputs with the same value
                const allYearInputs = copiesContainer.querySelectorAll('.copy-year-input');
                allYearInputs.forEach(input => {
                    input.value = yearValue;
                });
            });

            row.querySelector('.removeCopyBtn').addEventListener('click', function() {
                row.remove();
                copiesInput.value = copiesContainer.querySelectorAll('tr').length;
                updateControlNumbers();
            });
        }

        addCopyBtn.addEventListener('click', function() {
            const currentYear = new Date().getFullYear().toString();
            addCopyRow('', currentYear);
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
        callNumberInput.addEventListener('input', updateControlNumbers);


        // Add copies instead of replacing
        function handleAddCopies() {
            const toAdd = parseInt(copiesInput.value) || 0;
            if (toAdd > 0) {
                const current = copiesContainer.querySelectorAll('tr').length;
                const currentYear = new Date().getFullYear().toString();
                for (let i = 0; i < toAdd; i++) {
                    addCopyRow('', currentYear);
                }
                // Reset input to 0 after adding
                copiesInput.value = 0;
            }
        }
        copiesInput.addEventListener('change', handleAddCopies);
        copiesInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                handleAddCopies();
            }
        });

        // if there are existing rows already rendered (from Blade), sync copies input and update ctrl numbers
        if (copiesContainer.querySelectorAll('tr').length > 0) {
            copiesInput.value = copiesContainer.querySelectorAll('tr').length;
            updateControlNumbers();
            
            // Fill empty year fields with current year and add event listeners
            const currentYear = new Date().getFullYear().toString();
            const yearInputs = copiesContainer.querySelectorAll('.copy-year-input');
            yearInputs.forEach(yearInput => {
                // Fill empty fields with current year
                if (!yearInput.value || yearInput.value.trim() === '') {
                    yearInput.value = currentYear;
                }
                
                // Add event listener to auto-fill other rows
                yearInput.addEventListener('input', function() {
                    const yearValue = this.value;
                    // Query fresh each time to get all inputs including dynamically added ones
                    const allYearInputs = copiesContainer.querySelectorAll('.copy-year-input');
                    allYearInputs.forEach(input => {
                        input.value = yearValue;
                    });
                });
            });
            
            // Add event listeners to remove buttons
            copiesContainer.querySelectorAll('.removeCopyBtn').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.closest('tr').remove();
                    copiesInput.value = copiesContainer.querySelectorAll('tr').length;
                    updateControlNumbers();
                });
            });
        }

        // Handle form submission - ensure custom category is properly selected and years are filled
        const formElement = document.querySelector('form');
        if (formElement) {
            formElement.addEventListener('submit', function(e) {
                const selectedValue = categorySelect.value;
                const customValue = otherInput.value.trim();
                
                // Ensure all copy year inputs have values (fill empty with current year)
                const yearInputs = copiesContainer.querySelectorAll('input[name="copy_year[]"]');
                const currentYear = new Date().getFullYear().toString();
                yearInputs.forEach(input => {
                    if (!input.value || input.value.trim() === '') {
                        input.value = currentYear;
                    }
                });
                
                // If "other" is selected, custom value is required
                if (selectedValue === 'other') {
                    if (!customValue) {
                        e.preventDefault();
                        otherInput.classList.add('is-invalid');
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