<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Borrow;
use App\Models\Book;
use App\Models\User;
use App\Models\Teacher;
use App\Models\ActivityLog; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\DistributedBook;

class BorrowController extends Controller
{
    // Show form to borrow a book
    public function create()
    {
        $users = User::where(function($q) {
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
            $books = Book::where('status', 'available')
                ->whereNotIn('id', $borrowedBookIds)
                ->get();
        } else {
            $books = Book::where('status', 'available')->get();
        }

        $settings = DB::table('penalty_settings')->first();

        return view('borrow.create', compact('books', 'users', 'teachers', 'settings'));
    }

    // Show form to borrow a distributed book (now uses inventory books)
    public function createForDistribute()
    {
        // Only pass teachers from the separate Teacher collection
        $users = Teacher::whereNull('deleted_at')->orderBy('name')->get();

        // use regular books for distribution; show entire inventory (even zero copies)
        $books = Book::all();

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
        ]);

        $userId = $request->user_id;
        $bookIds = $request->book_ids;

        // Verify teacher exists
        $teacher = Teacher::find($userId);
        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher not found.');
        }

        $borrowDate = Carbon::parse($request->borrowed_at);
        $returnDate = Carbon::parse($request->due_date);

        $success = 0; $errors = [];

        foreach ($bookIds as $bookId) {
            $book = Book::find($bookId);
            if (!$book) {
                $errors[] = "Book {$bookId} not found";
                continue;
            }
            // determine availability
            $copies = $book->available_copies ?? $book->copies ?? 0;
            if ($copies < 1) {
                $errors[] = "{$book->title} is out of stock";
                continue;
            }
            try {
                $borrow = Borrow::create([
                    'user_id' => $userId,
                    'book_id' => $bookId,
                    'borrowed_at' => $borrowDate,
                    'due_date' => $returnDate,
                ]);

                // decrement inventory
                $book->available_copies = max(0, ($book->available_copies ?? 0) - 1);
                if (($book->available_copies ?? 0) < 1) {
                    $book->status = 'borrowed';
                }
                $book->save();

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
            'user_id'    => 'required|string',
            'borrowed_at' => 'required|date',
            'due_date'   => 'required|date|after_or_equal:borrowed_at',
            'book_ids'   => 'required|array|min:1|max:3',
            'book_ids.*' => 'required|string',
        ]);

        $userId = $request->user_id;
        $bookIds = $request->book_ids;

        // Determine if user is a student or teacher
        $user = User::find($userId);
        $isTeacher = false;
        if (!$user) {
            $user = Teacher::find($userId);
            if ($user) {
                $isTeacher = true;
            }
        }

        if (!$user) {
            return redirect()->back()->with('error', 'Student/Teacher not found.');
        }

        // Prevent borrowing if they already have 3 active borrows
        $activeBorrowCount = Borrow::where('user_id', $userId)
            ->whereNull('returned_at')
            ->count();
        
        if ($activeBorrowCount >= 3) {
            return redirect()->back()->with('error', 'You can only have 3 active book borrows at a time. Please return some books first.');
        }
        
        if ($activeBorrowCount + count($bookIds) > 3) {
            return redirect()->back()->with('error', 'You can only borrow ' . (3 - $activeBorrowCount) . ' more book(s). Currently borrowed: ' . $activeBorrowCount);
        }

        // Use provided dates instead of defaults
        $borrowDate = Carbon::parse($request->borrowed_at);
        $returnDate = Carbon::parse($request->due_date);

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($bookIds as $bookId) {
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
                $borrow = Borrow::create([
                    'user_id'     => $userId,
                    'book_id'     => $bookId,
                    'borrowed_at' => $borrowDate,
                    'due_date'    => $returnDate,
                    'returned_at' => null,
                ]);

                // Update book status
                $book->available_copies = ($book->available_copies ?? 1) - 1;
                if ($book->available_copies < 1) {
                    $book->status = 'borrowed';
                }
                $book->save();

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

        // Process only the requested quantity
        $processedCount = 0;
        $borrowCount = 0;
        
        foreach ($borrowIds as $id) {
            // Stop if we've already processed the requested quantity
            if ($borrowCount >= $quantityToReturn) {
                break;
            }

            $borrow = Borrow::with(['book', 'user'])->where('id', $id)->first();
            if (!$borrow || $borrow->returned_at) continue;

            // Allow admin to add a remark; prefer admin input but fallback to computed remark
            $dueDate = $borrow->due_date ? Carbon::parse($borrow->due_date) : null;
            $today = Carbon::now();

            $computedRemark = 'No Remarks';
            if ($dueDate && $today->gt($dueDate)) {
                $overdueDays = (int) ceil($today->diffInDays($dueDate));
                $computedRemark = "{$overdueDays} day(s) overdue";
            }

            $inputRemark = trim((string) $request->input('remark', ''));

            $borrow->remark = $inputRemark !== '' ? $inputRemark : $computedRemark;
            $borrow->notes = trim(($borrow->notes ? $borrow->notes . "\n" : '') . $request->input('notes', ''));

            // Mark as returned
            $borrow->returned_at = now();
            $borrow->save();

            // Update user's remark if there's a remark from return (except 'No Remarks')
            if ($borrow->remark && $borrow->remark !== 'No Remarks') {
                $user = User::find($borrow->user_id);
                if (!$user) {
                    // Try to load from Teacher model if not found in User model
                    $user = Teacher::find($borrow->user_id);
                }
                if ($user) {
                    $user->remark = $borrow->remark;
                    $user->save();
                }
            }

            // Safely update book status
            if ($borrow->book) {
                $borrow->book->status = 'available';
                $borrow->book->available_copies = min(
                    ($borrow->book->available_copies ?? 0) + 1,
                    $borrow->book->copies ?? PHP_INT_MAX
                );
                $borrow->book->save();
            } else {
                // Check if it's a distributed book
                $distBook = DistributedBook::find($borrow->book_id);
                if ($distBook) {
                    $distBook->copies = ($distBook->copies ?? 0) + 1;
                    $distBook->available_copies = ($distBook->available_copies ?? 0) + 1;
                    if ($distBook->copies > 0) $distBook->status = 'available';
                    $distBook->save();
                }
            }

            // Log activity with student name
            $studentName = $borrow->user ? ($borrow->user->name ?? ($borrow->user->first_name . ' ' . $borrow->user->last_name)) : 'Unknown';

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

        // After returning, go back to return list with success message
        if ($processedCount > 0) {
            return redirect()->route('borrow.return.index')
                     ->with('success', "Successfully returned {$processedCount} copy/copies!");
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
}


