@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <style>
        /* Compact neutral UI */
        .container.py-5 { max-width: 820px; }
        .card { border-radius: .6rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04); border: 1px solid #e9f0fb; background: #ffffff; }
        .card-body { padding: 1rem !important; }
        h4 { color: #0f172a; font-weight:600; font-size:1.05rem; margin-bottom:.35rem; }
        p.text-muted { color: #64748b; margin-bottom:.6rem; font-size:.9rem; }
        .form-label { font-size: .86rem; font-weight:500; color:#0f172a; }
        .form-control, .form-select { border-radius: .45rem; border: 1px solid #eaf3ff; background: #fbfdff; transition: all .12s ease; box-shadow: none; padding:.45rem .6rem; font-size:.92rem; }
        .form-control:focus, .form-select:focus { outline: none; border-color: #93c5fd; box-shadow: 0 0 0 4px rgba(59,130,246,0.05); background: #fff; }
        .btn { border-radius: .5rem; transition: transform .06s ease, box-shadow .06s ease, background-color .12s ease; padding: .45rem .75rem; font-size:.92rem; }
        .btn:hover { transform: translateY(-1px); }
        .btn-primary { background: #3B82F6; border-color: #3B82F6; box-shadow: 0 6px 12px rgba(59,130,246,0.10); color: #fff; }
        .btn-secondary { background: #f7fafc; border-color: #eef6ff; color: #111827; }
        #cartList ul { padding-left: 0; margin:0; }
        #cartList li { transition: background .12s ease, transform .12s ease; padding: .45rem; border-radius: .45rem; margin-bottom: .4rem; background: #fff; border:1px solid #f4f7fb; display:flex; justify-content:space-between; align-items:center; }
                .nav-pills .nav-link { color: #000; }
        .nav-pills .nav-link.active {
            background-color: #000;
            color: #fff;
        }
        #cartList li:hover { background: #f8fbff; transform: translateY(-1px); }
        #cartList .btn-outline-danger { border-color: transparent; color: #ef4444; background: transparent; padding: .2rem .45rem; }
        .badge.bg-secondary { background: #64748b; color: #fff; padding: .3rem .45rem; border-radius: .45rem; font-size:.85rem; }
        /* Select2 tweaks */ 
        .select2-container .select2-selection--single { height: calc(1.25em + 1rem); border-radius: .45rem; padding: .25rem .45rem; border:1px solid #eaf3ff; background:#fbfdff; font-size:.94rem; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 1.4; color: #0f172a; }
        @media (max-width: 767px) { .container.py-5 { padding-left: 1rem; padding-right:1rem; } .card-body { padding: .85rem !important; } }
    </style>
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-1 not-odd:">Book Borrowing</h4>
            <p class="text-muted mb-0">Issue books to students and teachers</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="container py-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">

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

                    <form id="borrowForm" action="{{ route('borrow.store') }}" method="POST">
                        @csrf

                    <div class="tab-content" id="borrowTabContent">
                        <!-- Student Tab -->
                        <div class="tab-pane fade show active" id="student-borrow" role="tabpanel" aria-labelledby="student-tab">

                        <!-- ================= STUDENT INFO ================= -->
                        <h6 class="fw-bold">Student Information</h6>
                        <p class="text-muted small">Search student by their name to borrow books.</p>

                        <div class="mb-3">
                            <label class="form-label">Select Student</label>
                            <select id="student_select" name="user_id" class="form-select" required>
                                <option value="" selected disabled>Select student...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id ?? $user->id }}"
                                                    data-first="{{ $user->first_name ?? '' }}"
                                                    data-last="{{ $user->last_name ?? '' }}"
                                                    data-lrn="{{ $user->lrn ?? '' }}"
                                                    data-grade_section="{{ $user->grade_section ?? $user->year_level ?? '' }}"
                                                    data-address="{{ $user->address ?? $user->course ?? '' }}">
                                        {{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">LRN</label>
                                <input id="student_id_display" type="text" class="form-control" placeholder="N/A" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Student Name</label>
                                <input id="student_name_display" type="text" class="form-control" placeholder="N/A" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Grade & Section</label>
                                <input id="student_year_display" type="text" class="form-control" placeholder="N/A" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Address</label>
                                <input id="student_course_display" type="text" class="form-control" placeholder="N/A" readonly>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Borrow Date</label>
                                <input type="date" name="borrowed_at" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Due Date</label>
                                <input type="date" name="due_date" class="form-control" value="{{ date('Y-m-d', strtotime('+3 days')) }}" required>
                            </div>
                        </div>
                        </div>

                        <!-- Teacher Tab -->
                        <div class="tab-pane fade" id="teacher-borrow" role="tabpanel" aria-labelledby="teacher-tab">

                        <!-- ================= TEACHER INFO ================= -->
                        <h6 class="fw-bold">Teacher Information</h6>
                        <p class="text-muted small">Search teacher by their name to borrow books.</p>

                        <div class="mb-3">
                            <label class="form-label">Select Teacher</label>
                            @if(isset($teachers) && $teachers->isEmpty())
                                <div class="alert alert-warning">No teachers available.</div>
                            @endif
                            <select id="teacher_select" name="user_id" class="form-select" required>
                                <option value="" selected disabled>Select teacher...</option>
                                @foreach($teachers as $user)
                                    <option value="{{ $user->id ?? $user->id }}">
                                        {{ $user->name ?? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Borrow Date</label>
                                <input type="date" name="borrowed_at" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Due Date</label>
                                <input type="date" name="due_date" class="form-control" value="{{ date('Y-m-d', strtotime('+12 months')) }}" required>
                            </div>
                        </div>
                        </div>

                        <hr class="my-4">

                        <!-- ================= BOOK INFO ================= -->
                        </div>

                        <h6 class="fw-bold">Book Information</h6>
                        <p class="text-muted small">Search and select a book to borrow.</p>

                        <div class="mb-3">
                            <label class="form-label">Select Book</label>
                            <div class="d-flex gap-2">
                                <select id="book_select" class="form-select">
                                    <option value="" selected disabled>Select an option...</option>
                                    @foreach($books as $book)
                                        @php
                                            $avail = $book->available_copies ?? $book->copies ?? 0;
                                            $total = $book->copies ?? 0;
                                        @endphp
                                        <option value="{{ $book->id ?? $book->id }}"
                                                        data-title="{{ $book->title }}"
                                                        data-author="{{ $book->author ?? '' }}"
                                                        data-publisher="{{ $book->publisher ?? '' }}"
                                                        data-isbn="{{ $book->isbn ?? '' }}"
                                                        data-available-copies="{{ $avail }}"
                                                        data-total-copies="{{ $total }}">
                                            {{ $book->title }} ({{ $avail }}/{{ $total }} available)
                                        </option>
                                    @endforeach
                                </select>
                                <button id="addBookBtn" type="button" class="btn btn-secondary">Add Book</button>
                            </div>
                            <small id="stockWarning" class="text-danger" style="display: none; margin-top: 0.5rem;">This book is out of stock.</small>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Title</label>
                                <input id="book_title" type="text" class="form-control" placeholder="N/A" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Author</label>
                                <input id="book_author" type="text" class="form-control" placeholder="N/A" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Publisher</label>
                                <input id="book_publisher" type="text" class="form-control" placeholder="N/A" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ISBN</label>
                                <input id="book_isbn" type="text" class="form-control" placeholder="N/A" readonly>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2 mb-4">
                            <button id="confirmBtn" type="button" class="btn btn-primary flex-grow-1">Confirm (0)</button>
                        </div>

                        <!-- ================= BOOK LIST ================= -->
                        <div class="border rounded p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>Books to Borrow</strong>
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
                $('#book_select').select2({ width: '100%', placeholder: 'Select a book...' });
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
            const bookIsbn = document.getElementById('book_isbn');

            const addBookBtn = document.getElementById('addBookBtn');
            const confirmBtn = document.getElementById('confirmBtn');
            const cartList = document.getElementById('cartList');
            const cartCount = document.getElementById('cartCount');
            const hiddenInputs = document.getElementById('hiddenInputs');

            let cart = [];

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
                    li.innerHTML = `<div><strong>${item.title}</strong><div class="small text-muted">${item.author} • ${item.publisher} • ${item.isbn}</div></div>`;
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn btn-sm btn-outline-danger ms-2';
                    removeBtn.textContent = 'Remove';
                    removeBtn.addEventListener('click', () => { cart.splice(idx,1); renderCart(); });
                    li.appendChild(removeBtn);
                    list.appendChild(li);

                    // hidden input
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'book_ids[]';
                    input.value = item.id;
                    hiddenInputs.appendChild(input);
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
                    isbn: opt.dataset.isbn || ''
                };
            });

            // Initialize button as disabled
            addBookBtn.disabled = true;

            function populateBookDetailsById(id){
                const data = bookMap[id];
                if (!data || !id) {
                    bookTitle.value = '';
                    bookAuthor.value = '';
                    bookPublisher.value = '';
                    bookIsbn.value = '';
                    addBookBtn.disabled = true;
                    return;
                }
                bookTitle.value = data.title || '';
                bookAuthor.value = data.author || '';
                bookPublisher.value = data.publisher || '';
                bookIsbn.value = data.isbn || '';
                addBookBtn.disabled = false;
            }

            bookSelect?.addEventListener('change', function(e){
                const availableCopies = parseInt(this.selectedOptions[0]?.dataset.availableCopies || 0);
                const stockWarning = document.getElementById('stockWarning');
                if (availableCopies < 1) {
                    stockWarning.style.display = 'block';
                    addBookBtn.disabled = true;
                } else {
                    stockWarning.style.display = 'none';
                    addBookBtn.disabled = !this.value;
                }
                populateBookDetailsById(this.value);
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
                $('#book_select').on('select2:select', function(){ 
                    populateBookDetailsById(this.value); 
                });
                $('#book_select').on('select2:unselecting', function(){
                    addBookBtn.disabled = true;
                    bookTitle.value = '';
                    bookAuthor.value = '';
                    bookPublisher.value = '';
                    bookIsbn.value = '';
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
                // Check if already in cart
                if (cart.some(c => c.id === bookId)) {
                    alert('This book is already in the cart.');
                    return;
                }
                // Check if cart is full (max 3 books)
                if (cart.length >= 3) {
                    alert('You can only add up to 3 books. Please remove a book to add another.');
                    return;
                }
                cart.push({ id: bookId, title: bookData.title || '', author: bookData.author || '', publisher: bookData.publisher || '', isbn: bookData.isbn || '' });
                bookSelect.value = '';
                if (window.jQuery) $('#book_select').val('').trigger('change');
                renderCart();
            });

            confirmBtn?.addEventListener('click', function(e){
                e.preventDefault();
                
                // Determine which tab is active
                const activeTab = document.querySelector('.nav-link.active')?.id;
                const isStudentTab = activeTab === 'student-tab';
                const isTeacherTab = activeTab === 'teacher-tab';
                
                if (isStudentTab && !studentSelect.value) {
                    alert('Please select a student.');
                    return;
                }
                if (isTeacherTab && !document.getElementById('teacher_select').value) {
                    alert('Please select a teacher.');
                    return;
                }
                if (cart.length === 0) {
                    alert('Please add at least one book.');
                    return;
                }
                
                if (cart.length > 3) {
                    alert('You can only borrow up to 3 books at a time. Currently selected: ' + cart.length);
                    return;
                }
                
                // Verify hidden inputs exist
                const bookInputs = document.querySelectorAll('input[name="book_ids[]"]');
                if (bookInputs.length === 0) {
                    alert('Error: No books were added to the form. Please try again.');
                    console.error('No book_ids[] inputs found');
                    return;
                }
                
                const selectedUserId = isStudentTab ? studentSelect.value : document.getElementById('teacher_select').value;
                console.log('Submitting borrow form for:', isStudentTab ? 'student' : 'teacher', selectedUserId);
                console.log('Books:', Array.from(bookInputs).map(i => i.value));
                
                // submit the form
                document.getElementById('borrowForm').submit();
            });

            // initial render
            renderCart();
        });
        </script>

        @endsection

