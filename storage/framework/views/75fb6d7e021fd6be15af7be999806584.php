<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-1">Books Inventory</h4>

        </div>
    
    </div>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    
    <div class="modal fade" id="importBooksModal" tabindex="-1" aria-labelledby="importBooksModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="<?php echo e(route('books.import.post')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
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

    
    <form id="searchForm" method="GET" action="<?php echo e(route('books.index')); ?>" class="mb-4">
        <div class="row g-2">
            <div class="col-md-3">
                <input
                    type="search"
                    name="title"
                    class="form-control"
                    placeholder="Title"
                    value="<?php echo e(request('title')); ?>"
                >
            </div>
            <div class="col-md-3">
                <input
                    type="search"
                    name="author"
                    class="form-control"
                    placeholder="Author"
                    value="<?php echo e(request('author')); ?>"
                >
            </div>
            <div class="col-md-3">
                <input
                    type="search"
                    name="publisher"
                    class="form-control"
                    placeholder="Publisher"
                    value="<?php echo e(request('publisher')); ?>"
                >
            </div>
            <div class="col-md-2">
                <?php
                    $categories = $books->pluck('category')->filter()->unique()->sort()->values();
                ?>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat); ?>" <?php echo e(request('category') == $cat ? 'selected' : ''); ?>><?php echo e($cat); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary w-100" type="submit">
                    <i class="bi bi-search me-1"></i>
                </button>
            </div>
        </div>
    </form>

    
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
                    <?php $__empty_1 = true; $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <?php
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
                            ?>
                            <td>
                                <input type="checkbox" class="form-check-input book-checkbox" data-book-id="<?php echo e($book->id); ?>">
                            </td>
                            <td class="fw-semibold"><?php echo e($ctrlBase); ?></td>
                            <td>
                                <div class="fw-semibold"><?php echo e($book->title); ?></div>
                            </td>
                            <td><?php echo e($book->author); ?></td>
                            <td class="d-none d-lg-table-cell"><?php echo e($book->publisher ?? '-'); ?></td>
                            <td class="d-none d-lg-table-cell"><?php echo e($book->category ?? '-'); ?></td>
                            <td class="d-none d-md-table-cell"><small><?php echo e($book->isbn); ?></small></td>
                            <td class="d-none d-lg-table-cell">
                                <?php
                                    $avail = $book->available_copies ?? $book->copies ?? 0;
                                    $total = $book->copies ?? 0;
                                ?>
                                <?php echo e($avail); ?>/<?php echo e($total); ?>

                            </td>
                            <td>
                                <?php
                                    $copies = $book->copies ?? 0;
                                    $avail = $book->available_copies ?? null;
                                ?>
                                <?php if($copies == 0 || ($avail !== null && $avail == 0)): ?>
                                    <span class="badge bg-danger text-white">Out of Stock</span>
                                <?php elseif(($avail !== null && $avail > 0) || ($avail === null && $copies > 0)): ?>
                                    <span class="badge bg-success text-white">Available</span>
                                <?php elseif(isset($book->status) && trim($book->status) !== ''): ?>
                                    <span class="badge bg-secondary"><?php echo e(ucfirst($book->status)); ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Unknown</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-dark viewBookBtn" title="View"
                                        data-book-id="<?php echo e($book->id); ?>"
                                        data-book-title="<?php echo e($book->title); ?>"
                                        data-book-author="<?php echo e($book->author); ?>"
                                        data-book-publisher="<?php echo e($book->publisher ?? '-'); ?>"
                                        data-book-isbn="<?php echo e($book->isbn); ?>"
                                        data-book-category="<?php echo e($book->category ?? '-'); ?>"
                                        data-book-published-year="<?php echo e($book->published_year ?? '-'); ?>"
                                        data-book-pages="<?php echo e($book->pages ?? '-'); ?>"
                                        data-book-edition="<?php echo e($book->edition ?? '-'); ?>"
                                        data-book-condition="<?php echo e($book->condition ?? '-'); ?>"
                                        data-book-acquisition-type="<?php echo e($book->acquisition_type ?? '-'); ?>"
                                        data-book-source-of-funds="<?php echo e($book->source_of_funds ?? '-'); ?>"
                                        data-book-purchase-price="<?php echo e($book->purchase_price ? '₱' . number_format($book->purchase_price, 2) : '-'); ?>"
                                        data-book-cost-price="<?php echo e(isset($book->cost_price) && $book->cost_price !== null ? '₱' . number_format($book->cost_price, 2) : '-'); ?>"
                                        data-book-copies="<?php echo e($book->copies ?? 0); ?>" data-book-available-copies="<?php echo e($book->available_copies ?? 0); ?>"
                                        data-book-control-numbers='<?php echo json_encode($book->control_numbers ?? [], 15, 512) ?>'
                                        data-book-copy-status='<?php echo json_encode($book->copy_status ?? [], 15, 512) ?>'
                                        data-bs-toggle="modal" data-bs-target="#viewBookModal">
                                        <i class="bi bi-eye"></i>
                                    
                                    <form action="<?php echo e(route('books.destroy', $book->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this book?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-book-x fs-1 d-block mb-2"></i>
                                    No books found.
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <div class="d-flex justify-content-between align-items-center mt-4 p-3 border-top">
                <div>
                    <button type="button" id="clearSelectBtn" class="btn btn-outline-secondary" style="display: none;">
                        <i class="bi bi-x-circle me-1"></i>Clear Selection
                    </button>
                </div>
                <div>
                    <?php echo e($books->withQueryString()->links('pagination::bootstrap-5')); ?>

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

                
                <div class="mb-4" id="copiesSection" style="display: none;">
                    <h6 class="fw-semibold mb-3">Physical Copies (<span id="copiesCount">0</span>)</h6>
                    <div class="row" id="copiesContainer">
                    </div>
                </div>
                            <div class="mt-3 text-end">
                                <button type="button" id="printBookBtn" class="btn btn-outline-secondary">
                                    <i class="bi bi-printer me-1"></i>Print
                                </button>
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

<?php $__env->stopSection(); ?>

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

            // Store current book data in the modal for print button to access
            viewBookModal.dataset.currentBookData = JSON.stringify({
                title, author, isbn, category, publisher, publishedYear, pages, edition,
                condition, acquisitionType, sourceOfFunds, costPrice, purchasePrice,
                availableCopies, copies, controlNumbers, copyStatus
            });
        });
    }
});
</script>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Herd\library\resources\views/books/index.blade.php ENDPATH**/ ?>