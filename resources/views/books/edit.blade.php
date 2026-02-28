  @extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Edit Book</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('books.update', $book) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ $book->title }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="author" class="form-label">Author</label>
                        <input type="text" class="form-control" id="author" name="author" value="{{ $book->author }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="publisher" class="form-label">Publisher</label>
                        <input type="text" class="form-control" id="publisher" name="publisher" value="{{ $book->publisher }}">
                    </div>
                    <div class="mb-3">
                        <label for="isbn" class="form-label">ISBN</label>
                        <input type="text" class="form-control" id="isbn" name="isbn" value="{{ $book->isbn }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="call_number" class="form-label">Control Number Base</label>
                        <input type="text" class="form-control" id="call_number" name="call_number" value="{{ old('call_number', $book->call_number ?? $nextCtrlBase) }}">
                    </div>
                    @php
                        $selectedCat = old('category', $book->category);
                        $isOther = $selectedCat === 'other' || (!$categories->contains($selectedCat) && $selectedCat !== '');
                    @endphp
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ $selectedCat === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                            <option value="other" {{ $selectedCat === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <input type="text" name="other_category" id="other_category" class="form-control mt-2 @error('other_category') is-invalid @enderror" placeholder="Enter new category" value="{{ old('other_category', $isOther ? $selectedCat : '') }}" style="display: none;">
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('other_category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- added fields from create.blade --}}
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="published_year" class="form-label">Published Year</label>
                            <input
                                type="number"
                                name="published_year"
                                id="published_year"
                                class="form-control @error('published_year') is-invalid @enderror"
                                value="{{ old('published_year', $book->published_year) }}"
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
                                value="{{ old('cost_price', $book->cost_price) }}"
                                min="0"
                                step="0.01"
                            >
                            @error('cost_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="pages" class="form-label">Pages</label>
                            <input
                                type="number"
                                name="pages"
                                id="pages"
                                class="form-control @error('pages') is-invalid @enderror"
                                value="{{ old('pages', $book->pages) }}"
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
                                value="{{ old('edition', $book->edition) }}"
                                placeholder="e.g., 3rd Edition"
                            >
                            @error('edition')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="condition" class="form-label">Condition</label>
                            <select
                                name="condition"
                                id="condition"
                                class="form-select @error('condition') is-invalid @enderror"
                            >
                                <option value="">-- Select Condition --</option>
                                <option value="Brand New" {{ old('condition', $book->condition) === 'Brand New' ? 'selected' : '' }}>Brand New</option>
                                <option value="Old" {{ old('condition', $book->condition) === 'Old' ? 'selected' : '' }}>Old</option>
                            </select>
                            @error('condition')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
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
                                <option value="purchase" {{ old('acquisition_type', $book->acquisition_type) === 'purchase' ? 'selected' : '' }}>Purchase</option>
                                <option value="donation" {{ old('acquisition_type', $book->acquisition_type) === 'donation' ? 'selected' : '' }}>Donation</option>
                                <option value="exchange" {{ old('acquisition_type', $book->acquisition_type) === 'exchange' ? 'selected' : '' }}>Exchange</option>
                                <option value="grant" {{ old('acquisition_type', $book->acquisition_type) === 'grant' ? 'selected' : '' }}>Grant</option>
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
                                value="{{ old('source_of_funds', $book->source_of_funds) }}"
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
                                value="{{ old('purchase_price', $book->purchase_price) }}"
                                min="0"
                                step="0.01"
                            >
                            @error('purchase_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- end added fields --}}

                    <div class="mb-3">
                        <label for="copies" class="form-label">Number of Copies</label>
                        <input type="number" class="form-control" id="copies" name="copies" value="{{ $book->copies }}" min="1" required>
                    </div>

                    {{-- Physical Copies Details --}}
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
                                        @foreach($book->control_numbers ?? [] as $cn)
                                            <tr>
                                                <td><input type="text" name="control_numbers[]" class="form-control form-control-sm ctrl-number" value="{{ $cn }}" readonly></td>
                                                <td><input type="number" name="copy_year[]" class="form-control form-control-sm" min="1900" max="2100"></td>
                                                <td><input type="text" name="copy_status[]" class="form-control form-control-sm"></td>
                                                <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeCopyBtn">&times;</button></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Book</button>
                    <a href="{{ route('books.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
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

        function toggleOther() {
            if (categorySelect.value === 'other') {
                otherInput.style.display = 'block';
                otherInput.required = true;
            } else {
                otherInput.style.display = 'none';
                otherInput.required = false;
                if (categorySelect.value !== otherInput.value) {
                    otherInput.value = '';
                }
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

        function addCopyRow(ctrlValue = '') {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" name="control_numbers[]" class="form-control form-control-sm ctrl-number" value="${ctrlValue}" readonly></td>
                <td><input type="number" name="copy_year[]" class="form-control form-control-sm" min="1900" max="2100"></td>
                <td><input type="text" name="copy_status[]" class="form-control form-control-sm"></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeCopyBtn">&times;</button></td>
            `;
            copiesContainer.appendChild(row);
            copiesInput.value = copiesContainer.querySelectorAll('tr').length;
            updateControlNumbers();

            row.querySelector('.removeCopyBtn').addEventListener('click', function() {
                row.remove();
                copiesInput.value = copiesContainer.querySelectorAll('tr').length;
                updateControlNumbers();
            });
        }

        addCopyBtn.addEventListener('click', function() {
            addCopyRow();
        });

        categorySelect.addEventListener('change', toggleOther);
        otherInput.addEventListener('input', syncOtherCategory);

        callNumberInput.addEventListener('input', updateControlNumbers);

        copiesInput.addEventListener('change', function() {
            const desired = parseInt(copiesInput.value) || 0;
            const current = copiesContainer.querySelectorAll('tr').length;
            if (desired > current) {
                for (let i = current; i < desired; i++) addCopyRow();
            } else if (desired < current) {
                for (let i = current; i > desired; i--) {
                    copiesContainer.querySelectorAll('tr')[i-1].remove();
                }
                updateControlNumbers();
            }
        });

        // if there are existing rows already rendered (from Blade), sync copies input and update ctrl numbers
        if (copiesContainer.querySelectorAll('tr').length > 0) {
            copiesInput.value = copiesContainer.querySelectorAll('tr').length;
            updateControlNumbers();
        }

        toggleOther();
    });
</script>
@endpush