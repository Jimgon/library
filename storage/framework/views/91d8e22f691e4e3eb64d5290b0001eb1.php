<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-1">Book Inventory</h4>

        </div>
        <div class="d-flex gap-2 flex-wrap align-items-center">
<a href="<?php echo e(route('books.print')); ?>" class="btn btn-outline-dark">
                <i class="bi bi-printer me-1"></i>Print All
            </a>

            <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#importBooksModal">
                <i class="bi bi-download me-1"></i>Import CSV
            </button>
        
            <a href="<?php echo e(route('books.create')); ?>" class="btn btn-success">
                <i class="bi bi-plus-circle me-1"></i>Add Book
            </a>
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
                                        data-book-copy-years='<?php echo json_encode($book->copy_years ?? [], 15, 512) ?>'
                                        data-book-copy-conditions='<?php echo json_encode($book->copy_conditions ?? [], 15, 512) ?>'
                                        data-book-lost-control-numbers='<?php echo json_encode($book->lost_control_numbers ?? [], 15, 512) ?>'
                                        data-bs-toggle="modal" data-bs-target="#viewBookModal">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-dark addCopiesBtn" title="Add Copies"
                                        data-book-id="<?php echo e($book->id); ?>"
                                        data-book-title="<?php echo e($book->title); ?>"
                                        data-bs-toggle="modal" data-bs-target="#addCopiesModal">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                    <a href="<?php echo e(route('books.edit', $book->id)); ?>" class="btn btn-sm btn-outline-dark" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
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

            
            <ul class="nav nav-tabs border-bottom" id="bookDetailsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="bookInfoTab" data-bs-toggle="tab" data-bs-target="#bookInfoPane" type="button" role="tab" aria-controls="bookInfoPane" aria-selected="true">
                        <i class="bi bi-info-circle me-1"></i>Book Information
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="copiesTab" data-bs-toggle="tab" data-bs-target="#copiesPane" type="button" role="tab" aria-controls="copiesPane" aria-selected="false">
                        <i class="bi bi-stack me-1"></i>Physical Copies <span class="badge bg-secondary ms-2" id="copiesBadge">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="repairedTab" data-bs-toggle="tab" data-bs-target="#repairedPane" type="button" role="tab" aria-controls="repairedPane" aria-selected="false">
                        <i class="bi bi-wrench me-1"></i>Repaired <span class="badge bg-secondary ms-2" id="repairedBadge">0</span>
                    </button>
                </li>
            </ul>

            <div class="modal-body">
                <div class="tab-content" id="bookDetailsContent">
                    
                    <div class="tab-pane fade show active" id="bookInfoPane" role="tabpanel" aria-labelledby="bookInfoTab">
                        
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
                    </div>

                    
                    <div class="tab-pane fade" id="copiesPane" role="tabpanel" aria-labelledby="copiesTab">
                        
                        <div class="mb-3" id="conditionFiltersContainer">
                            <div class="btn-group" role="group" aria-label="Filter by condition">
                                <button type="button" class="btn btn-sm btn-outline-secondary condition-filter-btn active" data-condition="">
                                    <i class="bi bi-funnel me-1"></i>All
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success condition-filter-btn" data-condition="Brand New">
                                    <i class="bi bi-star-fill me-1"></i>Brand New
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-warning condition-filter-btn" data-condition="Old">
                                    <i class="bi bi-clock-history me-1"></i>Old
                                </button>
                            </div>
                        </div>
                        <div id="noCopiesMessage" class="alert alert-info" style="display: none;">
                            <i class="bi bi-info-circle me-2"></i>No physical copies available
                        </div>
                        <div class="table-responsive" id="copiesTableContainer" style="display: none;">
                            <table class="table table-hover table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 20%;">Control Number</th>
                                        <th style="width: 20%;">Condition</th>
                                        <th style="width: 20%;">Year Donated</th>
                                        <th style="width: 20%;">Status</th>
                                        <th style="width: 20%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="copiesTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    
                    <div class="tab-pane fade" id="repairedPane" role="tabpanel" aria-labelledby="repairedTab">
                        <div id="noRepairedMessage" class="alert alert-info" style="display: none;">
                            <i class="bi bi-info-circle me-2"></i>No repaired items
                        </div>
                        <div class="table-responsive" id="repairedTableContainer" style="display: none;">
                            <table class="table table-hover table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 30%;">Control Number</th>
                                        <th style="width: 35%;">Originally Reported</th>
                                        <th style="width: 35%;">Repaired Date</th>
                                    </tr>
                                </thead>
                                <tbody id="repairedTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" id="printBookBtn" class="btn btn-outline-secondary">
                    <i class="bi bi-printer me-1"></i>Print
                </button>

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


