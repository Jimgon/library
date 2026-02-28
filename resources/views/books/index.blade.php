@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-1">Books Inventory</h4>

        </div>
    
    </div>

    {{-- Success Notification --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Error Notification --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Import Modal --}}
    <div class="modal fade" id="importBooksModal" tabindex="-1" aria-labelledby="importBooksModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('books.import.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importBooksModalLabel">
                            <i class="bi bi-upload me-2"></i>Import Books Data
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>CSV Format:</strong> Upload a CSV file with the following columns in order:
                            <ol class="mb-0 mt-2">
                                <li>Title (required)</li>
                                <li>Author (required)</li>
                                <li>Publisher (optional)</li>
                                <li>ISBN (required, must be unique)</li>
                                <li>Category (required)</li>
                                <li>Copies (required)</li>
                            </ol>
                        </div>
                        <div class="mb-3">
                            <label for="file" class="form-label">Select CSV File</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".csv,.xlsx,.xls,.txt" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Import Data</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Search Form --}}
    <form id="searchForm" method="GET" action="{{ route('books.index') }}" class="mb-4">
        <div class="row g-2">
            <div class="col-md-3">
                <input
                    type="search"
                    name="title"
                    class="form-control"
                    placeholder="Title"
                    value="{{ request('title') }}"
                >
            </div>
            <div class="col-md-3">
                <input
                    type="search"
                    name="author"
                    class="form-control"
                    placeholder="Author"
                    value="{{ request('author') }}"
                >
            </div>
            <div class="col-md-3">
                <input
                    type="search"
                    name="publisher"
                    class="form-control"
                    placeholder="Publisher"
                    value="{{ request('publisher') }}"
                >
            </div>
            <div class="col-md-2">
                @php
                    $categories = $books->pluck('category')->filter()->unique()->sort()->values();
                @endphp
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary w-100" type="submit">
                    <i class="bi bi-search me-1"></i>
                </button>
            </div>
        </div>
    </form>

    {{-- Books Table --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0">Book Collection</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 fw-semibold">
                                <input type="checkbox" id="selectAllCheckbox" class="form-check-input">
                            </th>
                            <th class="border-0 fw-semibold">Control #</th>
                            <th class="border-0 fw-semibold">Title</th>
                            <th class="border-0 fw-semibold">Author</th>
                            <th class="border-0 fw-semibold d-none d-lg-table-cell">Publisher</th>
                            <th class="border-0 fw-semibold d-none d-lg-table-cell">Category</th>
                            <th class="border-0 fw-semibold d-none d-md-table-cell">ISBN</th>
                            <th class="border-0 fw-semibold d-none d-lg-table-cell">Copies</th>
                            <th class="border-0 fw-semibold">Status</th>
                            <th class="border-0 fw-semibold text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($books as $book)
                        <tr>
                            @php
                                $ctrlBase = '-';
                                if (!empty($book->control_numbers) && is_array($book->control_numbers) && count($book->control_numbers) > 0) {
                                    $first = $book->control_numbers[0];
                                    $parts = explode('-', $first);
                                    $base = $parts[0] ?? ($book->call_number ?? '');
                                } else {
                                    $base = $book->call_number ?? '';
                                }
                                if (preg_match('/^\d+$/', $base)) {
                                    $ctrlBase = str_pad(ltrim($base, '0') === '' ? '0' : $base, 3, '0', STR_PAD_LEFT);
                                } elseif ($base !== '') {
                                    $ctrlBase = $base;
                                }
                            @endphp
                            <td>
                                <input type="checkbox" class="form-check-input book-checkbox" data-book-id="{{ $book->id }}">
                            </td>
                            <td class="fw-semibold">{{ $ctrlBase }}</td>
                            <td>
                                <div class="fw-semibold">{{ $book->title }}</div>
                            </td>
                            <td>{{ $book->author }}</td>
                            <td class="d-none d-lg-table-cell">{{ $book->publisher ?? '-' }}</td>
                            <td class="d-none d-lg-table-cell">{{ $book->category ?? '-' }}</td>
                            <td class="d-none d-md-table-cell"><small>{{ $book->isbn }}</small></td>
                            <td class="d-none d-lg-table-cell">
                                @php
                                    $avail = $book->available_copies ?? $book->copies ?? 0;
                                    $total = $book->copies ?? 0;
                                @endphp
                                {{ $avail }}/{{ $total }}
                            </td>
                            <td>
                                @php
                                    $copies = $book->copies ?? 0;
                                    $avail = $book->available_copies ?? null;
                                @endphp
                                @if($copies == 0 || ($avail !== null && $avail == 0))
                                    <span class="badge bg-danger text-white">Out of Stock</span>
                                @elseif(($avail !== null && $avail > 0) || ($avail === null && $copies > 0))
                                    <span class="badge bg-success text-white">Available</span>
                                @elseif(isset($book->status) && trim($book->status) !== '')
                                    <span class="badge bg-secondary">{{ ucfirst($book->status) }}</span>
                                @else
                                    <span class="badge bg-secondary">Unknown</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-dark viewBookBtn" title="View"
                                        data-book-id="{{ $book->id }}"
                                        data-book-title="{{ $book->title }}"
                                        data-book-author="{{ $book->author }}"
                                        data-book-publisher="{{ $book->publisher ?? '-' }}"
                                        data-book-isbn="{{ $book->isbn }}"
                                        data-book-category="{{ $book->category ?? '-' }}"
                                        data-book-published-year="{{ $book->published_year ?? '-' }}"
                                        data-book-pages="{{ $book->pages ?? '-' }}"
                                        data-book-edition="{{ $book->edition ?? '-' }}"
                                        data-book-condition="{{ $book->condition ?? '-' }}"
                                        data-book-acquisition-type="{{ $book->acquisition_type ?? '-' }}"
                                        data-book-source-of-funds="{{ $book->source_of_funds ?? '-' }}"
                                        data-book-purchase-price="{{ $book->purchase_price ? '₱' . number_format($book->purchase_price, 2) : '-' }}"
                                        data-book-cost-price="{{ isset($book->cost_price) && $book->cost_price !== null ? '₱' . number_format($book->cost_price, 2) : '-' }}"
                                        data-book-copies="{{ $book->copies ?? 0 }}" data-book-available-copies="{{ $book->available_copies ?? 0 }}"
                                        data-book-control-numbers='@json($book->control_numbers ?? [])'
                                        data-book-copy-status='@json($book->copy_status ?? [])'
                                        data-bs-toggle="modal" data-bs-target="#viewBookModal">
                                        <i class="bi bi-eye"></i>
                                    
                                    <form action="{{ route('books.destroy', $book->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this book?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-book-x fs-1 d-block mb-2"></i>
                                    No books found.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination and Action Buttons --}}
            <div class="d-flex justify-content-between align-items-center mt-4 p-3 border-top">
                <div>
                    <button type="button" id="clearSelectBtn" class="btn btn-outline-secondary" style="display: none;">
                        <i class="bi bi-x-circle me-1"></i>Clear Selection
                    </button>
                </div>
                <div>
                    {{ $books->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
                <div>
                    <button type="button" id="deleteSelectedBtnBottom" class="btn btn-outline-danger" style="display: none;">
                        <i class="bi bi-trash me-1"></i>Delete Selected (<span id="selectedCountBottom">0</span>)
                    </button>
                </div>
            </div>
        </div>
    </div>

    
</div>

{{-- View Book Details Modal --}}
<div class="modal fade" id="viewBookModal" tabindex="-1" aria-labelledby="viewBookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <div>
                    <h5 class="modal-title" id="viewBookModalLabel">
                        <i class="bi bi-book-fill me-2"></i>Book Details
                    </h5>
                    <p class="text-muted small mb-0">View complete information about this book</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Basic Information --}}
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Title</label>
                        <p class="fw-semibold" id="modalTitle">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Author</label>
                        <p class="fw-semibold" id="modalAuthor">-</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">ISBN</label>
                        <p class="fw-semibold" id="modalISBN">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Category</label>
                        <p class="fw-semibold" id="modalCategory">-</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Publisher</label>
                        <p class="fw-semibold" id="modalPublisher">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Published Year</label>
                        <p class="fw-semibold" id="modalPublishedYear">-</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Pages</label>
                        <p class="fw-semibold" id="modalPages">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Edition</label>
                        <p class="fw-semibold" id="modalEdition">-</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Condition</label>
                        <p class="fw-semibold" id="modalCondition">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Acquisition Type</label>
                        <p class="fw-semibold" id="modalAcquisitionType">-</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Source of Funds</label>
                        <p class="fw-semibold" id="modalSourceOfFunds">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Cost Price</label>
                        <p class="fw-semibold" id="modalCostPrice">-</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Purchase Price</label>
                        <p class="fw-semibold" id="modalPurchasePrice">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Available / Total Copies</label>
                        <p class="fw-semibold" id="modalCopies">-</p>
                    </div>
                </div>

                {{-- Physical Copies Section --}}
                <div class="mb-4" id="copiesSection" style="display: none;">
                    <h6 class="fw-semibold mb-3">Physical Copies (<span id="copiesCount">0</span>)</h6>
                    <div class="row" id="copiesContainer">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <a href="#" id="editBookBtn" class="btn btn-primary">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-arrow-left me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

