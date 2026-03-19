@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">


        <!-- Add Book Form -->
        <div class="col-md-8">
            <h4 class="mb-3">Add New Book</h4>
                <form action="{{ route('books.store') }}" method="POST" class="p-4">
                    @csrf

                    {{-- Basic Information Section --}}
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
                                class="form-control @error('title') is-invalid @enderror" 
                                value="{{ old('title') }}"
                                style="text-transform: capitalize;"
                                required
                            >
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="author" class="form-label">Author <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                name="author" 
                                id="author" 
                                class="form-control @error('author') is-invalid @enderror" 
                                value="{{ old('author') }}"
                                style="text-transform: capitalize;"
                                required
                            >
                            @error('author')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="publisher" class="form-label">Publisher</label>
                            <input 
                                type="text" 
                                name="publisher" 
                                id="publisher" 
                                class="form-control @error('publisher') is-invalid @enderror" 
                                value="{{ old('publisher') }}"
                                style="text-transform: capitalize;"
                            >
                            @error('publisher')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="isbn" class="form-label">ISBN <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                name="isbn" 
                                id="isbn" 
                                class="form-control @error('isbn') is-invalid @enderror" 
                                value="{{ old('isbn') }}"
                                placeholder="13 digit ISBN"
                                pattern="[0-9]{13}"
                                maxlength="13"
                                minlength="13"
                                inputmode="numeric"
                                required
                            >
                            @error('isbn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Classification & Cataloging Section --}}
                    <div class="section-title mb-4 mt-4">
                        <h6 class="text-uppercase fw-bold text-secondary">Classification & Cataloging</h6>
                        <hr>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                                <option value="">-- Select Category --</option>
                                @foreach($allCategories as $catValue)
                                    @php $catValue = trim($catValue); @endphp
                                    <option value="{{ $catValue }}" {{ old('category') === $catValue ? 'selected' : '' }}>{{ $catValue }}</option>
                                @endforeach
                                <option value="other" {{ old('category') === 'other' || (old('category') && !in_array(old('category'), $allCategories)) ? 'selected' : '' }}>Other</option>
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
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <input type="text" name="other_category" id="other_category" class="form-control mt-2 @error('other_category') is-invalid @enderror" placeholder="Enter new category" value="{{ old('other_category') }}" style="display: none; text-transform: capitalize;">
                            @error('other_category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                       

                        <div class="col-md-4 mb-3">
                            <label for="published_year" class="form-label">Published Year</label>
                            <input 
                                type="number" 
                                name="published_year" 
                                id="published_year" 
                                class="form-control @error('published_year') is-invalid @enderror" 
                                value="{{ old('published_year') }}"
                                min="1900"
                                max="{{ date('Y') + 1 }}"
                            >
                            @error('published_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- <div class="col-md-4 mb-3">
                            <label for="cost_price" class="form-label">Cost Price</label>
                            <input 
                                type="number" 
                                name="cost_price" 
                                id="cost_price" 
                                class="form-control @error('cost_price') is-invalid @enderror" 
                                value="{{ old('cost_price') }}"
                                min="0"
                                step="0.01"
                            >
                            @error('cost_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> --}}
                    </div>

                    {{-- Physical Characteristics Section --}}
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
                                class="form-control @error('pages') is-invalid @enderror" 
                                value="{{ old('pages') }}"
                                min="1"
                            >
                            @error('pages')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="edition" class="form-label">Edition</label>
                            <input 
                                type="text" 
                                name="edition" 
                                id="edition" 
                                class="form-control @error('edition') is-invalid @enderror" 
                                value="{{ old('edition') }}"
                                placeholder="e.g., 3rd Edition"
                                style="text-transform: capitalize;"
                            >
                            @error('edition')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="condition" class="form-label">Condition</label>
                            <select
                                name="condition"
                                id="condition"
                                class="form-select @error('condition') is-invalid @enderror"
                            >
                                <option value="">-- Select Condition --</option>
                                <option value="Brand New" {{ old('condition') === 'Brand New' ? 'selected' : '' }}>Brand New</option>
                                <option value="Old" {{ old('condition') === 'Old' ? 'selected' : '' }}>Old</option>
                            </select>
                            @error('condition')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                      
                    {{-- Acquisition Information Section --}}
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
                                class="form-select @error('acquisition_type') is-invalid @enderror"
                            >
                                <option value="">-- Select Type --</option>
                                <option value="purchase" {{ old('acquisition_type') === 'purchase' ? 'selected' : '' }}>Purchase</option>
                                <option value="donation" {{ old('acquisition_type') === 'donation' ? 'selected' : '' }}>Donation</option>
                            </select>
                            @error('acquisition_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="source_of_funds" class="form-label">Source of Funds</label>
                            <input 
                                type="text" 
                                name="source_of_funds" 
                                id="source_of_funds" 
                                class="form-control @error('source_of_funds') is-invalid @enderror" 
                                value="{{ old('source_of_funds') }}"
                                placeholder="e.g., School Budget, PTA Fund"
                                style="text-transform: capitalize;"
                            >
                            @error('source_of_funds')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="purchase_price" class="form-label">Purchase Price</label>
                            <input 
                                type="number" 
                                name="purchase_price" 
                                id="purchase_price" 
                                class="form-control @error('purchase_price') is-invalid @enderror" 
                                value="{{ old('purchase_price') }}"
                                min="0"
                                step="0.01"
                            >
                            @error('purchase_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Copies Information Section --}}
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
                                value="{{ $nextCtrlBase ?? '001' }}"
                                readonly
                            >
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="copies" class="form-label">Total Number of Copies <span class="text-danger">*</span></label>
                            <input 
                                type="number" 
                                name="copies" 
                                id="copies" 
                                class="form-control @error('copies') is-invalid @enderror" 
                                value="{{ old('copies', 1) }}"
                                min="1"
                                required
                            >
                            @error('copies')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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

                    {{-- Form Actions --}}
                    <div class="d-flex gap-2 justify-content-end mt-5">
                        <a href="{{ route('books.catalog') }}" class="btn btn-outline-secondary">
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
@endsection

@push('scripts')
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
@endpush
