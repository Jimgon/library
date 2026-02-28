@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h1 class="h2 mb-0">Students List</h1>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-upload me-2"></i>Import Students
            </button>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add New Student
            </a>
        </div>
    </div>

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Search Form --}}
    <form class="row g-3 mb-4" action="{{ route('users.index') }}" method="GET">
        <div class="col-md-8">
            <input class="form-control" type="search" name="search" value="{{ request('search') }}" placeholder="Search students by name, LRN..." onchange="this.form.submit()">
        </div>
        <div class="col-md-4">
            <select class="form-select" name="grade" onchange="this.form.submit()">
                <option value="">Year Level</option>
                @for($i = 7; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ request('grade') == $i ? 'selected' : '' }}>Grade {{ $i }}</option>
                @endfor
            </select>
        </div>
    </form>

{{-- Import Modal --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Students</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>CSV Format:</strong> Upload a CSV file with the following columns in order:
                        <ol class="mb-0 mt-2">
                            <li>Name (First Last - required)</li>
                            <li>Grade (optional, 7-12)</li>
                            <li>Strand (optional, ABM/GAS/STEM/HUMSS/ICT/TVL for Grade 11-12)</li>
                            <li>Section (optional)</li>
                            <li>LRN (optional, must be unique)</li>
                            <li>Phone Number (optional)</li>
                            <li>Address (optional)</li>
                        </ol>
                    </div>
                    <div class="mb-3">
                        <label for="file" class="form-label">Select File</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".csv,.txt" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="card-title mb-0">Students Management</h5>
    </div>
    <div class="card-body p-0">
        <div id="debugState" style="font-size:12px;color:#b00;background:#fffbe6;padding:4px 8px;margin-bottom:4px;display:none"></div>
        <div class="table-responsive">
            <form id="bulkDeleteForm" action="{{ route('users.bulkDelete') }}" method="POST">
                @csrf
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                        <th class="border-0 fw-semibold" style="width:36px">
                                <label class="form-check-label" for="selectAllUsersCheckbox">
                                <input type="checkbox" id="selectAllUsersCheckbox" class="form-check-input" aria-label="Select all students"
                                       onchange="(function(el){document.querySelectorAll('.user-checkbox').forEach(function(cb){cb.checked = el.checked; cb.dispatchEvent(new Event('change',{bubbles:true}));});})(this)"
                                >
                            </label>
                        </th>
                        <th class="border-0 fw-semibold">Name</th>
                        <th class="border-0 fw-semibold">Grade</th>
                        <th class="border-0 fw-semibold">Section</th>
                        <th class="border-0 fw-semibold">Strand</th>
                        <th class="border-0 fw-semibold d-none d-lg-table-cell">LRN</th>
                        <th class="border-0 fw-semibold d-none d-lg-table-cell">Phone</th>
                        <th class="border-0 fw-semibold d-none d-xl-table-cell">Address</th>
                        <th class="border-0 fw-semibold">Current Borrowed Books</th>
                        <th class="border-0 fw-semibold">Remarks</th>
                        <th class="border-0 fw-semibold d-none d-xl-table-cell">Notes</th>
                        <th class="border-0 fw-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @php
                            $activeBorrows = $user->borrows->whereNull('returned_at');
                            $totalOverdue = 0;
                            $today = \Carbon\Carbon::today();
                            // Define $displayName before use
                            $lastName = trim($user->last_name ?? '');
                            $firstName = trim($user->first_name ?? '');
                            if ($lastName && $firstName) {
                                $displayName = $lastName . ', ' . $firstName;
                            } elseif ($lastName) {
                                $displayName = $lastName;
                            } elseif ($firstName) {
                                $displayName = $firstName;
                            } else {
                                $displayName = '-';
                            }
                        @endphp

                        <tr>
                            <td>
                                <label class="form-check-label" for="userCheckbox{{ $user->id }}">
                                    <input type="checkbox" class="form-check-input user-checkbox" id="userCheckbox{{ $user->id }}" data-user-id="{{ $user->id }}" aria-label="Select student {{ $displayName }}">
                                </label>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $displayName }}</div>
                            </td>
                            @php
                                // derive grade, section, and strand robustly from available fields
                                $gradeDisplay = '-';
                                $sectionDisplay = '-';
                                $strandDisplay = '-';

                                $knownStrands = ['ABM','GAS','STEM','HUMSS','ICT','TVL'];

                                // First, try to get grade_section directly
                                $gsRaw = trim($user->grade_section ?? '');
                                
                                // If empty, fallback to checking other possible attributes
                                if (empty($gsRaw)) {
                                    // Try alternative attribute names via magic properties
                                    $gsRaw = trim($user->getAttribute('grade_section') ?? '') ?:
                                            trim($user->getAttribute('grade') ?? '') ?:
                                            trim($user->getAttribute('year_level') ?? '') ?:
                                            '';
                                }
                                
                                if ($gsRaw !== '') {
                                    // normalize separators and whitespace
                                    $gs = preg_replace('/[\-\/_,]+/', ' ', $gsRaw);
                                    $gs = preg_replace('/\s+/', ' ', $gs);
                                    $gs = trim($gs);

                                    // attempt to extract grade (7-12) if present
                                    $g = null;
                                    if (preg_match('/\b(7|8|9|10|11|12)\b/', $gs, $m)) {
                                        $g = $m[1];
                                    } elseif (preg_match('/^\s*(\d{1,2})\b/', $gs, $m)) {
                                        $g = $m[1];
                                    }

                                    // attempt to find known strand token
                                    $strandFound = null;
                                    foreach ($knownStrands as $st) {
                                        if (preg_match('/\b' . preg_quote($st, '/') . '\b/i', $gs)) { $strandFound = strtoupper($st); break; }
                                    }

                                    // remove grade and strand tokens from string to leave section/other text
                                    $tmp = $gs;
                                    if (!empty($g)) {
                                        $tmp = preg_replace('/\b' . preg_quote($g, '/') . '\b/', '', $tmp);
                                    }
                                    if (!empty($strandFound)) {
                                        $tmp = preg_replace('/\b' . preg_quote($strandFound, '/') . '\b/i', '', $tmp);
                                    }
                                    $tmp = preg_replace('/\s+/', ' ', trim($tmp));

                                    // decide displays
                                    $gradeDisplay = $g ?? '-';
                                    $sectionDisplay = $tmp !== '' ? $tmp : '-';
                                    $strandDisplay = $strandFound ?? '-';
                                }

                                $gradeDisplay = $gradeDisplay ?: '-';
                                $sectionDisplay = $sectionDisplay ?: '-';
                                $strandDisplay = $strandDisplay ?: '-';
                            @endphp
                            <td>
                                <span class="badge bg-secondary">{{ $gradeDisplay }}</span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ $sectionDisplay }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $strandDisplay }}</small>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <small>{{ $user->lrn ?? '-' }}</small>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <small>{{ $user->phone_number ?? '-' }}</small>
                            </td>
                            <td class="d-none d-xl-table-cell">
                                <small>{{ Str::limit($user->address, 25) ?? '-' }}</small>
                            </td>
                            <td>
                                @if($activeBorrows->count() > 0)
                                    <div class="d-flex flex-wrap gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#borrowedBooksModal{{ $user->id }}">
                                            <i class="bi bi-book"></i>
                                        </button>
                                        <small class="text-muted">{{ $activeBorrows->count() }} book(s)</small>
                                    </div>

                                    {{-- Borrowed Books Modal --}}
                                    <div class="modal fade" id="borrowedBooksModal{{ $user->id }}" tabindex="-1" aria-labelledby="borrowedBooksModalLabel{{ $user->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="borrowedBooksModalLabel{{ $user->id }}">
                                                        <i class="bi bi-book me-2"></i>Books Borrowed by {{ $user->first_name }} {{ $user->last_name }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row g-3">
                                                        @foreach($activeBorrows as $borrow)
                                                            @php
                                                                $borrowDate = $borrow->borrowed_at;
                                                                $dueDate    = $borrow->due_date;

                                                                // Calculate overdue days only if today is after due date
                                                                $overdueDays = 0;
                                                                if ($dueDate && $today->gt($dueDate)) {
                                                                    $overdueDays = (int) ceil($today->diffInDays($dueDate));
                                                                }

                                                                $totalOverdue += $overdueDays;

                                                                // Determine remark: prefer stored remark on borrow, otherwise computed overdue
                                                                $itemRemark = '';
                                                                $itemBadge = '';
                                                                if (!empty($borrow->remark)) {
                                                                    $itemRemark = $borrow->remark;
                                                                } else {
                                                                    $itemRemark = $overdueDays > 0 ? "{$overdueDays} day(s) overdue" : 'On Time';
                                                                }

                                                                $lower = strtolower($itemRemark);
                                                                // default classes for list item and badge
                                                                $liClass = '';
                                                                $itemBadgeClass = '';
                                                                if (str_contains($lower, 'overdue') || $lower === 'lost' || $lower === 'damage') {
                                                                    // overdue, lost, damage -> red
                                                                    $liClass = 'list-group-item-danger';
                                                                    $itemBadgeClass = 'bg-danger';
                                                                } elseif ($lower === 'late return') {
                                                                    // late return -> yellow
                                                                    $liClass = 'list-group-item-warning';
                                                                    $itemBadgeClass = 'bg-warning';
                                                                } else {
                                                                    // On Time or other -> green or neutral
                                                                    $liClass = '';
                                                                    $itemBadgeClass = (strtolower($itemRemark) === 'on time') ? 'bg-success' : 'bg-secondary';
                                                                }
                                                            @endphp
                                                            <div class="col-md-6 mb-3">
                                                                <div class="card h-100 {{ $liClass }}">
                                                                    <div class="card-body">
                                                                        <h6 class="card-title">{{ $borrow->book->title ?? 'Book not found' }}</h6>
                                                                        <p class="card-text small mb-2">
                                                                            <strong>Borrowed:</strong> {{ $borrowDate ? $borrowDate->format('M d, Y') : '-' }}<br>
                                                                            <strong>Due:</strong> {{ $dueDate ? $dueDate->format('M d, Y') : '-' }}
                                                                        </p>
                                                                        <span class="badge {{ $itemBadgeClass }}">{{ $itemRemark }}</span>
                                                                        @if($borrow->returned_at && !empty($borrow->notes))
                                                                            <p class="card-text small mt-2 mb-0">
                                                                                <strong>Notes:</strong> {{ $borrow->notes }}
                                                                            </p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                @php
                                    $displayRemark = $user->remark;
                                    if (!$displayRemark) {
                                        $displayRemark = $totalOverdue > 0 ? "{$totalOverdue} day(s) overdue" : 'Good Standing';
                                    }
                                @endphp

                                <span class="badge @if($totalOverdue > 0 && !$user->remark) bg-danger @elseif(in_array($user->remark, ['Cleared', 'Good Standing'])) bg-success @elseif($user->remark == 'Not Cleared') bg-danger @else bg-warning @endif">
                                    {{ $displayRemark }}
                                </span>
                            </td>

                            <td class="d-none d-xl-table-cell">
                                @php
                                    // Get recent notes from returned books (last 30 days)
                                    $recentReturns = $user->borrows->whereNotNull('returned_at')
                                        ->where('returned_at', '>=', \Carbon\Carbon::now()->subDays(30))
                                        ->whereNotNull('notes')
                                        ->where('notes', '!=', '')
                                        ->sortByDesc('returned_at')
                                        ->take(1); // Show last 1 note
                                @endphp

                                @if($recentReturns->count() > 0)
                                    <div>
                                        @foreach($recentReturns as $return)
                                            <div class="small text-muted">
                                                <em>{{ Str::limit($return->notes, 40) }}</em>
                                                <br><small>{{ $return->returned_at->format('M j') }}</small>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-dark" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-dark" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this student?');">
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
                            <td colspan="11" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                                    No students found.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>


                <!-- Pagination and Action Buttons -->
                <div class="d-flex justify-content-between align-items-center mt-4 p-3 border-top">
                    <div>
                        <button type="button" id="clearUserSelectBtn" class="btn btn-outline-secondary d-none">
                            <i class="bi bi-x-circle me-1"></i>Clear Selection
                        </button>
                    </div>
                    <div>
                        {{ $users->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                    <div>
                        <button type="submit" id="deleteSelectedUsersBtnBottom" class="btn btn-outline-danger d-none">
                            <i class="bi bi-trash me-1"></i>Delete Selected (<span id="selectedCountBottom">0</span>)
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Robust select-all handler: listens for change and works even if elements move
    function handleSelectAllChange(checked) {
        const userCheckboxes = document.querySelectorAll('.user-checkbox');
        console.log('Applying select-all -> checked:', checked, 'found:', userCheckboxes.length);
        userCheckboxes.forEach(cb => {
            cb.checked = checked;
            try { cb.dispatchEvent(new Event('change', { bubbles: true })); } catch (e) { console.warn('dispatchEvent failed', e); }
        });
        // ensure action buttons update immediately
        try { updateActionButtons(); } catch (e) { /* updateActionButtons may be defined later; ignore */ }
    }

    // Use delegated listener to be resilient against DOM differences
    document.addEventListener('change', function(e) {
        const target = e.target;
        if (!target) return;
        if (target.id === 'selectAllUsersCheckbox') {
            try {
                const isChecked = !!target.checked;
                console.log('Select-all toggled (delegated):', isChecked);
                handleSelectAllChange(isChecked);
            } catch (err) {
                console.error('Error in select-all handler', err);
            }
        }
    });

    // Show/hide action buttons based on selection count
    function updateActionButtons() {
        const clearBtn = document.getElementById('clearUserSelectBtn');
        const deleteBtn = document.getElementById('deleteSelectedUsersBtnBottom');
        const countSpan = document.getElementById('selectedCountBottom');
        const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
        console.log('updateActionButtons: checkedCount=', checkedCount);
        if (clearBtn) {
            if (checkedCount > 0) {
                clearBtn.classList.remove('d-none');
                clearBtn.classList.add('d-inline-block');
                clearBtn.style.removeProperty('display');
            } else {
                clearBtn.classList.add('d-none');
                clearBtn.classList.remove('d-inline-block');
            }
        }
        if (deleteBtn) {
            if (checkedCount >= 2) {
                deleteBtn.classList.remove('d-none');
                deleteBtn.classList.add('d-inline-block');
                deleteBtn.style.removeProperty('display');
            } else {
                deleteBtn.classList.add('d-none');
                deleteBtn.classList.remove('d-inline-block');
            }
        }
        if (countSpan) countSpan.textContent = checkedCount;
    }

    // Update buttons when any row checkbox changes
    document.addEventListener('change', function(e) {
        if (!e.target) return;
        if (e.target.classList && e.target.classList.contains('user-checkbox')) {
            updateActionButtons();
        }
    });

    // Also update once on load in case some checkboxes are pre-checked
    setTimeout(updateActionButtons, 50);

    // Safety: if the checkbox exists, ensure clicking label toggles it by listening to clicks too
    const selectAllElem = document.getElementById('selectAllUsersCheckbox');
    if (selectAllElem) {
        selectAllElem.addEventListener('click', function() {
            // small timeout to let the native toggle occur then read checked state
            setTimeout(() => { handleSelectAllChange(!!selectAllElem.checked); updateActionButtons(); }, 0);
        });
    } else {
        console.warn('selectAllUsersCheckbox not found in DOM');
    }

    // Clear selection button handler
    const clearBtn = document.getElementById('clearUserSelectBtn');
    if (clearBtn) {
        clearBtn.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = false);
            const selectAll = document.getElementById('selectAllUsersCheckbox');
            if (selectAll) { selectAll.checked = false; selectAll.indeterminate = false; }
            updateActionButtons();
        });
    }

    // Bulk delete form submit: collect selected IDs, confirm, add hidden inputs, then submit
    const bulkForm = document.getElementById('bulkDeleteForm');
    const deleteBtn = document.getElementById('deleteSelectedUsersBtnBottom');
    if (bulkForm) {
        // Track last clicked submit button for browsers that don't provide event.submitter
        let lastClickedSubmitterId = null;
        bulkForm.querySelectorAll('button[type="submit"]').forEach(btn => {
            btn.addEventListener('click', function() { lastClickedSubmitterId = this.id || null; });
        });

        bulkForm.addEventListener('submit', function(e) {
            // Determine which button triggered the submit. Prefer modern `e.submitter`, fallback to lastClickedSubmitterId.
            const submitterId = (typeof e.submitter !== 'undefined' && e.submitter) ? e.submitter.id : lastClickedSubmitterId;

            // Only intercept when the Delete Selected button triggered the submit.
            if (submitterId !== 'deleteSelectedUsersBtnBottom') {
                // allow normal submission for other buttons (pagination, filters, etc.)
                return;
            }

            // Prevent default to prepare payload for bulk delete
            e.preventDefault();
            // debug: show the form action and selected ids so we can confirm runtime URL
            console.log('bulkDeleteForm.action =', bulkForm.action);
            const selected = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.dataset.userId).filter(Boolean);
            console.log('bulkDelete selected IDs =', selected);
            if (selected.length < 2) {
                alert('Please select at least 2 students to delete.');
                return;
            }
            if (!confirm(`Are you sure you want to delete ${selected.length} selected students? This action cannot be undone.`)) {
                return;
            }
            // remove previous inputs
            bulkForm.querySelectorAll('input[name="selected_users[]"]').forEach(i => i.remove());
            selected.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_users[]';
                input.value = id;
                bulkForm.appendChild(input);
            });
            // submit normally
            bulkForm.submit();
        });
    }
});
</script>
@endpush

