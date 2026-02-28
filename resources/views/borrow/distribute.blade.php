@extends('layouts.app')

@section('content')
<div class="container-fluid">
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
                                    <option value="{{ $user->id ?? $user->id }}">{{ $user->name ?? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) }}</option>
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
                        <p class="text-muted small">Select distribution books and add to cart.</p>

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
                                        <option value="{{ $book->id }}" data-title="{{ $book->title }}" data-author="{{ $book->author }}" data-available-copies="{{ $avail }}" data-total-copies="{{ $total }}">
                                            {{ $book->title }} ({{ $avail }}/{{ $total }} available)
                                        </option>
                                    @endforeach
                                </select>
                                <input type="number" id="dist_book_qty" class="form-control" style="max-width: 100px;" min="1" value="1" placeholder="Qty">
                                <button id="addDistBookBtn" type="button" class="btn btn-secondary">Add Book</button>
                            </div>
                            <small id="stockWarning" class="text-danger" style="display: none;">This book is out of stock.</small>
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

                let cart = [];

                function renderCart() {
                    hiddenInputs.innerHTML = '';
                    if (cart.length === 0) {
                        cartList.innerHTML = '<div class="text-muted small">No books added yet.</div>';
                        confirmBtn.textContent = 'Confirm (0)';
                        return;
                    }

                    let totalCount = 0;
                    cart.forEach(c => totalCount += c.quantity);
                    confirmBtn.textContent = `Confirm (${totalCount})`;
                    
                    const ul = document.createElement('ul'); 
                    ul.className = 'list-unstyled mb-0';
                    
                    cart.forEach((c, idx) => {
                        const li = document.createElement('li'); 
                        li.className = 'd-flex justify-content-between align-items-center py-2 border-bottom';
                        li.innerHTML = `<div><strong>${c.title}</strong> <span class="badge bg-primary">${c.quantity}x</span><div class="small text-muted">${c.author}</div></div>`;
                        
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

                        // Create multiple hidden inputs based on quantity
                        for (let i = 0; i < c.quantity; i++) {
                            const input = document.createElement('input'); 
                            input.type = 'hidden'; 
                            input.name = 'book_ids[]'; 
                            input.value = c.id; 
                            hiddenInputs.appendChild(input);
                        }
                    });
                    
                    cartList.innerHTML = ''; 
                    cartList.appendChild(ul);
                }

                const userSelect = document.querySelector('select[name="user_id"]');

                // Update stock warning when book selection changes
                distBookSelect.addEventListener('change', function(){
                    const availableCopies = parseInt(this.selectedOptions[0]?.dataset.availableCopies || 0);
                    const stockWarning = document.getElementById('stockWarning');
                    if (availableCopies < 1) {
                        stockWarning.style.display = 'block';
                    } else {
                        stockWarning.style.display = 'none';
                    }
                    // Set max quantity to available copies
                    distBookQty.max = Math.max(1, availableCopies);
                });

                addDistBookBtn.addEventListener('click', function(){
                    const id = distBookSelect.value; 
                    if (!id) return alert('Select a book');
                    
                    const opt = distBookSelect.selectedOptions[0];
                    const availableCopies = parseInt(opt.dataset.availableCopies || 0);
                    
                    if (availableCopies < 1) {
                        return alert(`${opt.dataset.title} is out of stock`);
                    }
                    
                    const qty = parseInt(distBookQty.value) || 1;
                    if (qty < 1) return alert('Quantity must be at least 1');
                    if (qty > availableCopies) return alert(`Only ${availableCopies} copy/copies of this book available`);
                    
                    // Check if book already in cart, update quantity if so
                    const existing = cart.find(c => c.id === id);
                    if (existing) {
                        const newQty = existing.quantity + qty;
                        if (newQty > availableCopies) {
                            return alert(`Attempting to add ${newQty} copies, but only ${availableCopies} available`);
                        }
                        existing.quantity = newQty;
                    } else {
                        cart.push({ 
                            id, 
                            title: opt.dataset.title, 
                            author: opt.dataset.author,
                            quantity: qty
                        });
                    }
                    
                    distBookQty.value = '1';
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

