<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <style>
            /* Compact neutral UI */
            .container.py-5 { max-width: 1400px; width: 100%; }
            .card { border-radius: .6rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04); border: 1px solid #e9f0fb; background: #ffffff; }
            .card-body { padding: 1.5rem !important; }
            h4 { color: #0f172a; font-weight:600; font-size:1.05rem; margin-bottom:.35rem; }
            p.text-muted { color: #64748b; margin-bottom:.6rem; font-size:.9rem; }
            .form-label { font-size: .86rem; font-weight:500; color:#0f172a; }
            .form-control, .form-select { border-radius: .55rem; border: 1px solid #eaf3ff; background: #fbfdff; transition: all .12s ease; box-shadow: none; padding:.85rem 1.05rem; font-size:1.08rem; }
            .form-control:focus, .form-select:focus { outline: none; border-color: #93c5fd; box-shadow: 0 0 0 4px rgba(59,130,246,0.05); background: #fff; }
            .btn { border-radius: .5rem; transition: transform .06s ease, box-shadow .06s ease, background-color .12s ease; padding: .45rem .75rem; font-size:.92rem; }
            .btn:hover { transform: translateY(-1px); }
            .btn-primary { background: #3B82F6; border-color: #3B82F6; box-shadow: 0 6px 12px rgba(59,130,246,0.10); color: #fff; }
            .btn-secondary { background: #f7fafc; border-color: #eef6ff; color: #111827; }
            .btn-outline-primary { border-color: #93c5fd; color: #3b82f6; background: transparent; transition: all .12s ease; }
            .btn-outline-primary:hover { background: #eff6ff; border-color: #3b82f6; }
            .btn-outline-primary.active { background: #3b82f6; color: #fff; border-color: #3b82f6; }
            #cartList ul { padding-left: 0; margin:0; }
            #cartList li { transition: background .12s ease, transform .12s ease; padding: .45rem; border-radius: .45rem; margin-bottom: .4rem; background: #fff; border:1px solid #f4f7fb; display:flex; justify-content:space-between; align-items:center; }
            .nav-pills { background: #f1f5f9; border-radius: .85rem; padding: .35rem; gap: .35rem; }
            .nav-pills .nav-link { color: #0f172a; border-radius: .7rem; font-weight: 500; padding: .6rem 1rem; }
            .nav-pills .nav-link.active { background-color: #0f172a; color: #fff; }
            #cartList li:hover { background: #f8fbff; transform: translateY(-1px); }
            #cartList .btn-outline-danger { border-color: transparent; color: #ef4444; background: transparent; padding: .2rem .45rem; }
            .badge.bg-secondary { background: #64748b; color: #fff; padding: .3rem .45rem; border-radius: .45rem; font-size:.85rem; }
            .section-block { background: #f8fafc; border: 1px solid #eef2f7; border-radius: .9rem; padding: 1rem; }
            .section-title { font-weight: 750; color: #0f172a; font-size: .98rem; margin: 0; }
            .section-subtitle { color: #64748b; font-size: .9rem; margin: .15rem 0 0; }
            /* Select2 tweaks */ 
            .select2-container .select2-selection--single { height: calc(1.75em + 1.7rem + 2px); border-radius: .55rem; border:1px solid #eaf3ff; background:#fbfdff; font-size:1.08rem; display:flex; align-items:center; padding: 0 1.05rem; }
            .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 1.75; color: #0f172a; padding-left: 0; padding-right: 2.4rem; }
            .select2-container--default .select2-selection--single .select2-selection__arrow { height: 100%; right: .45rem; }
            .select2-container--default .select2-selection--single .select2-selection__placeholder { color: #64748b; }
            .select2-container { width: 100% !important; }
            .btn-add-book { min-width: 110px; white-space: normal; }
            @media (max-width: 767px) { .container.py-5 { padding-left: 1rem; padding-right:1rem; } .card-body { padding: .85rem !important; } }
        </style>
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <div>
                <h4 class="mb-1 not-odd:">Book Borrowing</h4>
                <p class="text-muted mb-0">Issue books to students and teachers</p>
            </div>
            <div class="d-flex align-items-center">
                <a href="<?php echo e(route('borrow.distribute')); ?>" class="btn btn-sm btn-dark">Bulk Distribution</a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="container py-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4">

                        
                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                                <strong>Error!</strong>
                                <ul class="mb-0 mt-2">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if(session('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                                <?php echo e(session('success')); ?>

                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if(session('warning')): ?>
                            <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
                                <?php echo e(session('warning')); ?>

                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Tabs for Student/Teacher -->
                        <ul class="nav nav-pills mb-4" id="borrowTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="student-tab" data-bs-toggle="tab" data-bs-target="#student-borrow" type="button" role="tab" aria-controls="student-borrow" aria-selected="true">
                                    Student Borrowing
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="teacher-tab" data-bs-toggle="tab" data-bs-target="#teacher-borrow" type="button" role="tab" aria-controls="teacher-borrow" aria-selected="false">
                                    Teacher Borrowing
                                </button>
                            </li>
                        </ul>

                        <form id="borrowForm" action="<?php echo e(route('borrow.store')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="borrow_type" id="borrowTypeField" value="student">
                            <input type="hidden" name="user_id" id="userIdField" value="">
                            <input type="hidden" name="borrowed_at" id="borrowedAtField" value="">
                            <input type="hidden" name="due_date" id="dueDateField" value="">

                        <div class="tab-content" id="borrowTabContent">
                            <!-- Student Tab -->
                            <div class="tab-pane fade show active" id="student-borrow" role="tabpanel" aria-labelledby="student-tab">

                            <!-- ================= STUDENT INFO ================= -->
                            <div class="section-block mb-4">
                                <div class="mb-3">
                                    <p class="section-title">Student Information</p>
                                    <p class="section-subtitle">Search student by their name to borrow books.</p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label mb-1">Select Student</label>
                                    <select id="student_select" class="form-select">
                                        <option value="" selected disabled>Select student...</option>
                                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($user->_id ?? $user->id); ?>"
                                                            data-first="<?php echo e($user->first_name ?? ''); ?>"
                                                            data-last="<?php echo e($user->last_name ?? ''); ?>"
                                                            data-lrn="<?php echo e($user->lrn ?? ''); ?>"
                                                            data-grade_section="<?php echo e($user->grade_section ?? $user->year_level ?? ''); ?>"
                                                            data-address="<?php echo e($user->address ?? $user->course ?? ''); ?>">
                                                <?php echo e(trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''))); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label mb-1">LRN</label>
                                        <input id="student_id_display" type="text" class="form-control" placeholder="N/A" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label mb-1">Student Name</label>
                                        <input id="student_name_display" type="text" class="form-control" placeholder="N/A" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label mb-1">Grade & Section</label>
                                        <input id="student_year_display" type="text" class="form-control" placeholder="N/A" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label mb-1">Address</label>
                                        <input id="student_course_display" type="text" class="form-control" placeholder="N/A" readonly>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label mb-1">Borrow Date</label>
                                        <input type="date" id="student_borrow_date" name="student_borrow_date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label mb-1">Due Date</label>
                                        <input type="date" id="student_due_date" name="student_due_date" class="form-control" value="<?php echo e(date('Y-m-d', strtotime('+3 days'))); ?>">
                                    </div>
                                </div>
                            </div>
                            </div>

                            <!-- Teacher Tab -->
                            <div class="tab-pane fade" id="teacher-borrow" role="tabpanel" aria-labelledby="teacher-tab">

                            <!-- ================= TEACHER INFO ================= -->
                            <div class="section-block mb-4">
                                <div class="mb-3">
                                    <p class="section-title">Teacher Information</p>
                                    <p class="section-subtitle">Search teacher by their name to borrow books.</p>
                                    <p class="section-subtitle"><em>Teachers can borrow up to 3 books for personal use. For bulk/distribution, use the distribution page instead.</em></p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label mb-1">Select Teacher</label>
                                    <?php if(isset($teachers) && $teachers->isEmpty()): ?>
                                        <div class="alert alert-warning">No teachers available.</div>
                                    <?php endif; ?>
                                    <select id="teacher_select" class="form-select">
                                        <option value="" selected disabled>Select teacher...</option>
                                        <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($user->_id ?? $user->id); ?>">
                                                <?php echo e($user->name ?? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''))); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label mb-1">Borrow Date</label>
                                        <input type="date" id="teacher_borrow_date" name="teacher_borrow_date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label mb-1">Due Date</label>
                                        <input type="date" id="teacher_due_date" name="teacher_due_date" class="form-control" value="<?php echo e(date('Y-m-d', strtotime('+12 months'))); ?>">
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- ================= BOOK INFO ================= -->
                        <div class="section-block mb-4">
                            <div class="mb-3">
                                <p class="section-title">Book Information</p>
                                <p class="section-subtitle">Search and select a book to borrow.</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label mb-1">Select Book</label>
                                <div class="row g-2 align-items-stretch">
                                    <div class="col-12 col-md">
                                    <select id="book_select" class="form-select w-100">
                                        <option value="" selected disabled>Select an option...</option>
                                        <?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                // Get accurate counts from the book model accessors
                                                $available = $book->available_copies;
                                                $total = $book->total_copies;
                                                // Use BookCopy as source of truth for copy tracking
                                                $availableCtrls = $book->copies()
                                                    ->where('status', 'available')
                                                    ->where('is_lost_damaged', false)
                                                    ->whereNotNull('control_number')
                                                    ->orderBy('control_number')
                                                    ->pluck('control_number')
                                                    ->toArray();
                                                $untrackedAvailable = $book->copies()
                                                    ->where('status', 'available')
                                                    ->where('is_lost_damaged', false)
                                                    ->whereNull('control_number')
                                                    ->count();
                                            ?>
                                            <option value="<?php echo e($book->_id ?? $book->id); ?>"
                                                            data-title="<?php echo e($book->title); ?>"
                                                            data-author="<?php echo e($book->author ?? ''); ?>"
                                                            data-publisher="<?php echo e($book->publisher ?? ''); ?>"
                                                            data-isbn="<?php echo e($book->isbn ?? ''); ?>"
                                                            data-available-copies="<?php echo e($available); ?>"
                                                            data-total-copies="<?php echo e($total); ?>"
                                                            data-control-numbers='<?php echo json_encode($availableCtrls, 15, 512) ?>'
                                                            data-untracked-available="<?php echo e($untrackedAvailable); ?>">
                                                <?php echo e($book->title); ?> (<?php echo e($available); ?>/<?php echo e($total); ?> available)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    </div>
                                    <div class="col-12 col-md-auto d-grid">
                                        <button id="addBookBtn" type="button" class="btn btn-secondary btn-add-book">Add Book</button>
                                    </div>
                                </div>
                                <div id="stockWarning" class="text-danger small mt-2" style="display: none;">This book is out of stock.</div>
                            </div>

                            <!-- Control Number Selection -->
                            <div class="mb-3" id="controlNumberSection">
                                <label class="form-label mb-1">Select Copy (Ctrl#)</label>
                                <select id="controlNumberSelect" class="form-select" disabled>
                                    <option value="" disabled selected>Select a copy...</option>
                                </select>
                                <div class="section-subtitle" id="ctrlCopyHelp">Select a copy to borrow.</div>
                            </div>

                            <div class="row g-3 mb-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label mb-1">Title</label>
                                    <input id="book_title" type="text" class="form-control" placeholder="N/A" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label mb-1">Author</label>
                                    <input id="book_author" type="text" class="form-control" placeholder="N/A" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label mb-1">Publisher</label>
                                    <input id="book_publisher" type="text" class="form-control" placeholder="N/A" readonly>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button id="confirmBtn" type="submit" class="btn btn-primary">Confirm (0)</button>
                            </div>
                        </div>

                        <!-- ================= BOOK LIST ================= -->
                        <div class="section-block">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <p class="section-title mb-0">Books to Borrow</p>
                                <span id="cartCount" class="badge bg-secondary">0</span>
                            </div>

                            <div id="cartList" class="text-muted small">
                                No books added yet.
                            </div>
                        </div>

                            <!-- hidden container for selected book ids -->
                            <div id="hiddenInputs"></div>
                        </form>


                    </div>
                </div>
            </div>

            <!-- jQuery + Select2 JS for searchable selects -->
            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

            <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Initialize Select2
                if (window.jQuery) {
                    $('#student_select').select2({ width: '100%', placeholder: 'Select student...' });
                    $('#teacher_select').select2({ width: '100%', placeholder: 'Select teacher...' });

                    const bookMatcher = function (params, data) {
                        const term = $.trim(params.term || '');
                        if (term === '') return data;
                        if (!data || !data.element) return null;

                        const el = data.element;
                        const haystack = [
                            data.text,
                            el.getAttribute('data-title'),
                            el.getAttribute('data-author'),
                            el.getAttribute('data-publisher'),
                            el.getAttribute('data-isbn'),
                        ]
                            .filter(Boolean)
                            .join(' ')
                            .toLowerCase();

                        return haystack.includes(term.toLowerCase()) ? data : null;
                    };

                    const normalizeCtrl = function (value) {
                        return String(value || '').replace(/[^0-9]/g, '');
                    };

                    const ctrlMatcher = function (params, data) {
                        const term = $.trim(params.term || '');
                        if (term === '') return data;
                        const normalizedTerm = normalizeCtrl(term);
                        const normalizedText = normalizeCtrl(data.text);
                        return normalizedText.includes(normalizedTerm) ? data : null;
                    };

                    $('#book_select').select2({ width: '100%', placeholder: 'Select a book...', matcher: bookMatcher });
                    $('#controlNumberSelect').select2({
                        width: '100%',
                        placeholder: 'Select a copy...',
                        matcher: ctrlMatcher,
                        minimumResultsForSearch: 0,
                    });

                    // Restrict Ctrl# search input only (do not affect other Select2 inputs)
                    $('#controlNumberSelect').on('select2:open', function () {
                        const searchField = document.querySelector('.select2-container--open .select2-search__field');
                        if (!searchField) return;
                        if (searchField.dataset.ctrlFilterAttached === '1') return;
                        searchField.dataset.ctrlFilterAttached = '1';
                        searchField.addEventListener('input', function () {
                            this.value = this.value.replace(/[^0-9-]/g, '');
                        });
                    });
                }

                const studentSelect = document.getElementById('student_select');
                const studentIdDisplay = document.getElementById('student_id_display');
                const studentNameDisplay = document.getElementById('student_name_display');
                const studentYearDisplay = document.getElementById('student_year_display');
                const studentCourseDisplay = document.getElementById('student_course_display');

                const teacherSelect = document.getElementById('teacher_select');

                const bookSelect = document.getElementById('book_select');
                const bookTitle = document.getElementById('book_title');
                const bookAuthor = document.getElementById('book_author');
                const bookPublisher = document.getElementById('book_publisher');
                const bookCtrlNumber = document.getElementById('book_ctrl_number');
                const controlNumberSection = document.getElementById('controlNumberSection');
                const controlNumberSelect = document.getElementById('controlNumberSelect');

                const addBookBtn = document.getElementById('addBookBtn');
                const confirmBtn = document.getElementById('confirmBtn');
                const cartList = document.getElementById('cartList');
                const cartCount = document.getElementById('cartCount');
                const hiddenInputs = document.getElementById('hiddenInputs');

                let cart = [];
                let selectedControlNumbers = [];

                function renderCart() {
                    cartCount.textContent = cart.length;
                    confirmBtn.textContent = `Confirm (${cart.length})`;
                    hiddenInputs.innerHTML = '';

                    if (cart.length === 0) {
                        cartList.innerHTML = '<div class="text-muted small">No books added yet.</div>';
                        return;
                    }

                    const list = document.createElement('ul');
                    list.className = 'list-unstyled mb-0';
                    cart.forEach((item, idx) => {
                        const li = document.createElement('li');
                        li.className = 'd-flex justify-content-between align-items-center py-2 border-bottom';
                        li.innerHTML = `<div><strong>${item.title}</strong><div class="small text-muted">${item.author} • ${item.publisher}</div><div class="small" style="color: #3b82f6; font-weight: 500;">Ctrl#: ${item.controlNumber}</div></div>`;
                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'btn btn-sm btn-outline-danger ms-2';
                        removeBtn.textContent = 'Remove';
                        removeBtn.addEventListener('click', () => { cart.splice(idx,1); renderCart(); });
                        li.appendChild(removeBtn);
                        list.appendChild(li);

                        // hidden inputs
                        const bookInput = document.createElement('input');
                        bookInput.type = 'hidden';
                        bookInput.name = 'book_ids[]';
                        bookInput.value = item.id;
                        hiddenInputs.appendChild(bookInput);

                        const ctrlInput = document.createElement('input');
                        ctrlInput.type = 'hidden';
                        ctrlInput.name = 'copy_numbers[]';
                        ctrlInput.value = item.controlNumber;
                        hiddenInputs.appendChild(ctrlInput);
                    });

                    cartList.innerHTML = '';
                    cartList.appendChild(list);
                }

                // build lookup maps for students and books (so other selects can populate details too)
                const studentMap = {};
                document.querySelectorAll('#student_select option').forEach(opt => {
                    if (!opt.value) return;
                    studentMap[opt.value] = {
                        first: opt.dataset.first || '',
                        last: opt.dataset.last || '',
                        lrn: opt.dataset.lrn || '',
                        grade_section: opt.dataset.grade_section || '',
                        address: opt.dataset.address || ''
                    };
                });

                function populateStudentDetailsById(id){
                    const data = studentMap[id];
                    if (!data) return;
                    studentIdDisplay.value = data.lrn || 'N/A';
                    studentNameDisplay.value = (data.first || '') + ' ' + (data.last || '');
                    studentYearDisplay.value = data.grade_section || 'N/A';
                    studentCourseDisplay.value = data.address || 'N/A';
                }

                studentSelect?.addEventListener('change', function(e){ populateStudentDetailsById(this.value); });

                // If legacy select exists (#user_id), keep it in sync
                const legacyUserSelect = document.getElementById('user_id');
                if (legacyUserSelect) {
                    legacyUserSelect.addEventListener('change', function(){
                        // try to sync selects and populate
                        const val = this.value;
                        if (studentSelect && studentSelect.value !== val) {
                            studentSelect.value = val;
                            if (window.jQuery) $('#student_select').trigger('change');
                        }
                        populateStudentDetailsById(val);
                    });
                }

                // listen to Select2 selection events to ensure details populate when using the widget
                if (window.jQuery) {
                    $('#student_select').on('select2:select', function(){ populateStudentDetailsById(this.value); });
                }

                // build book map
                const bookMap = {};
                document.querySelectorAll('#book_select option').forEach(opt => {
                    if (!opt.value) return;
                    bookMap[opt.value] = {
                        title: opt.dataset.title || '',
                        author: opt.dataset.author || '',
                        publisher: opt.dataset.publisher || '',
                        controlNumbers: opt.dataset.controlNumbers ? JSON.parse(opt.dataset.controlNumbers) : [],
                        untrackedAvailable: parseInt(opt.dataset.untrackedAvailable || 0, 10) || 0
                    };
                });

                // Initialize button as disabled
                addBookBtn.disabled = true;

                function updateAddBookButtonState() {
                    const bookId = bookSelect?.value || '';
                    const bookData = bookMap[bookId];
                    const availableCopies = parseInt(bookSelect?.selectedOptions?.[0]?.dataset?.availableCopies || 0, 10) || 0;

                    if (!bookId || !bookData || availableCopies < 1) {
                        addBookBtn.disabled = true;
                        return;
                    }

                    if (bookData.controlNumbers && bookData.controlNumbers.length > 0) {
                        addBookBtn.disabled = !controlNumberSelect?.value;
                    } else {
                        addBookBtn.disabled = false;
                    }
                }

                function populateBookDetailsById(id){
                    const data = bookMap[id];
                    selectedControlNumbers = [];
                    // Always show the section
                    controlNumberSection.style.display = 'block';
                    const ctrlCopyHelp = document.getElementById('ctrlCopyHelp');
                    if (!data || !id) {
                        bookTitle.value = '';
                        bookAuthor.value = '';
                        bookPublisher.value = '';
                        // Reset control number select
                        controlNumberSelect.innerHTML = '<option value="" disabled selected>Select a copy...</option>';
                        controlNumberSelect.disabled = true;
                        addBookBtn.disabled = true;
                        if (ctrlCopyHelp) ctrlCopyHelp.textContent = 'Select a copy to borrow.';
                        return;
                    }
                    bookTitle.value = data.title || '';
                    bookAuthor.value = data.author || '';
                    bookPublisher.value = data.publisher || '';
                    // Show control numbers - filter out borrowed ones and lost ones
                    if (data.controlNumbers && data.controlNumbers.length > 0) {
                        const availableCtrls = data.controlNumbers.filter(ctrl => ctrl);
                        controlNumberSelect.innerHTML = '<option value="" disabled selected>Select a copy...</option>';
                        if (availableCtrls.length > 0) {
                            availableCtrls.forEach((ctrl) => {
                                const option = document.createElement('option');
                                option.value = ctrl;
                                option.textContent = ctrl;
                                controlNumberSelect.appendChild(option);
                            });
                            controlNumberSelect.disabled = false;
                            addBookBtn.disabled = true; // Require control number selection
                            if (ctrlCopyHelp) ctrlCopyHelp.textContent = 'Select a copy to borrow.';
                            // Reinitialize Select2 with new options
                            if (window.jQuery) {
                                $('#controlNumberSelect').val('').trigger('change');
                            }
                            updateAddBookButtonState();
                        } else {
                            controlNumberSelect.disabled = true;
                            addBookBtn.disabled = true;
                            if (ctrlCopyHelp) ctrlCopyHelp.textContent = 'No available copies for this book.';
                        }
                    } else {
                        // No control numbers for this book
                        controlNumberSelect.innerHTML = '<option value="" disabled selected>No copy tracking</option>';
                        controlNumberSelect.disabled = true;
                        addBookBtn.disabled = false;
                        const availableCopies = parseInt(bookSelect?.selectedOptions?.[0]?.dataset?.availableCopies || 0, 10) || 0;
                        const untracked = data?.untrackedAvailable || 0;
                        if (ctrlCopyHelp) {
                            if (availableCopies > 0 && untracked > 0) {
                                ctrlCopyHelp.textContent = 'Copies exist but Ctrl# is not assigned yet. Assign control numbers in Books > Edit to enable copy tracking.';
                            } else {
                                ctrlCopyHelp.textContent = 'This book does not use copy numbers.';
                            }
                        }
                        updateAddBookButtonState();
                    }
                }

                // Enable Add Book button when control number is selected
                controlNumberSelect?.addEventListener('change', function() {
                    updateAddBookButtonState();
                });

                bookSelect?.addEventListener('change', function(e){
                    const availableCopies = parseInt(this.selectedOptions[0]?.dataset.availableCopies || 0);
                    const stockWarning = document.getElementById('stockWarning');
                    if (availableCopies < 1) {
                        stockWarning.style.display = 'block';
                        addBookBtn.disabled = true;
                    } else {
                        stockWarning.style.display = 'none';
                    }
                    populateBookDetailsById(this.value);
                    updateAddBookButtonState();
                });

                // If legacy select exists (#book_id), keep it in sync
                const legacyBookSelect = document.getElementById('book_id');
                if (legacyBookSelect) {
                    legacyBookSelect.addEventListener('change', function(){
                        const val = this.value;
                        if (bookSelect && bookSelect.value !== val) {
                            bookSelect.value = val;
                            if (window.jQuery) $('#book_select').trigger('change');
                        }
                        populateBookDetailsById(val);
                    });
                }

                if (window.jQuery) {
                    $('#controlNumberSelect').on('select2:select select2:clear', function(){
                        updateAddBookButtonState();
                    });
                    $('#book_select').on('select2:select', function(){ 
                        populateBookDetailsById(this.value); 
                        updateAddBookButtonState();
                    });
                    $('#book_select').on('select2:unselecting', function(){
                        addBookBtn.disabled = true;
                        bookTitle.value = '';
                        bookAuthor.value = '';
                        bookPublisher.value = '';
                    });
                }

                addBookBtn?.addEventListener('click', function(e){
                    e.preventDefault();
                    const bookId = bookSelect.value;
                    if (!bookId) {
                        alert('Please select a book.');
                        return;
                    }
                    const bookData = bookMap[bookId];
                    const availableCopies = parseInt(bookSelect.selectedOptions[0]?.dataset.availableCopies || 0);

                    if (availableCopies < 1) {
                        alert(`${bookData.title || 'This book'} is out of stock.`);
                        return;
                    }

                    if (!bookData) {
                        alert('Book data not found.');
                        return;
                    }

                    // Get all selected copies
                    let ctrlsToAdd = [];
                    if (bookData.controlNumbers && bookData.controlNumbers.length > 0) {
                        // Single control number select
                        const selectedCtrl = controlNumberSelect.value;
                        if (!selectedCtrl) {
                            alert('Please select a copy (Ctrl#) before adding.');
                            return;
                        }
                        ctrlsToAdd = [selectedCtrl];
                    } else {
                        ctrlsToAdd = ['N/A'];
                    }

                    // Check if total would exceed limit based on user type
                    let isTeacherTab = document.getElementById('teacher-tab')?.classList.contains('active');
                    const maxBooks = isTeacherTab ? 3 : 3; // Teachers: 3 books, Students: 3 books
                    if (cart.length + ctrlsToAdd.length > maxBooks) {
                        const userType = isTeacherTab ? 'teacher' : 'student';
                        alert(`You can only add up to ${maxBooks} books for ${userType} borrowing. Currently have ${cart.length} book(s), trying to add ${ctrlsToAdd.length} more.`);
                        return;
                    }

                    // Add each selected copy to cart
                    ctrlsToAdd.forEach(ctrl => {
                        // Check if this specific copy is already in cart
                        if (cart.some(c => c.id === bookId && c.controlNumber === ctrl)) {
                            alert(`Copy ${ctrl} of "${bookData.title}" is already in the list.`);
                            return;
                        }

                        cart.push({ 
                            id: bookId, 
                            title: bookData.title || '', 
                            author: bookData.author || '', 
                            publisher: bookData.publisher || '', 
                            controlNumber: ctrl
                        });
                    });

                    bookSelect.value = '';
                    controlNumberSelect.value = '';
                    // Do NOT hide the control number section; just reset and disable the dropdown
                    controlNumberSelect.innerHTML = '<option value="" disabled selected>Select a copy...</option>';
                    // Reinitialize Select2
                    if (window.jQuery) {
                        $('#controlNumberSelect').val('').trigger('change');
                    }
                    controlNumberSelect.disabled = true;
                    if (window.jQuery) $('#book_select').val('').trigger('change');
                    selectedControlNumbers = [];
                    renderCart();
                });

                const borrowForm = document.getElementById('borrowForm');

                borrowForm?.addEventListener('submit', function(e){
                    e.preventDefault();
                    console.log('Form submit event triggered');

                    // Determine which tab is active by checking the active nav link
                    const studentTabButton = document.getElementById('student-tab');
                    const teacherTabButton = document.getElementById('teacher-tab');
                    const isStudentTab = studentTabButton?.classList.contains('active');
                    const isTeacherTab = teacherTabButton?.classList.contains('active');

                    console.log('isStudentTab:', isStudentTab, 'isTeacherTab:', isTeacherTab);

                    // Get selected user based on active tab
                    let selectedUserId = '';
                    let borrowType = '';

                    if (isStudentTab) {
                        selectedUserId = studentSelect?.value || '';
                        borrowType = 'student';
                        console.log('Student selected:', selectedUserId);
                        if (!selectedUserId) {
                            alert('Please select a student.');
                            return;
                        }
                    } else if (isTeacherTab) {
                        selectedUserId = teacherSelect?.value || '';
                        borrowType = 'teacher';
                        console.log('Teacher selected:', selectedUserId);
                        if (!selectedUserId) {
                            alert('Please select a teacher.');
                            return;
                        }
                    } else {
                        alert('Error: Unable to determine active tab.');
                        return;
                    }

                    console.log('Cart length:', cart.length);
                    if (cart.length === 0) {
                        alert('Please add at least one book.');
                        return;
                    }

                    // Determine book limit based on user type
                    const maxBooks = isTeacherTab ? 3 : 3;
                    if (cart.length > maxBooks) {
                        const userType = isTeacherTab ? 'teacher' : 'student';
                        alert(`You can only borrow up to ${maxBooks} books for ${userType} borrowing. Currently selected: ${cart.length}`);
                        return;
                    }

                    // Get dates from the active tab
                    let borrowedAt = '';
                    let dueDate = '';

                    if (isStudentTab) {
                        borrowedAt = document.getElementById('student_borrow_date')?.value || '';
                        dueDate = document.getElementById('student_due_date')?.value || '';
                    } else if (isTeacherTab) {
                        borrowedAt = document.getElementById('teacher_borrow_date')?.value || '';
                        dueDate = document.getElementById('teacher_due_date')?.value || '';
                    }

                    console.log('Dates - Borrowed:', borrowedAt, 'Due:', dueDate);

                    // Validate dates are set
                    if (!borrowedAt || !dueDate) {
                        alert('Please set both borrow and due dates.');
                        return;
                    }

                    // Set all form fields
                    const borrowTypeField = document.getElementById('borrowTypeField');
                    const userIdField = document.getElementById('userIdField');
                    const borrowedAtField = document.getElementById('borrowedAtField');
                    const dueDateField = document.getElementById('dueDateField');

                    if (!borrowTypeField || !userIdField || !borrowedAtField || !dueDateField) {
                        alert('Error: Form fields are missing.');
                        return;
                    }

                    borrowTypeField.value = borrowType;
                    userIdField.value = selectedUserId;
                    borrowedAtField.value = borrowedAt;
                    dueDateField.value = dueDate;

                    console.log('Form values set - Type:', borrowType, 'User:', selectedUserId, 'Books:', cart.length);
                    console.log('About to submit form with', cart.length, 'books');

                    // Actually submit the form now
                    borrowForm.submit();
                });

                // initial render
                renderCart();
            });
            </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Herd\library\resources\views/borrow/create.blade.php ENDPATH**/ ?>