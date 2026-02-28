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
                                placeholder="10 or 13 digit ISBN"
                                pattern="[0-9]{10,20}"
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
                                <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <input type="text" name="other_category" id="other_category" class="form-control mt-2 @error('other_category') is-invalid @enderror" placeholder="Enter new category" value="{{ old('other_category') }}" style="display: none;">
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
                        <div class="col-md-4 mb-3">
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
                        </div>
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
                                <option value="exchange" {{ old('acquisition_type') === 'exchange' ? 'selected' : '' }}>Exchange</option>
                                <option value="grant" {{ old('acquisition_type') === 'grant' ? 'selected' : '' }}>Grant</option>
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
                                value="{{ $nextCtrlBase ?? '' }}"
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
                        <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">
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

        // Filter ISBN to allow only numbers
        isbnInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Filter Pages to allow only numbers
        pagesInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        function toggleOther() {
            if (categorySelect.value === 'other') {
                otherInput.style.display = 'block';
                otherInput.required = true;
            } else {
                otherInput.style.display = 'none';
                otherInput.required = false;
                // clear the input when hiding
                otherInput.value = '';
            }
        }

        // keep an option in select when user types a new category
        function syncOtherCategory() {
            const val = otherInput.value.trim();
            if (!val) {
                categorySelect.value = 'other';
                return;
            }
            
            let existing = Array.from(categorySelect.options).find(o => o.value === val);
            if (!existing) {
                const opt = document.createElement('option');
                opt.value = val;
                opt.textContent = val;  // Use textContent for better compatibility
                opt.text = val;  // Also set text property
                // Add before "Other" option
                const otherOption = categorySelect.querySelector('option[value="other"]');
                categorySelect.insertBefore(opt, otherOption);
            }
            // Auto-select the custom category as the user types
            categorySelect.value = val;
        }

        function generateBase() {
            let base = callNumberInput.value.trim();
            if (!base) {
                base = '001';
            }
            return base;
        }

        function getNextControlNumber() {
            const rows = copiesContainer.querySelectorAll('tr');
            if (rows.length === 0) {
                const base = generateBase();
                return base + '-001';
            }
            // Get last control number and increment it
            const lastRow = rows[rows.length - 1];
            const lastCtrlInput = lastRow.querySelector('input.ctrl-number');
            if (lastCtrlInput) {
                const lastCtrl = lastCtrlInput.value;
                const parts = lastCtrl.split('-');
                if (parts.length === 2) {
                    const base = parts[0];
                    const suffix = parseInt(parts[1]) + 1;
                    return base + '-' + String(suffix).padStart(3, '0');
                }
            }
            const base = generateBase();
            return base + '-' + String(rows.length + 1).padStart(3, '0');
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

        function addCopyRow(ctrlValue = '') {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" name="control_numbers[]" class="form-control form-control-sm ctrl-number" value="${ctrlValue}" readonly></td>
                <td><input type="number" name="copy_year[]" class="form-control form-control-sm" min="1900" max="2100"></td>
                <td><input type="text" name="copy_status[]" class="form-control form-control-sm" value="available" readonly></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeCopyBtn">&times;</button></td>
            `;
            copiesContainer.appendChild(row);
            copiesInput.value = copiesContainer.querySelectorAll('tr').length;

            row.querySelector('.removeCopyBtn').addEventListener('click', function() {
                row.remove();
                copiesInput.value = copiesContainer.querySelectorAll('tr').length;
            });
        }

        addCopyBtn.addEventListener('click', function() {
            const nextCtrl = getNextControlNumber();
            addCopyRow(nextCtrl);
        });

        // Handle form submission - ensure custom category is properly selected
        form.addEventListener('submit', function(e) {
            const selectedValue = categorySelect.value;
            const customValue = otherInput.value.trim();
            
            // If user entered a custom category, make sure it's properly selected
            if (customValue && selectedValue !== customValue) {
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
            if (desired > current) {
                for (let i = current; i < desired; i++) {
                    const nextCtrl = getNextControlNumber();
                    addCopyRow(nextCtrl);
                }
            } else if (desired < current) {
                for (let i = current; i > desired; i--) {
                    copiesContainer.querySelectorAll('tr')[i-1].remove();
                }
            }
        });

        // initialize rows on page load with auto-incremented control numbers
        const initialCopies = parseInt(copiesInput.value) || 1;
        for (let i = 0; i < initialCopies; i++) {
            const nextCtrl = getNextControlNumber();
            addCopyRow(nextCtrl);
        }

        // run initial toggle
        toggleOther();
    });
</script>
@endpush