<div class="modal fade" id="addCopiesModal" tabindex="-1" aria-labelledby="addCopiesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <div>
                    <h5 class="modal-title" id="addCopiesModalLabel">
                        <i class="bi bi-plus-circle me-2"></i>Add Copies
                    </h5>
                    <p class="text-muted small mb-0" id="addCopiesBookTitle">Add copies to book</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCopiesForm" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="acquisitionYear" class="form-label">Acquisition Year</label>
                        <input 
                            type="number" 
                            class="form-control" 
                            id="acquisitionYear" 
                            name="acquisition_year" 
                            min="1900" 
                            max="<?php echo e(date('Y')); ?>"
                            placeholder="Enter acquisition year"
                        >
                        <small class="text-muted">Optional: Year when copies were acquired</small>
                    </div>
                    <div class="mb-3">
                        <label for="copiesCount" class="form-label">Number of Copies to Add</label>
                        <input 
                            type="number" 
                            class="form-control" 
                            id="copiesCount" 
                            name="additional_copies" 
                            min="1" 
                            max="1000"
                            placeholder="Enter number of copies"
                            required
                        >
                        <small class="text-muted">Must be a positive number between 1 and 1000</small>
                    </div>
                    <div class="card bg-light mb-3" id="copiesYearsCard" style="display: none;">
                        <div class="card-header">
                            <h6 class="mb-0">Acquisition Year for Each Copy</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0" id="copiesYearsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Copy #</th>
                                            <th>Acquisition Year</th>
                                        </tr>
                                    </thead>
                                    <tbody id="copiesYearsContainer">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check me-1"></i>Add Copies
                    </button>
                </div>
            </form>
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

    /* Table scrollbar styles */
    .table-responsive {
        max-height: 600px;
        overflow-y: auto;
    }

    /* Tab navigation styles */
    .nav-tabs {
        background-color: #f8f9fa;
        border-radius: 0.25rem 0.25rem 0 0;
    }

    .nav-tabs .nav-link {
        border: none;
        color: #495057;
        font-weight: 500;
        padding: 0.75rem 1rem;
    }

    .nav-tabs .nav-link:hover {
        color: #0d6efd;
        border-color: transparent;
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd;
        background-color: white;
        border-bottom: 2px solid #0d6efd;
    }

    /* Table styling for copies */
    #copiesTableBody tr:hover {
        background-color: #f8f9fa;
    }

    #copiesTableBody td {
        vertical-align: middle;
        padding: 0.75rem;
    }

    .badge {
        font-size: 0.8rem;
        padding: 0.35rem 0.6rem;
    }

    /* Modal improvements */
    .modal-body {
        padding: 1.5rem;
    }

    #viewBookModal .modal-footer {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    #viewBookModal .modal-footer #printBookBtn {
        order: -1;
    }

    #viewBookModal .modal-footer .btn-secondary {
        margin-left: auto;
    }

    .tab-content {
        padding-top: 1rem;
    }

    /* Center modal properly */
    #addBookModal .modal-dialog {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
    }

    #addBookModal .modal-content {
        max-height: 90vh;
    }

    /* Condition filter buttons in modal */
    #conditionFiltersContainer .btn-group {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    #conditionFiltersContainer .condition-filter-btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    #conditionFiltersContainer .condition-filter-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    #conditionFiltersContainer .condition-filter-btn.active {
        font-weight: 600;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
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

    // Delete Copy Function
    function deleteCopy(bookId, copyIndex) {
        if (!confirm('Are you sure you want to delete this copy?')) {
            return;
        }

        const formData = new FormData();
        formData.append('copy_index', copyIndex);

        fetch(`/books/${bookId}/delete-copy`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async response => {
            let data = {};
            try {
                data = await response.json();
            } catch (e) {}
            if (response.ok && data.success) {
                setTimeout(() => {
                    location.reload();
                }, 300);
            } else {
                alert((data && data.error) ? data.error : 'Error deleting copy. Please try again.');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Error deleting copy. Please check the console.');
        });
    }

    // Book Details Modal Handler
    const viewBookModal = document.getElementById('viewBookModal');
    if (viewBookModal) {
        viewBookModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const bookId = button.getAttribute('data-book-id');
            
            console.log('Opening book details modal for book ID:', bookId);
            
            // Show loading state
            const modalBody = viewBookModal.querySelector('.modal-body');
            const originalContent = modalBody.innerHTML;
            
            // Fetch fresh data from server to get updated acquisition years
            fetch(`/books/${bookId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('=== BOOK DATA RECEIVED ===');
                console.log('Book data:', data);
                console.log('Total Copies:', data.copies);
                console.log('Available Copies:', data.available_copies);
                console.log('Control Numbers:', data.control_numbers);
                console.log('Copy Status:', data.copy_status);
                console.log('Copy Years:', data.copy_years);
                console.log('Copy Conditions:', data.copy_conditions);
                console.log('Lost Control Numbers:', data.lost_control_numbers);
                console.log('==================');
                
                // Populate basic info
                document.getElementById('modalTitle').textContent = data.title || '-';
                document.getElementById('modalAuthor').textContent = data.author || '-';
                document.getElementById('modalISBN').textContent = data.isbn || '-';
                document.getElementById('modalCategory').textContent = data.category || '-';
                document.getElementById('modalPublisher').textContent = data.publisher || '-';
                document.getElementById('modalPublishedYear').textContent = data.published_year || '-';
                document.getElementById('modalPages').textContent = data.pages || '-';
                document.getElementById('modalEdition').textContent = data.edition || '-';
                document.getElementById('modalCondition').textContent = data.condition || '-';
                document.getElementById('modalAcquisitionType').textContent = data.acquisition_type || '-';
                document.getElementById('modalSourceOfFunds').textContent = data.source_of_funds || '-';
                document.getElementById('modalCostPrice').textContent = data.cost_price ? '₱' + parseFloat(data.cost_price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '-';
                document.getElementById('modalPurchasePrice').textContent = data.purchase_price ? '₱' + parseFloat(data.purchase_price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '-';
                document.getElementById('modalCopies').textContent = `${data.available_copies || '0'} / ${data.copies || '0'}`;

                // Populate physical copies
                const copiesTableBody = document.getElementById('copiesTableBody');
                const copiesTableContainer = document.getElementById('copiesTableContainer');
                const noCopiesMessage = document.getElementById('noCopiesMessage');
                const copiesBadge = document.getElementById('copiesBadge');
                copiesTableBody.innerHTML = '';

                const controlNumbers = data.control_numbers || [];
                const copyStatus = data.copy_status || [];
                const copyYears = data.copy_years || [];
                const copyConditions = data.copy_conditions || [];
                const lostControlNumbers = data.lost_control_numbers || [];
                const totalCopies = data.copies || 0;

                console.log('Displaying copies - Total:', totalCopies, 'Control Numbers Count:', controlNumbers.length);

                // Display copies if total copies > 0
                if (totalCopies > 0) {
                    // Display all control numbers (active copies only)
                    if (controlNumbers.length > 0) {
                        // Count only active copies (exclude lost/damaged)
                        let activeCopiesCount = 0;
                        controlNumbers.forEach((cn, index) => {
                            // Skip if this is a lost/damaged copy
                            if (lostControlNumbers && lostControlNumbers.includes(cn)) {
                                return;
                            }
                            activeCopiesCount++;
                        });
                        
                        // Update badge with count of active copies only
                        copiesBadge.textContent = activeCopiesCount;
                        
                        // Now display the rows
                        controlNumbers.forEach((cn, index) => {
                            // Skip if this is a lost/damaged copy
                            if (lostControlNumbers && lostControlNumbers.includes(cn)) {
                                return;
                            }

                            const status = (copyStatus && copyStatus[index]) ? copyStatus[index] : 'Available';
                            const badgeClass = status.toLowerCase() === 'available' ? 'bg-success' : 'bg-warning text-dark';
                            const acquisitionYear = (copyYears && copyYears[index]) ? copyYears[index] : (data.created_at ? new Date(data.created_at).getFullYear() : new Date().getFullYear());
                            const condition = (copyConditions && copyConditions[index]) ? copyConditions[index] : 'Brand New';
                            const conditionBadgeClass = condition.toLowerCase() === 'brand new' ? 'bg-success' : (condition.toLowerCase() === 'good' ? 'bg-info' : 'bg-warning text-dark');
                            
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>
                                    <strong>${cn}</strong>
                                </td>
                                <td>
                                    <span class="badge ${conditionBadgeClass}">${condition}</span>
                                </td>
                                <td>
                                    <strong>${acquisitionYear}</strong>
                                </td>
                                <td>
                                    <span class="badge ${badgeClass}">${status}</span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger deleteCopyBtn" data-book-id="${bookId}" data-copy-index="${index}" title="Delete copy">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            `;
                            copiesTableBody.appendChild(row);
                        });
                        
                        copiesTableContainer.style.display = 'block';
                        noCopiesMessage.style.display = 'none';
                    } else if (totalCopies > 0) {
                        // Copies exist but no control numbers assigned yet
                        // Create placeholder rows for the copies
                        const currentYear = new Date().getFullYear();
                        for (let i = 0; i < totalCopies; i++) {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>
                                    <span class="badge bg-secondary">Unassigned</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">Unknown</span>
                                </td>
                                <td>
                                    <strong>${currentYear}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-success">Available</span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Assign control number first">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            `;
                            copiesTableBody.appendChild(row);
                        }
                        
                        copiesTableContainer.style.display = 'block';
                        noCopiesMessage.style.display = 'none';
                    } else {
                        // Fallback: no copies to display
                        copiesTableContainer.style.display = 'none';
                        noCopiesMessage.style.display = 'block';
                    }
                    
                    // Attach delete event listeners to active deletion buttons
                    copiesTableBody.querySelectorAll('.deleteCopyBtn:not(:disabled)').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const bookId = this.getAttribute('data-book-id');
                            const copyIndex = this.getAttribute('data-copy-index');
                            deleteCopy(bookId, copyIndex);
                        });
                    });
                    
                    // Attach filter button listeners
                    const filterButtons = document.querySelectorAll('.condition-filter-btn');
                    filterButtons.forEach(button => {
                        button.addEventListener('click', function() {
                            const selectedCondition = this.getAttribute('data-condition');
                            
                            // Update active button state
                            filterButtons.forEach(btn => btn.classList.remove('active'));
                            this.classList.add('active');
                            
                            // Filter table rows
                            const rows = copiesTableBody.querySelectorAll('tr');
                            rows.forEach(row => {
                                if (selectedCondition === '') {
                                    // Show all
                                    row.style.display = '';
                                } else {
                                    // Filter by condition
                                    const conditionBadge = row.querySelector('td:nth-child(2) .badge');
                                    if (conditionBadge && conditionBadge.textContent.trim() === selectedCondition) {
                                        row.style.display = '';
                                    } else {
                                        row.style.display = 'none';
                                    }
                                }
                            });
                        });
                    });
                } else {
                    // No copies at all
                    copiesTableContainer.style.display = 'none';
                    noCopiesMessage.style.display = 'block';
                    copiesBadge.textContent = '0';
                }

                // Handle Repaired Items Tab
                const repairedItems = data.repaired_items || [];
                const repairedTableBody = document.getElementById('repairedTableBody');
                const repairedTableContainer = document.getElementById('repairedTableContainer');
                const noRepairedMessage = document.getElementById('noRepairedMessage');
                const repairedBadge = document.getElementById('repairedBadge');
                
                repairedTableBody.innerHTML = '';
                repairedBadge.textContent = repairedItems.length;
                
                if (repairedItems.length > 0) {
                    repairedItems.forEach((item) => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>
                                <strong>${item.copy_number}</strong>
                            </td>
                            <td>
                                <span>${item.original_report_date}</span>
                            </td>
                            <td>
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>${item.repaired_date}</span>
                            </td>
                        `;
                        repairedTableBody.appendChild(row);
                    });
                    
                    repairedTableContainer.style.display = 'block';
                    noRepairedMessage.style.display = 'none';
                } else {
                    repairedTableContainer.style.display = 'none';
                    noRepairedMessage.style.display = 'block';
                }

                // Update edit button link
                document.getElementById('editBookBtn').href = `/books/${bookId}/edit`;

                // Store book data in modal for print functionality
                viewBookModal.dataset.currentBookData = JSON.stringify({
                    title: data.title,
                    author: data.author,
                    isbn: data.isbn,
                    category: data.category,
                    publisher: data.publisher,
                    published_year: data.published_year,
                    pages: data.pages,
                    edition: data.edition,
                    condition: data.condition,
                    acquisition_type: data.acquisition_type,
                    source_of_funds: data.source_of_funds,
                    cost_price: data.cost_price ? '₱' + parseFloat(data.cost_price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '-',
                    purchase_price: data.purchase_price ? '₱' + parseFloat(data.purchase_price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '-',
                    available_copies: data.available_copies || '0',
                    copies: data.copies || '0',
                    control_numbers: data.control_numbers || [],
                    copy_status: data.copy_status || [],
                    copy_years: data.copy_years || []
                });
            })
            .catch(err => {
                console.error('❌ Error fetching book data:', err);
                console.error('Full error object:', err);
                
                // Set all fields to show error status
                document.getElementById('modalTitle').textContent = 'Error Loading Data';
                document.getElementById('modalAuthor').textContent = `Error: ${err.message}`;
                
                // Show error in copies section
                const copiesTableContainer = document.getElementById('copiesTableContainer');
                const noCopiesMessage = document.getElementById('noCopiesMessage');
                noCopiesMessage.innerHTML = `<i class="bi bi-exclamation-circle me-2"></i>Error loading copies: ${err.message}. Please check the browser console for details.`;
                noCopiesMessage.style.display = 'block';
                copiesTableContainer.style.display = 'none';
            });
        });
    }

        // Add Copies Modal Handler
        const addCopiesModal = document.getElementById('addCopiesModal');
        const copiesCountInput = document.getElementById('copiesCount');
        const acquisitionYearInput = document.getElementById('acquisitionYear');
        const copiesYearsCard = document.getElementById('copiesYearsCard');
        const copiesYearsContainer = document.getElementById('copiesYearsContainer');

        if (addCopiesModal) {
            addCopiesModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const bookId = button.getAttribute('data-book-id');
                const bookTitle = button.getAttribute('data-book-title');
            
                // Set form action
                const form = document.getElementById('addCopiesForm');
                form.action = `/books/${bookId}/add-copies`;

                // Update modal title
                document.getElementById('addCopiesBookTitle').textContent = `Add copies to "${bookTitle}"`;

                // Reset form and initialize with current year
                copiesCountInput.value = '';
                const currentYear = new Date().getFullYear();
                acquisitionYearInput.value = currentYear;
                acquisitionYearInput.placeholder = currentYear;
                copiesYearsCard.style.display = 'none';
                copiesYearsContainer.innerHTML = '';

                // Add listener for acquisition year changes AFTER modal is shown
                acquisitionYearInput.removeEventListener('input', updateYearsOnAcquisitionChange);
                acquisitionYearInput.addEventListener('input', updateYearsOnAcquisitionChange);
            });

            // Define the update function outside so it can be reused
            const updateYearsOnAcquisitionChange = function() {
                const yearValue = this.value;
                if (!yearValue || yearValue.trim() === '') {
                    return; // Don't update if acquisition year is empty
                }
                const yearInputs = copiesYearsContainer.querySelectorAll('.copy-year-input');
                yearInputs.forEach(yearInput => {
                    yearInput.value = yearValue;
                });
            };

            // Handle number of copies change
            copiesCountInput.addEventListener('change', function() {
                const count = parseInt(this.value) || 0;
                copiesYearsContainer.innerHTML = '';
                
                if (count > 0) {
                    copiesYearsCard.style.display = 'block';
                    const baseYear = acquisitionYearInput.value && acquisitionYearInput.value.trim() !== '' ? acquisitionYearInput.value : new Date().getFullYear();
                    
                    for (let i = 0; i < count; i++) {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${i + 1}</td>
                            <td><input type="number" name="copy_years[]" class="form-control form-control-sm copy-year-input" min="1900" max="<?php echo e(date('Y')); ?>" value="${baseYear}" required></td>
                        `;
                        copiesYearsContainer.appendChild(row);
                    }
                    
                    // Add event listeners to auto-fill years
                    const yearInputs = copiesYearsContainer.querySelectorAll('.copy-year-input');
                    yearInputs.forEach(input => {
                        input.addEventListener('input', function() {
                            const yearValue = this.value;
                            yearInputs.forEach(yearInput => {
                                yearInput.value = yearValue;
                            });
                        });
                    });
                } else {
                    copiesYearsCard.style.display = 'none';
                }
            });

            // Handle form submission
            document.getElementById('addCopiesForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Ensure all year inputs are filled before submission
                const yearInputs = copiesYearsContainer.querySelectorAll('.copy-year-input');
                const fallbackYear = acquisitionYearInput.value || new Date().getFullYear().toString();
                
                yearInputs.forEach(input => {
                    if (!input.value || input.value.trim() === '') {
                        input.value = fallbackYear;
                    }
                });
                
                // Get form data and submit
                const formData = new FormData(this);
                const action = this.getAttribute('action');
                
                fetch(action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (response.ok || response.status === 302 || response.status === 301) {
                        setTimeout(() => {
                            location.reload();
                        }, 300);
                    } else {
                        alert('Error adding copies. Please try again.');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert('Error adding copies. Please check the console.');
                });

                // Close modal
                const modal = bootstrap.Modal.getInstance(addCopiesModal);
                if (modal) {
                    modal.hide();
                }
            });
        }

    // Print button handler
    const printBtn = document.getElementById('printBookBtn');
    if (printBtn) {
        printBtn.addEventListener('click', function() {
            try {
                const viewBookModal = document.getElementById('viewBookModal');
                const bookData = JSON.parse(viewBookModal.dataset.currentBookData || '{}');
                const { title, author, isbn, category, publisher, published_year, pages, edition,
                    condition, acquisition_type, source_of_funds, cost_price, purchase_price,
                    available_copies, copies, control_numbers, copy_status, copy_years } = bookData;

                // Format current date
                const now = new Date();
                const dateStr = now.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });

                // Build print content with professional layout matching print.blade.php
                let printContent = `
                    <!-- School Header -->
                    <div style="text-align: center; margin-bottom: 30px; border-bottom: 3px solid #1e3a8a; padding-bottom: 20px;">
                        <img src="<?php echo e(asset('images/snhs-logo.png')); ?>" alt="SNHS Logo" style="width: 80px; height: 80px; object-fit: contain; margin-bottom: 10px;">
                        <h1 style="font-size: 24px; font-weight: bold; color: #1e3a8a; margin: 10px 0 5px 0;">Subic National High School</h1>
                        <p style="font-size: 14px; color: #555; margin: 0; margin-bottom: 15px;">Mangan-vaca, Subic, Zambales</p>
                        <h2 style="font-size: 20px; font-weight: bold; color: #1e3a8a; margin: 15px 0 0 0;">Book Details Report</h2>
                    </div>

                    <!-- Report Metadata -->
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 12px; color: #666;">
                        <div><strong>Report Date:</strong> ${dateStr}</div>
                        <div><strong>Time:</strong> ${timeStr}</div>
                    </div>

                    <!-- Main Book Details Table -->
                    <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                        <thead style="background-color: #1e3a8a; color: white; font-weight: bold;">
                            <tr>
                                <th style="padding: 10px; text-align: left; font-size: 12px; border: 1px solid #ddd;">Field</th>
                                <th style="padding: 10px; text-align: left; font-size: 12px; border: 1px solid #ddd;">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="background-color: #f9f9f9;">
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd; font-weight: bold;">Title</td>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd;">${title || '-'}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd; font-weight: bold;">Author</td>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd;">${author || '-'}</td>
                            </tr>
                            <tr style="background-color: #f9f9f9;">
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd; font-weight: bold;">ISBN</td>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd;">${isbn || '-'}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd; font-weight: bold;">Category</td>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd;">${category || '-'}</td>
                            </tr>
                            <tr style="background-color: #f9f9f9;">
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd; font-weight: bold;">Publisher</td>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd;">${publisher || '-'}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd; font-weight: bold;">Published Year</td>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd;">${published_year || '-'}</td>
                            </tr>
                            <tr style="background-color: #f9f9f9;">
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd; font-weight: bold;">Pages</td>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd;">${pages || '-'}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd; font-weight: bold;">Edition</td>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd;">${edition || '-'}</td>
                            </tr>
                            <tr style="background-color: #f9f9f9;">
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd; font-weight: bold;">Condition</td>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd;">${condition || '-'}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd; font-weight: bold;">Acquisition Type</td>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd;">${acquisition_type || '-'}</td>
                            </tr>
                            <tr style="background-color: #f9f9f9;">
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd; font-weight: bold;">Source of Funds</td>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd;">${source_of_funds || '-'}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd; font-weight: bold;">Cost Price</td>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd;">${cost_price || '-'}</td>
                            </tr>
                            <tr style="background-color: #f9f9f9;">
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd; font-weight: bold;">Purchase Price</td>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd;">${purchase_price || '-'}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd; font-weight: bold;">Available / Total Copies</td>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd;">${available_copies || '0'} / ${copies || '0'}</td>
                            </tr>
                        </tbody>
                    </table>
                `;

                // Add physical copies if available
                if (control_numbers && control_numbers.length > 0) {
                    printContent += `
                        <h3 style="color: #1e3a8a; margin-top: 30px; font-size: 16px; margin-bottom: 15px;">Physical Copies (${control_numbers.length})</h3>
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead style="background-color: #1e3a8a; color: white; font-weight: bold;">
                                <tr>
                                    <th style="padding: 10px; text-align: left; font-size: 12px; border: 1px solid #ddd;">Copy #</th>
                                    <th style="padding: 10px; text-align: left; font-size: 12px; border: 1px solid #ddd;">Control #</th>
                                    <th style="padding: 10px; text-align: center; font-size: 12px; border: 1px solid #ddd;">Year</th>
                                    <th style="padding: 10px; text-align: center; font-size: 12px; border: 1px solid #ddd;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
                    
                    control_numbers.forEach((cn, idx) => {
                        const status = copy_status?.[idx] || 'Available';
                        const year = copy_years?.[idx] || new Date().getFullYear();
                        const bgColor = idx % 2 === 0 ? '#f9f9f9' : 'white';
                        printContent += `
                            <tr style="background-color: ${bgColor};">
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd;">Copy #${idx + 1}</td>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd;">${cn}</td>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd; text-align: center;">${year}</td>
                                <td style="padding: 8px 10px; font-size: 11px; border: 1px solid #ddd; text-align: center;">${status}</td>
                            </tr>
                        `;
                    });
                    printContent += `
                            </tbody>
                        </table>
                    `;
                }

                // Add summary footer
                printContent += `
                    <div style="margin-top: 20px; padding-top: 15px; border-top: 2px solid #1e3a8a; font-size: 12px; text-align: right; color: #555;">
                        Generated by SNHS Library System
                    </div>
                `;

                // Open print window with matching styles
                const printWindow = window.open('', 'PrintWindow', 'width=900,height=700');
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="utf-8">
                        <title>Book Details - Print</title>
                        <style>
                            body {
                                font-family: 'Arial', sans-serif;
                                background: #fff;
                                color: #333;
                                padding: 20px;
                                margin: 0;
                            }
                            table {
                                width: 100%;
                                border-collapse: collapse;
                            }
                            h3 {
                                margin: 30px 0 15px 0;
                                color: #1e3a8a;
                                font-size: 16px;
                            }
                            @media print {
                                body { 
                                    padding: 10px;
                                    margin: 0;
                                }
                                table {
                                    page-break-inside: avoid;
                                    margin-top: 10px;
                                }
                                table tbody tr {
                                    page-break-inside: avoid;
                                }
                                h3 {
                                    page-break-after: avoid;
                                    margin-top: 25px;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        ${printContent}
                    </body>
                    </html>
                `);
                printWindow.document.close();
                printWindow.focus();
                
                // Delay print to allow content to render
                setTimeout(() => {
                    printWindow.print();
                }, 250);
            } catch (error) {
                console.error('Print error:', error);
                alert('Error printing book details. Please try again.');
            }
        });
    }

});
</script>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Herd\library\resources\views/books/catalog.blade.php ENDPATH**/ ?>