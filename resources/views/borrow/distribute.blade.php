@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <style>
        .btn-outline-primary { border-color: #93c5fd; color: #3b82f6; background: transparent; transition: all .12s ease; }
        .btn-outline-primary:hover { background: #eff6ff; border-color: #3b82f6; }
        .btn-outline-primary.active { background: #3b82f6; color: #fff; border-color: #3b82f6; }
    </style>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-1">Borrow Distributed Books</h4>
            <p class="text-muted mb-0">Issue distribution books to teachers</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="container py-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <h4 class="mb-1">Borrow Distributed Books</h4>
                    <p class="text-muted mb-4">Select teacher and distribution books with quantities to borrow.</p>

                    <form id="borrowDistributeForm" action="{{ route('borrow.distribute.store') }}" method="POST">
                        @csrf

                        <h6 class="fw-bold">Teacher Information</h6>
                        <p class="text-muted small">Search teacher by their name to borrow books.</p>

                        <div class="mb-3">
                            <label class="form-label">Select Teacher</label>
                            <select id="student_select_dist" name="user_id" class="form-select" required>
                                <option value="" selected disabled>Select teacher...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->_id ?? $user->id }}">{{ $user->name ?? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Borrow Date</label>
                                <input type="date" name="borrowed_at" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Due Date</label>
                                <input type="date" name="due_date" class="form-control" value="{{ date('Y-m-d', strtotime('+12 months')) }}" required>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold">Distribution Book Information</h6>
                        <p class="text-muted small">Select distribution books and add to list.</p>

                        <div class="mb-3">
                            <label class="form-label">Select Book and Quantity</label>
                            <div class="d-flex gap-2">
                                <select id="dist_book_select" class="form-select">
                                    <option value="" selected disabled>Select a book...</option>
                                    @foreach($books as $book)
                                        @php
                                            $avail = $book->available_copies ?? $book->copies ?? 0;
                                            $total = $book->copies ?? 0;
                                        @endphp
                                        <option value="{{ $book->_id ?? $book->id }}" 
                                                        data-title="{{ $book->title }}" 
                                                        data-author="{{ $book->author }}" 
                                                        data-available-copies="{{ $avail }}" 
                                                        data-total-copies="{{ $total }}"
                                                        data-control-numbers='@json($book->control_numbers ?? [])'>
                                            {{ $book->title }} ({{ $avail }}/{{ $total }} available)
                                        </option>
                                    @endforeach
                                </select>
                                <input type="number" id="dist_book_qty" class="form-control" style="max-width: 100px;" min="1" value="1" placeholder="Qty">
                                <button id="addDistBookBtn" type="button" class="btn btn-secondary">Add Book</button>
                            </div>
                            <small id="stockWarning" class="text-danger" style="display: none;">This book is out of stock.</small>
                        </div>

                        <!-- Control Number Selection for Distribution -->
                        <div class="mb-3" id="distControlNumberSection" style="display: none;">
                            <label class="form-label">Select Copies (Ctrl#)</label>
                            <div id="distControlNumberCheckboxes" class="d-flex flex-wrap gap-3"></div>
                            <small class="text-muted">Click the checkboxes of the copies you want to add.</small>
                        </div>

                        <div id="cartList" class="border rounded p-3 text-muted small mb-3">No books added yet.</div>
                        <div id="hiddenInputs"></div>

                        <div class="d-flex gap-2 mb-4">
                            <button id="confirmBtn" type="button" class="btn btn-primary flex-grow-1">Confirm (0)</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const distBookSelect = document.getElementById('dist_book_select');
                const distBookQty = document.getElementById('dist_book_qty');
                const addDistBookBtn = document.getElementById('addDistBookBtn');
                const cartList = document.getElementById('cartList');
                const hiddenInputs = document.getElementById('hiddenInputs');
                const confirmBtn = document.getElementById('confirmBtn');
                const distControlNumberSection = document.getElementById('distControlNumberSection');
                const distControlNumberCheckboxes = document.getElementById('distControlNumberCheckboxes');

                let cart = [];
                let selectedCopies = [];

                function renderCart() {
                    hiddenInputs.innerHTML = '';
                    if (cart.length === 0) {
                        cartList.innerHTML = '<div class="text-muted small">No books added yet.</div>';
                        confirmBtn.textContent = 'Confirm (0)';
                        return;
                    }

                    let totalCount = 0;
                    cart.forEach(c => totalCount += c.controlNumbers.length);
                    confirmBtn.textContent = `Confirm (${totalCount})`;
                    
                    const ul = document.createElement('ul'); 
                    ul.className = 'list-unstyled mb-0';
                    
                    cart.forEach((c, idx) => {
                        const li = document.createElement('li'); 
                        li.className = 'd-flex justify-content-between align-items-center py-2 border-bottom';
                        const ctrlsDisplay = c.controlNumbers.join(', ');
                        li.innerHTML = `<div><strong>${c.title}</strong> <span class="badge bg-primary">${c.controlNumbers.length}x</span><div class="small text-muted">${c.author}</div><div class="small" style="color: #3b82f6; font-weight: 500;">Ctrl#: ${ctrlsDisplay}</div></div>`;
                        
                        const btn = document.createElement('button'); 
                        btn.className = 'btn btn-sm btn-outline-danger'; 
                        btn.type = 'button'; 
                        btn.textContent = 'Remove';
                        btn.addEventListener('click', () => { 
                            cart.splice(idx, 1); 
                            renderCart(); 
                        });
                        
                        li.appendChild(btn);
                        ul.appendChild(li);

                        // Create hidden inputs for each control number
                        c.controlNumbers.forEach(ctrl => {
                            const bookInput = document.createElement('input'); 
                            bookInput.type = 'hidden'; 
                            bookInput.name = 'book_ids[]'; 
                            bookInput.value = c.id; 
                            hiddenInputs.appendChild(bookInput);

                            const ctrlInput = document.createElement('input'); 
                            ctrlInput.type = 'hidden'; 
                            ctrlInput.name = 'copy_numbers[]'; 
                            ctrlInput.value = ctrl; 
                            hiddenInputs.appendChild(ctrlInput);
                        });
                    });
                    
                    cartList.innerHTML = ''; 
                    cartList.appendChild(ul);
                }

                const userSelect = document.querySelector('select[name="user_id"]');

                // Build control number map for books
                const bookControlMap = {};
                document.querySelectorAll('#dist_book_select option').forEach(opt => {
                    if (!opt.value) return;
                    bookControlMap[opt.value] = {
                        title: opt.dataset.title || '',
                        author: opt.dataset.author || '',
                        controlNumbers: opt.dataset.controlNumbers ? JSON.parse(opt.dataset.controlNumbers) : []
                    };
                });

                // Update stock warning and show control numbers when book selection changes
                distBookSelect.addEventListener('change', function(){
                    const availableCopies = parseInt(this.selectedOptions[0]?.dataset.availableCopies || 0);
                    const stockWarning = document.getElementById('stockWarning');
                    if (availableCopies < 1) {
                        stockWarning.style.display = 'block';
                        distControlNumberSection.style.display = 'none';
                    } else {
                        stockWarning.style.display = 'none';
                        
                        // Show available control numbers as checkboxes
                        const bookId = this.value;
                        const bookData = bookControlMap[bookId];
                        if (bookData && bookData.controlNumbers.length > 0) {
                            distControlNumberSection.style.display = 'block';
                            distControlNumberCheckboxes.innerHTML = '';
                            selectedCopies = [];
                            
                            bookData.controlNumbers.forEach((ctrl) => {
                                const checkboxDiv = document.createElement('div');
                                checkboxDiv.className = 'form-check';
                                
                                const checkbox = document.createElement('input');
                                checkbox.className = 'form-check-input';
                                checkbox.type = 'checkbox';
                                checkbox.id = `ctrl_${ctrl}`;
                                checkbox.value = ctrl;
                                checkbox.dataset.controlNumber = ctrl;
                                
                                const label = document.createElement('label');
                                label.className = 'form-check-label';
                                label.htmlFor = `ctrl_${ctrl}`;
                                label.textContent = ctrl;
                                
                                checkboxDiv.appendChild(checkbox);
                                checkboxDiv.appendChild(label);
                                distControlNumberCheckboxes.appendChild(checkboxDiv);
                            });
                        } else {
                            distControlNumberSection.style.display = 'none';
                        }
                    }
                    // Set max quantity to available copies
                    distBookQty.max = Math.max(1, availableCopies);
                });

                addDistBookBtn.addEventListener('click', function(){
                    const id = distBookSelect.value; 
                    if (!id) return alert('Select a book');
                    
                    const opt = distBookSelect.selectedOptions[0];
                    const availableCopies = parseInt(opt.dataset.availableCopies || 0);
                    const bookData = bookControlMap[id];
                    
                    if (availableCopies < 1) {
                        return alert(`${opt.dataset.title} is out of stock`);
                    }

                    // Get all checked copies from checkboxes
                    let ctrlsToUse = [];
                    if (bookData && bookData.controlNumbers.length > 0) {
                        const checkedBoxes = Array.from(distControlNumberCheckboxes.querySelectorAll('input[type="checkbox"]:checked'));
                        if (checkedBoxes.length === 0) {
                            return alert('Please select at least one copy (Ctrl#)');
                        }
                        ctrlsToUse = checkedBoxes.map(cb => cb.value);
                    } else {
                        ctrlsToUse = ['N/A'];
                    }
                    
                    // Add all selected copies to cart
                    ctrlsToUse.forEach(ctrl => {
                        // Check if this specific copy is already in cart
                        const existing = cart.find(c => c.id === id && c.controlNumbers.includes(ctrl));
                        if (!existing) {
                            cart.push({ 
                                id, 
                                title: opt.dataset.title, 
                                author: opt.dataset.author,
                                controlNumbers: [ctrl]
                            });
                        }
                    });
                    
                    distBookSelect.value = '';
                    distControlNumberSection.style.display = 'none';
                    distControlNumberCheckboxes.innerHTML = '';
                    selectedCopies = [];
                    renderCart();
                });

                confirmBtn.addEventListener('click', function(){
                    if (cart.length === 0) return alert('Add at least one book');
                    if (!document.querySelector('select[name="user_id"]').value) return alert('Select a teacher');
                    document.getElementById('borrowDistributeForm').submit();
                });
            });
        </script>

    </div>
</div>
@endsection