<style>
    .btn-group {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        justify-content: center;
    }

    .btn-group form {
        display: contents;
        margin: 0;
        padding: 0;
    }

    .btn-group .btn {
        flex: 0 0 auto;
        margin: 0;
        padding: 0.375rem 0.75rem;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Bulk delete functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const bookCheckboxes = document.querySelectorAll('.book-checkbox');
    const deleteSelectedBtnBottom = document.getElementById('deleteSelectedBtnBottom');
    const clearSelectBtn = document.getElementById('clearSelectBtn');
    const selectedCountBottom = document.getElementById('selectedCountBottom');

    function updateDeleteButton() {
        const checkedCount = document.querySelectorAll('.book-checkbox:checked').length;
        selectedCountBottom.textContent = checkedCount;
        deleteSelectedBtnBottom.style.display = checkedCount >= 2 ? 'inline-block' : 'none';
        clearSelectBtn.style.display = checkedCount > 0 ? 'inline-block' : 'none';
    }

    // Initialize button state
    updateDeleteButton();

    selectAllCheckbox.addEventListener('change', function() {
        bookCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateDeleteButton();
    });

    bookCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            selectAllCheckbox.checked = Array.from(bookCheckboxes).every(cb => cb.checked);
            updateDeleteButton();
        });
    });

    clearSelectBtn.addEventListener('click', function() {
        bookCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        selectAllCheckbox.checked = false;
        updateDeleteButton();
    });

    function performDelete() {
        const selectedIds = Array.from(bookCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.dataset.bookId);
        
        if (selectedIds.length === 0) {
            alert('Please select at least one book to delete.');
            return;
        }

        if (!confirm(`Delete ${selectedIds.length} selected book(s)?`)) {
            return;
        }

        deleteSelectedBtnBottom.disabled = true;
        deleteSelectedBtnBottom.innerHTML = '<i class="bi bi-trash me-1"></i>Processing...';

        let successCount = 0;
        let errorCount = 0;
        let completedCount = 0;

        selectedIds.forEach((bookId, index) => {
            setTimeout(() => {
                const row = document.querySelector(`input.book-checkbox[data-book-id="${bookId}"]`).closest('tr');
                const deleteForm = row.querySelector('form');

                if (deleteForm) {
                    const formData = new FormData(deleteForm);
                    const action = deleteForm.getAttribute('action');

                    fetch(action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (response.ok || response.status === 302 || response.status === 301) {
                            successCount++;
                        } else {
                            errorCount++;
                        }
                    })
                    .catch(err => {
                        errorCount++;
                        console.error('Error:', err);
                    })
                    .finally(() => {
                        completedCount++;
                        if (completedCount === selectedIds.length) {
                            setTimeout(() => {
                                alert(`Successfully deleted ${successCount} book(s).`);
                                window.location.reload();
                            }, 300);
                        }
                    });
                }
            }, index * 800);
        });
    }

    deleteSelectedBtnBottom.addEventListener('click', performDelete);

    // Book Details Modal Handler
    const viewBookModal = document.getElementById('viewBookModal');
    if (viewBookModal) {
        viewBookModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const bookId = button.getAttribute('data-book-id');
            const title = button.getAttribute('data-book-title');
            const author = button.getAttribute('data-book-author');
            const publisher = button.getAttribute('data-book-publisher');
            const isbn = button.getAttribute('data-book-isbn');
            const category = button.getAttribute('data-book-category');
            const publishedYear = button.getAttribute('data-book-published-year');
            const pages = button.getAttribute('data-book-pages');
            const edition = button.getAttribute('data-book-edition');
            const condition = button.getAttribute('data-book-condition');
            const acquisitionType = button.getAttribute('data-book-acquisition-type');
            const sourceOfFunds = button.getAttribute('data-book-source-of-funds');
            const purchasePrice = button.getAttribute('data-book-purchase-price');
            const costPrice = button.getAttribute('data-book-cost-price');
            const copies = button.getAttribute('data-book-copies');
            const availableCopies = button.getAttribute('data-book-available-copies');
            const controlNumbers = JSON.parse(button.getAttribute('data-book-control-numbers') || '[]');
            const copyStatus = JSON.parse(button.getAttribute('data-book-copy-status') || '[]');

            // Populate basic info
            document.getElementById('modalTitle').textContent = title || '-';
            document.getElementById('modalAuthor').textContent = author || '-';
            document.getElementById('modalISBN').textContent = isbn || '-';
            document.getElementById('modalCategory').textContent = category || '-';
            document.getElementById('modalPublisher').textContent = publisher || '-';
            document.getElementById('modalPublishedYear').textContent = publishedYear || '-';
            document.getElementById('modalPages').textContent = pages || '-';
            document.getElementById('modalEdition').textContent = edition || '-';
            document.getElementById('modalCondition').textContent = condition || '-';
            document.getElementById('modalAcquisitionType').textContent = acquisitionType || '-';
            document.getElementById('modalSourceOfFunds').textContent = sourceOfFunds || '-';
            document.getElementById('modalCostPrice').textContent = costPrice || '-';
            document.getElementById('modalPurchasePrice').textContent = purchasePrice || '-';
            document.getElementById('modalCopies').textContent = `${availableCopies || '0'} / ${copies || '0'}`;

            // Populate physical copies
            const copiesContainer = document.getElementById('copiesContainer');
            const copiesSection = document.getElementById('copiesSection');
            copiesContainer.innerHTML = '';

            if (controlNumbers.length > 0) {
                document.getElementById('copiesCount').textContent = controlNumbers.length;
                controlNumbers.forEach((cn, idx) => {
                    const status = copyStatus[idx] || 'Available';
                    const badgeClass = status.toLowerCase() === 'available' ? 'bg-success' : 'bg-secondary';
                    const copyCard = `
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>Copy #${idx + 1}</strong>
                                    <span class="badge ${badgeClass}">${status}</span>
                                </div>
                                <p class="text-muted small mb-0">Ctrl: <strong>${cn}</strong></p>
                            </div>
                        </div>
                    `;
                    copiesContainer.innerHTML += copyCard;
                });
                copiesSection.style.display = 'block';
            } else {
                copiesSection.style.display = 'none';
            }

            // Update edit button link
            document.getElementById('editBookBtn').href = `/books/${bookId}/edit`;
        });
    }
});
</script>

