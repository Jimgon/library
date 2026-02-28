@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-3">Return Borrowed Books</h3>

    {{-- Notifications --}}
    @if(session('success') || session('error'))
        <div id="notification" class="position-fixed top-50 start-50 translate-middle p-3 rounded text-white" 
             style="z-index: 1050; min-width: 300px; text-align:center; 
                    background-color: {{ session('success') ? '#28a745' : '#dc3545' }};
                    animation: popup 0.5s ease-out forwards;">
            {{ session('success') ?? session('error') }}
        </div>

        <style>
            @keyframes popup {
                0% { transform: translate(-50%, -60%) scale(0); opacity: 0; }
                60% { transform: translate(-50%, -50%) scale(1.2); opacity: 1; }
                100% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
            }
        </style>

        <script>
            setTimeout(() => {
                const notif = document.getElementById('notification');
                if(notif) notif.style.display = 'none';
            }, 3000);
        </script>
    @endif

    <div class="d-flex mb-3" style="gap:.5rem;align-items:center;">
        <input id="returnSearch" type="search" class="form-control" placeholder="Search borrower or book..." aria-label="Search returns">
        <button id="clearReturnSearch" type="button" class="btn btn-outline-secondary">Clear</button>
    </div>

    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th style="width: 40px;">
                    <input type="checkbox" id="selectAllCheckbox" aria-label="Select all">
                </th>
                <th>Borrower</th>
                <th>Book</th>
                <th>Type</th>
                <th>Borrow Date</th>
                <th>Due Date</th>
                <th>Remarks</th>
                <th>Notes</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @php
            // Group borrows by user_id, book_id, and borrowed_at date
            $grouped = $borrows->groupBy(function($borrow) {
                $borrowDate = $borrow->borrowed_at ? \Carbon\Carbon::parse($borrow->borrowed_at)->format('Y-m-d') : 'unknown';
                return $borrow->user_id . '|' . $borrow->book_id . '|' . $borrowDate;
            })->map(function($group) {
                return [
                    'borrows' => $group,
                    'count' => $group->count(),
                    'firstBorrow' => $group->first()
                ];
            });
        @endphp
        
        @forelse($grouped as $transaction)
            @php
                $borrow = $transaction['firstBorrow'];
                $quantity = $transaction['count'];
                
                // Use borrowed_at if available, otherwise use created_at as fallback
                $borrowedAt = null;
                if ($borrow->borrowed_at) {
                    $borrowedAt = \Carbon\Carbon::parse($borrow->borrowed_at);
                } elseif ($borrow->created_at) {
                    $borrowedAt = \Carbon\Carbon::parse($borrow->created_at);
                }
                
                // Use due_date if available, otherwise calculate from borrowed_at
                $dueDate = null;
                if ($borrow->due_date) {
                    $dueDate = \Carbon\Carbon::parse($borrow->due_date);
                } elseif ($borrowedAt) {
                    // Check if this is a distribution book to determine default duration
                    $isDistribution = $borrow->book ? false : \App\Models\DistributedBook::find($borrow->book_id);
                    $dueDate = $isDistribution ? $borrowedAt->addMonths(12) : $borrowedAt->addDays(14);
                }
                
                $today = \Carbon\Carbon::today();

                $overdueDays = 0;
                $computedRemark = 'No Remarks';
                if ($dueDate && $today->gt($dueDate)) {
                    $overdueDays = $today->diffInDays($dueDate);
                    $computedRemark = "{$overdueDays} day(s) overdue";
                }

                $student = $borrow->user;
                $remark = !empty($borrow->remark) ? $borrow->remark : $computedRemark;

                $lower = strtolower($remark);
                // Red for overdue, lost, damage; Green for everything else
                if (str_contains($lower, 'overdue') || $lower === 'lost' || $lower === 'damage') {
                    $badgeClass = 'bg-danger';
                } else {
                    $badgeClass = 'bg-success';
                }
            @endphp

            <tr class="borrow-row">
                <td>
                    <input type="checkbox" class="borrow-checkbox" data-borrow-id="{{ $borrow->id }}" data-quantity="{{ $quantity }}" aria-label="Select this transaction">
                </td>
                <td>
                    @php
                        $borrower = $borrow->user;
                        if (!$borrower) {
                            // Try to load from Teacher model if not found in User model
                            $borrower = \App\Models\Teacher::find($borrow->user_id);
                        }
                    @endphp
                    @if($borrower)
                        {{ $borrower->name ?? (($borrower->first_name ?? 'Unknown') . ' ' . ($borrower->last_name ?? '')) }}
                    @else
                        Unknown
                    @endif
                </td>
                <td>
                    @php
                        $bookTitle = 'Book not found';
                        $bookType = 'Student';
            
                        if ($borrow->book) {
                            $bookTitle = $borrow->book->title;
                        } else {
                            $distBook = \App\Models\DistributedBook::find($borrow->book_id);
                            if ($distBook) {
                                $bookTitle = $distBook->title;
                                $bookType = 'Teacher';
                            }
                        }
                    @endphp
                    {{ $bookTitle }}
                </td>
                <td>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <span class="badge {{ $bookType === 'Distribution' ? 'bg-info' : 'bg-secondary' }}">
                            {{ $bookType }}
                        </span>
                        <span class="badge bg-primary">{{ $quantity }}x</span>
                        <small class="text-muted">
                            <strong>Return:</strong>
                            <input type="number" class="returnQuantity" min="0" max="{{ $quantity }}" value="{{ $quantity }}" style="width: 50px; padding: 3px; border: 1px solid #ccc; border-radius: 4px;">
                        </small>
                    </div>
                </td>
                <td>{{ $borrowedAt ? $borrowedAt->format('Y-m-d') : 'N/A' }}</td>
                <td>{{ $dueDate ? $dueDate->format('Y-m-d') : 'N/A' }}</td>

                {{-- Remarks column --}}
                <td>
                    <form action="{{ route('borrow.return.process', $borrow->id) }}" method="POST" class="d-flex flex-column gap-2 return-form" data-quantity="{{ $quantity }}">
                        @csrf
                        @php $selected = old('remark', $borrow->remark ?? ''); @endphp
                        
                        {{-- Add hidden inputs for all borrow IDs in this transaction --}}
                        @foreach($transaction['borrows'] as $b)
                            <input type="hidden" name="borrow_ids[]" value="{{ $b->id }}">
                        @endforeach
                        
                        {{-- Hidden input for quantity being returned --}}
                        <input type="hidden" name="quantity_returned" class="quantity-returned-input" value="{{ $quantity }}">
                        
                        <select name="remark" class="form-select form-select-sm" aria-label="Set remark">
                            <option value="No Remarks" {{ $selected === 'No Remarks' ? 'selected' : '' }}>No Remarks</option>
                            <option value="On Time" {{ $selected === 'On Time' ? 'selected' : '' }}>On Time</option>
                            <option value="Late Return" {{ $selected === 'Late Return' ? 'selected' : '' }}>Late Return</option>
                            <option value="Lost" {{ $selected === 'Lost' ? 'selected' : '' }}>Lost</option>
                            <option value="Damage" {{ $selected === 'Damage' ? 'selected' : '' }}>Damage</option>
                        </select>
                </td>

                {{-- Notes column --}}
                <td>
                        @if($borrow->notes)
                            <div class="small text-muted mb-2">
                                <strong>Existing Notes:</strong><br>
                                {{ $borrow->notes }}
                            </div>
                        @endif
                        <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Add additional notes..." maxlength="500">{{ old('notes', '') }}</textarea>
                </td>

                <td>
                            <button type="submit" class="btn btn-success btn-sm return-btn">Return <span class="return-quantity">{{ $quantity }}</span></button>
                        </form>

                        {{-- Print Receipt --}}
                        <a href="{{ route('borrow.receipt', $borrow->id) }}" target="_blank" class="btn btn-primary btn-sm mt-1">
                            Print Receipt
                        </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center py-4">No books to return.</td>
            </tr>
        @endforelse
    </table>
    <div class="mt-3 d-flex gap-2">
        <button id="returnSelectedBtn" type="button" class="btn btn-success" style="display: none;">
            Return Selected (<span id="selectedCount">0</span>)
        </button>
        <button id="clearSelectionBtn" type="button" class="btn btn-outline-secondary" style="display: none;">
            Clear Selection
        </button>
    </div>

    <script>
        (function(){
            const searchInput = document.getElementById('returnSearch');
            const clearBtn = document.getElementById('clearReturnSearch');
            const table = document.querySelector('.table.table-bordered');
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            const returnSelectedBtn = document.getElementById('returnSelectedBtn');
            const clearSelectionBtn = document.getElementById('clearSelectionBtn');
            const selectedCountSpan = document.getElementById('selectedCount');
            
            if(!searchInput || !table) return;

            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr.borrow-row'));
            
            // Get all checkboxes
            const allCheckboxes = Array.from(tbody.querySelectorAll('input.borrow-checkbox'));

            // Update return quantity when quantity input changes
            rows.forEach(row => {
                const quantityInput = row.querySelector('input.returnQuantity');
                const quantityReturnedInput = row.querySelector('input.quantity-returned-input');
                const returnQuantitySpan = row.querySelector('span.return-quantity');
                
                if(quantityInput) {
                    quantityInput.addEventListener('change', function() {
                        const value = parseInt(this.value) || 0;
                        if(quantityReturnedInput) {
                            quantityReturnedInput.value = value;
                        }
                        if(returnQuantitySpan) {
                            returnQuantitySpan.textContent = value;
                        }
                    });
                    
                    // Also update on input for real-time feedback
                    quantityInput.addEventListener('input', function() {
                        const value = parseInt(this.value) || 0;
                        if(returnQuantitySpan) {
                            returnQuantitySpan.textContent = value;
                        }
                    });
                }
            });

            function normalize(s){ return (s||'').toString().trim().toLowerCase(); }

            function filterRows(){
                const q = normalize(searchInput.value);
                if(q === ''){
                    rows.forEach(r => r.style.display = '');
                    return;
                }

                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    const borrower = normalize(cells[1]?.textContent || '');
                    const book = normalize(cells[2]?.textContent || '');
                    const notes = normalize(cells[6]?.textContent || '');
                    const combined = borrower + ' ' + book + ' ' + notes;
                    row.style.display = combined.indexOf(q) !== -1 ? '' : 'none';
                });
            }

            // Update selected count and button visibility
            function updateSelectedCount(){
                const checkedCount = allCheckboxes.filter(cb => cb.checked).length;
                selectedCountSpan.textContent = checkedCount;
                
                if(checkedCount > 0){
                    returnSelectedBtn.style.display = 'inline-block';
                    clearSelectionBtn.style.display = 'inline-block';
                } else {
                    returnSelectedBtn.style.display = 'none';
                    clearSelectionBtn.style.display = 'none';
                }
            }

            // Select all checkbox
            selectAllCheckbox.addEventListener('change', function(){
                const isChecked = this.checked;
                allCheckboxes.forEach(cb => {
                    cb.checked = isChecked;
                });
                updateSelectedCount();
            });

            // Individual checkboxes
            allCheckboxes.forEach(cb => {
                cb.addEventListener('change', function(){
                    updateSelectedCount();
                });
            });

            // Clear selection
            clearSelectionBtn.addEventListener('click', function(){
                selectAllCheckbox.checked = false;
                allCheckboxes.forEach(cb => cb.checked = false);
                updateSelectedCount();
            });

            // Return selected books using AJAX to process multiple returns
            returnSelectedBtn.addEventListener('click', function(){
                const selectedIds = allCheckboxes.filter(cb => cb.checked).map(cb => cb.dataset.borrowId);
                
                if(selectedIds.length === 0){
                    alert('Please select at least one book to return.');
                    return;
                }

                if(!confirm(`Return ${selectedIds.length} selected book(s)? Each will use the remarks and notes from their respective row.`)){
                    return;
                }

                // Disable button during processing
                returnSelectedBtn.disabled = true;
                returnSelectedBtn.textContent = 'Processing...';

                // Submit all forms via AJAX sequentially so they all process
                let index = 0;
                let successCount = 0;
                let errorCount = 0;

                function submitNext(){
                    if(index >= selectedIds.length){
                        // All done, reload page to show results
                        alert(`Successfully returned ${successCount} book(s).${errorCount > 0 ? ` Failed: ${errorCount}` : ''}`);
                        window.location.reload();
                        return;
                    }
                    
                    const id = selectedIds[index];
                    const row = table.querySelector(`input.borrow-checkbox[data-borrow-id="${id}"]`).closest('tr');
                    const form = row.querySelector('form.return-form');
                    
                    if(form){
                        const formData = new FormData(form);
                        const action = form.getAttribute('action');
                        
                        fetch(action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            if(response.ok) {
                                successCount++;
                            } else {
                                errorCount++;
                            }
                        })
                        .catch(err => {
                            console.error('Error submitting form:', err);
                            errorCount++;
                        })
                        .finally(() => {
                            index++;
                            // Wait a moment before next submission
                            setTimeout(submitNext, 500);
                        });
                    } else {
                        index++;
                        submitNext();
                    }
                }
                
                submitNext();
            });

            searchInput.addEventListener('input', filterRows);
            clearBtn.addEventListener('click', () => { searchInput.value = ''; filterRows(); searchInput.focus(); });
        })();
    </script>
</div>
@endsection

