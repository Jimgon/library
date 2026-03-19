<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">


        <!-- Add Book Form -->
        <div class="col-md-8">
            <h4 class="mb-3">Add New Book</h4>
                <form action="<?php echo e(route('books.store')); ?>" method="POST" class="p-4">
                    <?php echo csrf_field(); ?>

                    
                    <div class="section-title mb-4">
                        <h6 class="text-uppercase fw-bold text-secondary">Basic Information</h6>
                        <hr>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                name="title" 
                                id="title" 
                                class="form-control <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                value="<?php echo e(old('title')); ?>"
                                style="text-transform: capitalize;"
                                required
                            >
                            <?php $__errorArgs = ['title'];
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
                            <label for="author" class="form-label">Author <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                name="author" 
                                id="author" 
                                class="form-control <?php $__errorArgs = ['author'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                value="<?php echo e(old('author')); ?>"
                                style="text-transform: capitalize;"
                                required
                            >
                            <?php $__errorArgs = ['author'];
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
                            <label for="publisher" class="form-label">Publisher</label>
                            <input 
                                type="text" 
                                name="publisher" 
                                id="publisher" 
                                class="form-control <?php $__errorArgs = ['publisher'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                value="<?php echo e(old('publisher')); ?>"
                                style="text-transform: capitalize;"
                            >
                            <?php $__errorArgs = ['publisher'];
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
                            <label for="isbn" class="form-label">ISBN <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                name="isbn" 
                                id="isbn" 
                                class="form-control <?php $__errorArgs = ['isbn'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                value="<?php echo e(old('isbn')); ?>"
                                placeholder="13 digit ISBN"
                                pattern="[0-9]{13}"
                                maxlength="13"
                                minlength="13"
                                inputmode="numeric"
                                required
                            >
                            <?php $__errorArgs = ['isbn'];
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

                    
                    <div class="section-title mb-4 mt-4">
                        <h6 class="text-uppercase fw-bold text-secondary">Classification & Cataloging</h6>
                        <hr>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category" id="category" class="form-select <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">-- Select Category --</option>
                                <?php $__currentLoopData = $allCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $catValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $catValue = trim($catValue); ?>
                                    <option value="<?php echo e($catValue); ?>" <?php echo e(old('category') === $catValue ? 'selected' : ''); ?>><?php echo e($catValue); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <option value="other" <?php echo e(old('category') === 'other' || (old('category') && !in_array(old('category'), $allCategories)) ? 'selected' : ''); ?>>Other</option>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const categorySelect = document.getElementById('category');
                                    const otherInput = document.getElementById('other_category');
                                    // Show the other input if needed on page load
                                    if (categorySelect.value === 'other') {
                                        otherInput.style.display = 'block';
                                        otherInput.required = true;
                                        otherInput.disabled = false;
                                    }
                                });
                            </script>
                            </select>
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
                            <input type="text" name="other_category" id="other_category" class="form-control mt-2 <?php $__errorArgs = ['other_category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Enter new category" value="<?php echo e(old('other_category')); ?>" style="display: none; text-transform: capitalize;">
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
                                value="<?php echo e(old('published_year')); ?>"
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

                    
                    <div class="section-title mb-4 mt-4">
                        <h6 class="text-uppercase fw-bold text-secondary">Physical Characteristics</h6>
                        <hr>
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
                                value="<?php echo e(old('pages')); ?>"
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
                                value="<?php echo e(old('edition')); ?>"
                                placeholder="e.g., 3rd Edition"
                                style="text-transform: capitalize;"
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
                                <option value="Brand New" <?php echo e(old('condition') === 'Brand New' ? 'selected' : ''); ?>>Brand New</option>
                                <option value="Old" <?php echo e(old('condition') === 'Old' ? 'selected' : ''); ?>>Old</option>
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

                      
                    
                    <div class="section-title mb-4 mt-4">
                        <h6 class="text-uppercase fw-bold text-secondary">Acquisition Information</h6>
                        <hr>
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
                                <option value="purchase" <?php echo e(old('acquisition_type') === 'purchase' ? 'selected' : ''); ?>>Purchase</option>
                                <option value="donation" <?php echo e(old('acquisition_type') === 'donation' ? 'selected' : ''); ?>>Donation</option>
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
                                value="<?php echo e(old('source_of_funds')); ?>"
                                placeholder="e.g., School Budget, PTA Fund"
                                style="text-transform: capitalize;"
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
                                value="<?php echo e(old('purchase_price')); ?>"
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

                    
                    <div class="section-title mb-4 mt-4">
                        <h6 class="text-uppercase fw-bold text-secondary">Copies Information</h6>
                        <hr>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="call_number" class="form-label">Control Number Base</label>
                            <input 
                                type="text" 
                                name="call_number" 
                                id="call_number" 
                                class="form-control" 
                                placeholder="Auto-generated"
                                value="<?php echo e($nextCtrlBase ?? '001'); ?>"
                                readonly
                            >
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="copies" class="form-label">Total Number of Copies <span class="text-danger">*</span></label>
                            <input 
                                type="number" 
                                name="copies" 
                                id="copies" 
                                class="form-control <?php $__errorArgs = ['copies'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                value="<?php echo e(old('copies', 1)); ?>"
                                min="1"
                                required
                            >
                            <?php $__errorArgs = ['copies'];
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
                                            <th style="width: 30%;">Acquisition Year</th>
                                            <th style="width: 30%;">Status</th>
                                            <th style="width: 20%;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="copiesContainer">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    
                    <div class="d-flex gap-2 justify-content-end mt-5">
                        <a href="<?php echo e(route('books.catalog')); ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Save Book
                        </button>
                    </div>
            </form>
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
        const isbnInput = document.getElementById('isbn');
        const pagesInput = document.getElementById('pages');
        const form = document.querySelector('form');

        // Validate that required elements exist
        if (!copiesContainer) {
            console.error('ERROR: copiesContainer element not found in DOM');
            return;
        }
        if (!form) {
            console.error('ERROR: form element not found in DOM');
            return;
        }

        // Filter ISBN to allow only numbers
        if (isbnInput) {
            isbnInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }

        // Filter Pages to allow only numbers
        if (pagesInput) {
            pagesInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }

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

        // keep an option in select when user types a new category
        function toAllCaps(str) {
            if (!str) return '';
            return str.toUpperCase();
        }

        function syncOtherCategory() {
            let val = otherInput.value.trim();
            if (!val) {
                categorySelect.value = 'other';
                return;
            }
            // Normalize to all caps
            val = toAllCaps(val);
            otherInput.value = val;
            // Check for existing option (case-insensitive)
            let existing = Array.from(categorySelect.options).find(o => o.value.toUpperCase() === val);
            if (!existing) {
                const opt = document.createElement('option');
                opt.value = val;
                opt.textContent = val;
                opt.text = val;
                // Add before "Other" option
                const otherOption = categorySelect.querySelector('option[value="other"]');
                categorySelect.insertBefore(opt, otherOption);
                categorySelect.value = val;
            } else {
                // If exists, always select the existing one (preserve original casing)
                categorySelect.value = existing.value;
                otherInput.value = existing.value;
                return;
            }
        }

        function generateBase() {
            let base = callNumberInput.value.trim();
            if (!base) {
                base = '001';
            }
            return base;
        }

        // Track the highest suffix ever used for the current base
        let maxCopySuffix = 0;
        function getNextControlNumber() {
            const base = generateBase();
            // Find all current and previously used suffixes
            let suffixes = [];
            const rows = copiesContainer.querySelectorAll('tr');
            rows.forEach(row => {
                const input = row.querySelector('input.ctrl-number');
                if (input) {
                    const val = input.value;
                    const parts = val.split('-');
                    if (parts.length === 2 && parts[0] === base) {
                        const num = parseInt(parts[1]);
                        if (!isNaN(num)) suffixes.push(num);
                    }
                }
            });
            // Also consider maxCopySuffix (in case of deleted rows)
            let maxSuffix = Math.max(maxCopySuffix, ...suffixes, 0);
            maxCopySuffix = maxSuffix + 1;
            return base + '-' + String(maxCopySuffix).padStart(3, '0');
        }

        // Reset maxCopySuffix if base changes
        callNumberInput.addEventListener('input', function() {
            maxCopySuffix = 0;
            updateControlNumbers();
        });

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
            // If no ctrlValue provided, generate next
            if (!ctrlValue) {
                ctrlValue = getNextControlNumber();
            } else {
                // Update maxCopySuffix if needed
                const parts = ctrlValue.split('-');
                if (parts.length === 2) {
                    const num = parseInt(parts[1]);
                    if (!isNaN(num) && num > maxCopySuffix) maxCopySuffix = num;
                }
            }
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" name="control_numbers[]" class="form-control form-control-sm ctrl-number" value="${ctrlValue}" readonly></td>
                <td><input type="number" name="copy_year[]" class="form-control form-control-sm copy-year-input" min="1900" max="2100" value="${yearValue}" placeholder="Enter year"></td>
                <td><input type="text" name="copy_status[]" class="form-control form-control-sm" value="available" readonly></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeCopyBtn">&times;</button></td>
            `;
            copiesContainer.appendChild(row);
            copiesInput.value = copiesContainer.querySelectorAll('tr').length;

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

            row.querySelector('.removeCopyBtn').addEventListener('click', function(e) {
                e.preventDefault();
                // Don't allow removing if it's the last copy
                const remainingRows = copiesContainer.querySelectorAll('tr').length;
                if (remainingRows <= 1) {
                    alert('You must have at least one copy. Cannot remove.');
                    return;
                }
                row.remove();
                copiesInput.value = copiesContainer.querySelectorAll('tr').length;
                // Do not decrement maxCopySuffix so deleted numbers are not reused
            });
        }

        addCopyBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const nextCtrl = getNextControlNumber();
            const currentYear = new Date().getFullYear().toString();
            addCopyRow(nextCtrl, currentYear);
        });

        // Handle form submission - ensure custom category is properly selected and copies exist
        form.addEventListener('submit', function(e) {
            const selectedValue = categorySelect.value;
            const customValue = otherInput.value.trim();

            // Check if at least one copy row exists
            const copyRows = copiesContainer.querySelectorAll('tr');
            if (copyRows.length === 0) {
                e.preventDefault();
                alert('Error: You must add at least one copy. Please add a copy row before saving.');
                return false;
            }

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
                    alert('Error: Please enter a category name for "Other".');
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
                // Disable and clear the other_category input so only 'category' is submitted
                otherInput.disabled = true;
                otherInput.value = '';
                // Ensure the select's value is the custom value for submission
                setTimeout(function() {
                    categorySelect.value = customValue;
                }, 0);
            } else {
                // If "other" is NOT selected, clear the custom value
                otherInput.value = '';
                otherInput.disabled = true;
            }
        });

        // new listeners
        categorySelect.addEventListener('change', toggleOther);
        otherInput.addEventListener('input', syncOtherCategory);
        callNumberInput.addEventListener('input', updateControlNumbers);

        // initialize with copies number if user set one manually
        copiesInput.addEventListener('change', function() {
            const desired = parseInt(copiesInput.value) || 0;
            const current = copiesContainer.querySelectorAll('tr').length;
            const currentYear = new Date().getFullYear().toString();
            if (desired > current) {
                for (let i = current; i < desired; i++) {
                    const nextCtrl = getNextControlNumber();
                    addCopyRow(nextCtrl, currentYear);
                }
            } else if (desired < current) {
                // Only allow removal if at least one remains
                if (desired >= 1) {
                    for (let i = current; i > desired; i--) {
                        const rows = copiesContainer.querySelectorAll('tr');
                        if (rows.length > 0) {
                            rows[rows.length - 1].remove();
                        }
                    }
                } else {
                    // Reset to minimum 1
                    copiesInput.value = 1;
                    alert('You must have at least one copy.');
                }
            }
        });

        // initialize rows on page load with auto-incremented control numbers
        try {
            const initialCopies = parseInt(copiesInput.value) || 1;
            const currentYear = new Date().getFullYear().toString();
            for (let i = 0; i < initialCopies; i++) {
                const nextCtrl = getNextControlNumber();
                addCopyRow(nextCtrl, currentYear);
            }
        } catch (err) {
            console.error('Error initializing copy rows:', err);
            alert('Error: Could not initialize copy rows. Please refresh the page.');
        }

        // run initial toggle
        toggleOther();
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Herd\library\resources\views/books/create.blade.php ENDPATH**/ ?>