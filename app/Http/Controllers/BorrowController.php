<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Borrow;
use App\Models\Book;
use App\Models\User;
use App\Models\Teacher;
use App\Models\ActivityLog;
use App\Models\LostDamagedItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\DistributedBook;
use App\Models\BookCopy;

class BorrowController extends Controller
{
    private function normalizeCopyNumber(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '' || strtoupper($value) === 'N/A') {
            return null;
        }
        return $value;
    }

    private function pickAvailableCopy(Book $book, ?string $controlNumber = null): ?BookCopy
    {
        $query = $book->copies()
            ->where('status', 'available')
            ->where('is_lost_damaged', false);

        if ($controlNumber !== null) {
            $query->where('control_number', $controlNumber);
        } else {
            $query->orderByRaw("CASE WHEN control_number IS NULL THEN 1 ELSE 0 END")
                ->orderBy('control_number');
        }

        return $query->first();
    }

    private function syncBookCounts(Book $book): void
    {
        $total = $book->copies()->count();
        $available = $book->copies()
            ->where('status', 'available')
            ->where('is_lost_damaged', false)
            ->count();

        $book->forceFill([
            'copies' => $total,
            'available_copies' => $available,
            'status' => $available > 0 ? 'available' : 'borrowed',
        ])->save();
    }

    // Show form to borrow a book
    public function create()
    {
        $users = User::where(function(\Illuminate\Database\Eloquent\Builder$q) {
            $q->whereNull('role')->orWhere('role', '!=', 'teacher');
        })->whereNull('deleted_at')->get();
        // teachers are stored in separate model/table
        $teachers = Teacher::whereNull('deleted_at')->get();

        // If a user is selected, filter books they haven't borrowed yet
        $selectedUserId = request('user_id');
        if ($selectedUserId) {
            // Get book IDs already borrowed and not yet returned by this user
            $borrowedBookIds = Borrow::where('user_id', $selectedUserId)
                ->whereNull('returned_at')
                ->pluck('book_id')
                ->toArray();
            // Exclude books where ALL copies are marked as lost
            $books = Book::where('status', 'available')
                ->whereNotIn('id', $borrowedBookIds)
                ->get()
                ->filter(function ($book) {
                    // Only include books that have at least one available control number (not lost)
                    $availableCtrls = $book->getAvailableControlNumbers();
                    return !empty($availableCtrls);
                });
        } else {
            // Exclude books where ALL copies are marked as lost
            $books = Book::where('status', 'available')
                ->get()
                ->filter(function ($book) {
                    // Only include books that have at least one available control number (not lost)
                    $availableCtrls = $book->getAvailableControlNumbers();
                    return !empty($availableCtrls);
                });
        }

        $settings = DB::table('penalty_settings')->first();

        return view('borrow.create', compact('books', 'users', 'teachers', 'settings'));
    }

    // Show form to borrow a distributed book (now uses inventory books)
    public function createForDistribute()
    {
        // Only pass teachers from the separate Teacher collection
        $users = Teacher::whereNull('deleted_at')->orderBy('name', 'asc')->get();

        // use regular books for distribution; show only books with available copies (not all lost)
        $books = Book::all()
            ->filter(function ($book) {
                // Only include books that have at least one available control number (not lost)
                $availableCtrls = $book->getAvailableControlNumbers(); array_filter($availableCtrls);
                return !empty($availableCtrls);
            });

        $settings = DB::table('penalty_settings')->first();

        return view('borrow.distribute', compact('books','users','settings'));
    }

    // Store borrow for distributed books (inventory-backed)
    public function storeForDistribute(Request $request)
    {
        $request->validate([
            'user_id'    => 'required',
            'borrowed_at' => 'required|date',
            'due_date'   => 'required|date|after_or_equal:borrowed_at',
            'book_ids'   => 'required|array|min:1',
            'book_ids.*' => 'required|string',
            'copy_numbers' => 'nullable|array',
            'copy_numbers.*' => 'nullable|string',
        ]);

        $userId = $request->input('user_id');
        $bookIds = $request->input('book_ids');
        $copyNumbers = $request->input('copy_numbers') ?? [];

        // Verify teacher exists
        $teacher = Teacher::find($userId);
        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher not found.');
        }

        $borrowDate = Carbon::parse($request->input('borrowed_at'));
        $returnDate = Carbon::parse($request->input('due_date'));

        $success = 0; $errors = [];

        foreach ($bookIds as $index => $bookId) {
            $book = Book::find($bookId);
            if (!$book) {
                $errors[] = "Book {$bookId} not found";
                continue;
            }

            if ($book->available_copies < 1) {
                $errors[] = "{$book->title} is out of stock";
                continue;
            }
            try {
                $borrow = DB::transaction(function () use ($book, $bookId, $userId, $borrowDate, $returnDate, $copyNumbers, $index, &$errors) {
                    $controlNumber = $this->normalizeCopyNumber($copyNumbers[$index] ?? null);

                    $copyQuery = $book->copies()
                        ->where('status', 'available')
                        ->where('is_lost_damaged', false);
                    if ($controlNumber !== null) {
                        $copyQuery->where('control_number', $controlNumber);
                    } else {
                        $copyQuery->orderByRaw("CASE WHEN control_number IS NULL THEN 1 ELSE 0 END")
                            ->orderBy('control_number');
                    }

                    $bookCopy = $copyQuery->lockForUpdate()->first();
                    if (!$bookCopy) {
                        $errors[] = "{$book->title}: No available copy to borrow" . ($controlNumber ? " (Ctrl# {$controlNumber})" : '');
                        return null;
                    }

                    $borrow = Borrow::create([
                        'user_id'     => $userId,
                        'book_id'     => $bookId,
                        'book_copy_id'=> $bookCopy->id,
                        'borrowed_at' => $borrowDate,
                        'due_date'    => $returnDate,
                        'role'        => 'teacher',
                        'origin'      => 'distribution',
                        'copy_number' => $bookCopy->control_number,
                    ]);

                    $bookCopy->markAsBorrowed();
                    $this->syncBookCounts($book);

                    return $borrow;
                });

                if (!$borrow) {
                    continue;
                }

                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'Borrowed Distributed Book',
                    'target_type' => 'Book',
                    'target_id' => $book->id,
                    'details' => "{$book->title} borrowed by {$teacher->name}",
                ]);

                $success++;
            } catch (\Exception $e) {
                $errors[] = "Failed to borrow {$book->title}: " . $e->getMessage();
            }
        }

        $message = "{$success} book(s) borrowed.";
        if (!empty($errors)) $message .= ' Errors: ' . implode('; ', $errors);

        if ($success > 0) {
            return redirect()->route('borrow.return.index')->with('success', $message);
        } else {
            return redirect()->back()->with('error', 'Failed to borrow books. ' . implode('; ', $errors));
        }
    }

    // Store a borrowed book (or multiple books)
    public function store(Request $request)
    {
        $request->validate([
            'user_id'      => 'required|string',
            'borrowed_at'  => 'required|date',
            'due_date'     => 'required|date|after_or_equal:borrowed_at',
            // Allow up to teacher limit here; enforce per-type below.
            'book_ids'     => 'required|array|min:1|max:3',
            'book_ids.*'   => 'required|string',
            'copy_numbers' => 'nullable|array',
            'copy_numbers.*' => 'nullable|string',
            'borrow_type'  => 'nullable|in:student,teacher',
        ]);

        $userId = $request->input('user_id');
        $borrowType = $request->input('borrow_type') ?? 'student'; // default to student
        $bookIds = $request->input('book_ids');
        $copyNumbers = $request->input('copy_numbers') ?? [];

        // Determine if user is a student or teacher
        $user = User::find($userId);
        if (!$user) {
            $user = Teacher::find($userId);
        }
        
        // Use the provided borrow_type from the form
        $isTeacher = ($borrowType === 'teacher');
        $maxBooks = $isTeacher ? 3 : 3;

        if (!$user) {
            return redirect()->back()->with('error', 'Student/Teacher not found.');
        }

        // Prevent borrowing if they already have the max active borrows
        $activeBorrowCount = Borrow::where('user_id', $userId)
            ->where('role', $borrowType)
            ->whereNull('returned_at')
            ->count();
        
        if ($activeBorrowCount >= $maxBooks) {
            return redirect()->back()->with('error', "You can only have {$maxBooks} active book borrows at a time. Please return some books first.");
        }
        
        if ($activeBorrowCount + count($bookIds) > $maxBooks) {
            return redirect()->back()->with('error', 'You can only borrow ' . ($maxBooks - $activeBorrowCount) . ' more book(s). Currently borrowed: ' . $activeBorrowCount);
        }

        // Use provided dates instead of defaults
        $borrowDate = Carbon::parse($request->input('borrowed_at'));
        $returnDate = Carbon::parse($request->input('due_date'));

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($bookIds as $index => $bookId) {
            $book = Book::find($bookId);

            if (!$book) {
                $errorCount++;
                $errors[] = "Book ID {$bookId} not found.";
                continue;
            }

            if ($book->status !== 'available' || $book->available_copies < 1) {
                $errorCount++;
                $errors[] = "'{$book->title}' is not available.";
                continue;
            }

            // Create borrow record
            try {
                $borrow = DB::transaction(function () use ($book, $bookId, $userId, $borrowDate, $returnDate, $copyNumbers, $index, $borrowType, &$errorCount, &$errors) {
                    $controlNumber = $this->normalizeCopyNumber($copyNumbers[$index] ?? null);

                    $copyQuery = $book->copies()
                        ->where('status', 'available')
                        ->where('is_lost_damaged', false);
                    if ($controlNumber !== null) {
                        $copyQuery->where('control_number', $controlNumber);
                    } else {
                        $copyQuery->orderByRaw("CASE WHEN control_number IS NULL THEN 1 ELSE 0 END")
                            ->orderBy('control_number');
                    }

                    $bookCopy = $copyQuery->lockForUpdate()->first();
                    if (!$bookCopy) {
                        $errorCount++;
                        $errors[] = "{$book->title}: No available copy to borrow" . ($controlNumber ? " (Ctrl# {$controlNumber})" : '');
                        return null;
                    }

                    $borrow = Borrow::create([
                        'user_id'     => $userId,
                        'book_id'     => $bookId,
                        'book_copy_id'=> $bookCopy->id,
                        'borrowed_at' => $borrowDate,
                        'due_date'    => $returnDate,
                        'returned_at' => null,
                        'role'        => $borrowType,
                        'origin'      => 'personal',
                        'copy_number' => $bookCopy->control_number,
                    ]);

                    $bookCopy->markAsBorrowed();
                    $this->syncBookCounts($book);

                    return $borrow;
                });

                if (!$borrow) {
                    continue;
                }

                // Log activity
                $borrowerName = $isTeacher ? ($user->name ?? '') : trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action'  => 'Borrowed Book',
                    'target_type' => 'Book',
                    'target_id' => $book->id,
                    'details' => "Book '{$book->title}' borrowed by {$borrowerName}",
                ]);

                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = "Error borrowing '{$book->title}': " . $e->getMessage();
            }
        }

        $message = "{$successCount} book(s) borrowed successfully!";
        if ($errorCount > 0) {
            $message .= " ({$errorCount} failed: " . implode(', ', $errors) . ")";
        }

        return redirect()->route('borrow.create')->with(
            $errorCount > 0 ? 'warning' : 'success',
            $message
        );
    }

    // Show borrowed books that are not yet returned
    public function returnIndex()
    {
        $borrows = Borrow::with(['book', 'user'])->whereNull('returned_at')->orderBy('borrowed_at', 'desc')->get();

        $today = Carbon::now();

        foreach ($borrows as $borrow) {
            $dueDate = $borrow->due_date ? Carbon::parse($borrow->due_date) : null;

            // Overdue days always positive whole number
            $overdueDays = 0;
            if ($dueDate && $today->gt($dueDate)) {
                $overdueDays = (int) ceil($today->diffInDays($dueDate));
            }

            $borrow->overdue_days = $overdueDays;
            // Use remark instead of monetary penalty
            $borrow->remark = $overdueDays > 0 ? "{$overdueDays} day(s) overdue" : 'No Remarks';
        }

        return view('borrow.return', compact('borrows'));
    }

    // Process a book return
    public function processReturn(Request $request, $borrowId)
    {
        // Validate remark is one of allowed values
        $request->validate([
            'remark' => ['nullable', 'in:No Remarks,On Time,Late Return,Lost,Damage'],
            'remarks' => ['nullable', 'array'],
            'remarks.*' => ['nullable', 'in:No Remarks,On Time,Late Return,Lost,Damage'],
            'notes' => ['nullable', 'string', 'max:500'],
            'borrow_ids' => ['nullable', 'array'],
            'borrow_ids.*' => ['string'],
            'quantity_returned' => ['nullable', 'integer', 'min:1'],
        ]);

        // Get all borrow IDs to process (from hidden inputs if multiple, or use the route parameter)
        $borrowIds = $request->input('borrow_ids', []);
        if (empty($borrowIds)) {
            $borrowIds = [$borrowId];
        }

        // Get quantity to return (default: all of them)
        $quantityToReturn = (int) $request->input('quantity_returned', count($borrowIds));
        $quantityToReturn = min($quantityToReturn, count($borrowIds));

        // Get per-borrow remarks (new structure) or fallback to single remark (old structure)
        $perBorrowRemarks = $request->input('remarks', []);
        $fallbackRemark = trim((string) $request->input('remark', ''));

        // Process only the requested quantity
        $processedCount = 0;
        $borrowCount = 0;
        $hasLostOrDamaged = false;
        
        foreach ($borrowIds as $id) {
            // Stop if we've already processed the requested quantity
            if ($borrowCount >= $quantityToReturn) {
                break;
            }

            $borrow = Borrow::with(['book', 'user', 'bookCopy'])->where('id', $id)->first();
            if (!$borrow || $borrow->returned_at) continue;

            // Allow admin to add a remark; prefer admin input but fallback to computed remark
            $dueDate = $borrow->due_date ? Carbon::parse($borrow->due_date) : null;
            $today = Carbon::now();

            $computedRemark = 'No Remarks';
            if ($dueDate && $today->gt($dueDate)) {
                $overdueDays = (int) ceil($today->diffInDays($dueDate));
                $computedRemark = "{$overdueDays} day(s) overdue";
            }

            // Get per-borrow remark or use fallback
            $inputRemark = isset($perBorrowRemarks[$id]) ? trim((string) $perBorrowRemarks[$id]) : $fallbackRemark;

            $borrow->remark = $inputRemark !== '' ? $inputRemark : $computedRemark;
            $borrow->notes = trim(($borrow->notes ? $borrow->notes . "\n" : '') . $request->input('notes', ''));

            // Determine return_status based on remark and due date
            $returnStatus = $this->determineReturnStatus($borrow->remark, $dueDate, $today);
            $borrow->return_status = $returnStatus;

            // Record lost or damaged items
            if ($borrow->remark === 'Lost' || $borrow->remark === 'Damage') {
                $hasLostOrDamaged = true;
                LostDamagedItem::create([
                    'borrow_id' => $borrow->id,
                    'book_id' => $borrow->book_id,
                    'user_id' => $borrow->user_id,
                    'type' => $borrow->remark === 'Lost' ? 'lost' : 'damaged',
                    'copy_number' => $borrow->copy_number ?? 'BK-' . $borrow->book_id,
                    'remarks' => $borrow->notes,
                    'borrowed_at' => $borrow->borrowed_at,
                    'due_date' => $borrow->due_date,
                    'status' => 'active',
                    'role' => $borrow->role,
                    'origin' => $borrow->origin,
                ]);
            }

            // Mark as returned
            DB::transaction(function () use ($borrow) {
                $bookCopy = $borrow->bookCopy;
                if (!$bookCopy && $borrow->copy_number) {
                    $bookCopy = BookCopy::where('book_id', $borrow->book_id)
                        ->where('control_number', $borrow->copy_number)
                        ->lockForUpdate()
                        ->first();
                } elseif ($bookCopy) {
                    $bookCopy = BookCopy::where('id', $bookCopy->id)->lockForUpdate()->first();
                }

                if ($bookCopy) {
                    if ($borrow->remark === 'Lost') {
                        $bookCopy->markAsLost();
                    } elseif ($borrow->remark === 'Damage') {
                        $bookCopy->markAsDamaged();
                    } else {
                        $bookCopy->markAsAvailable();
                    }
                }

                $borrow->returned_at = now();
                $borrow->save();

                if ($borrow->book) {
                    $this->syncBookCounts($borrow->book);
                }
            });

            // Update user's remark if there's a remark from return (except 'No Remarks')
            if ($borrow->remark && $borrow->remark !== 'No Remarks') {
                $borrower = $borrow->getBorrower();
                if ($borrower) {
                    $borrower->remark = $borrow->remark;
                    $borrower->save();
                }
            }

            // Safely update distributed book status (legacy table)
            if (!$borrow->book) {
                // Check if it's a distributed book
                $distBook = DistributedBook::find($borrow->book_id);
                if ($distBook) {
                    $distBook->copies = ($distBook->copies ?? 0) + 1;
                    $distBook->available_copies = ($distBook->available_copies ?? 0) + 1;
                    if ($distBook->copies > 0) $distBook->status = 'available';
                    $distBook->save();
                }
            }

            // Log activity with student/teacher name
            $borrower = $borrow->getBorrower();
            $studentName = $borrower ? ($borrower->name ?? (($borrower->first_name ?? '') . ' ' . ($borrower->last_name ?? ''))) : 'Unknown';

            $bookTitle = $borrow->book ? $borrow->book->title : $borrow->book_id;

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action'  => 'Returned Book',
                'target_type' => 'Book',
                'target_id' => $borrow->book ? $borrow->book->id : null,
                'details' => "Book '{$bookTitle}' returned by {$studentName}",
            ]);

            $processedCount++;
            $borrowCount++;
        }

        // After returning, redirect based on whether lost/damaged items were processed
        if ($processedCount > 0) {
            $message = "Successfully returned {$processedCount} copy/copies!";
            
            // If any items were marked as lost or damaged, redirect to lost & damaged interface
            if ($hasLostOrDamaged) {
                return redirect()->route('books.lost-damage')
                    ->with('success', $message);
            }
            
            return redirect()->route('borrow.return.index')
                     ->with('success', $message);
        } else {
            return redirect()->route('borrow.return.index')
                     ->with('error', 'No books were returned.');
        }
    }

    // Generate printable receipt
    public function receipt($borrowId)
    {
        $borrow = Borrow::with(['user', 'book'])->findOrFail($borrowId);

        $borrowedAt = $borrow->borrowed_at ? Carbon::parse($borrow->borrowed_at) : null;
        $dueDate    = $borrow->due_date ? Carbon::parse($borrow->due_date) : null;
        $today      = Carbon::now();

        $overdueDays = 0;
        $remark = 'No Remarks';
        if ($dueDate && $today->gt($dueDate)) {
            $overdueDays = (int) ceil($today->diffInDays($dueDate));
            $remark = "{$overdueDays} day(s) overdue";
        }

        return view('borrow.receipt', compact('borrow', 'borrowedAt', 'dueDate', 'overdueDays', 'remark'));
    }

    // Generate printable receipts for all active borrows
    public function receiptAll()
    {
        $borrows = Borrow::with(['book', 'user'])->whereNull('returned_at')->orderBy('borrowed_at', 'desc')->get();
        $today = Carbon::now();

        foreach ($borrows as $borrow) {
            $borrowedAt = $borrow->borrowed_at ? Carbon::parse($borrow->borrowed_at) : null;
            $dueDate = $borrow->due_date ? Carbon::parse($borrow->due_date) : null;

            $overdueDays = 0;
            $remark = 'No Remarks';
            if ($dueDate && $today->gt($dueDate)) {
                $overdueDays = (int) ceil($today->diffInDays($dueDate));
                $remark = "{$overdueDays} day(s) overdue";
            }

            $borrow->borrowedAt = $borrowedAt;
            $borrow->dueDate = $dueDate;
            $borrow->overdueDays = $overdueDays;
            $borrow->remark = $remark;
        }

        return view('borrow.receipt-all', compact('borrows'));
    }

    /**
     * Determine the return status based on remark and due date
     *
     * @param string $remark The return remark
     * @param \Carbon\Carbon|null $dueDate The due date
     * @param \Carbon\Carbon $today Current date
     * @return string The return status
     */
    private function determineReturnStatus($remark, $dueDate = null, $today = null)
    {
        if (!$today) {
            $today = Carbon::now();
        }

        // Check for specific remarks that map to statuses
        if ($remark === 'Damage') {
            return Borrow::STATUS_DAMAGED_FOR_REPAIR;
        }

        if ($remark === 'Lost') {
            return Borrow::STATUS_LOST_AND_FOUND;
        }

        // If no due date or already returned, check if it's overdue
        if ($dueDate && $today->gt($dueDate)) {
            return Borrow::STATUS_LATE_RETURN;
        }

        // Default to returned on time
        return Borrow::STATUS_RETURNED_ON_TIME;
    }
}
