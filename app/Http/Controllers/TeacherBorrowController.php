<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Borrow;
use Illuminate\Support\Facades\DB;
use App\Models\Teacher;

class TeacherBorrowController extends Controller
{

    public function create()
    {
        $settings = DB::table('penalty_settings')->first();
        // teachers come from dedicated Teacher model
        $teachers = \App\Models\Teacher::whereNull('deleted_at')->orderBy('name')->get();
        $books = Book::all()->filter(function($book) {
            return $book->available_copies > 0;
        });
        $users = collect(); // empty for student form
        return view('borrow.create', compact('settings', 'teachers', 'books', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'book_ids' => 'required|array|min:1',
            'book_ids.*' => 'required|integer',
            'borrowed_at' => 'required|date',
            'due_date' => 'required|date|after_or_equal:borrowed_at',
        ]);

        $teacher = Teacher::find($request->user_id);
        if (!$teacher) {
            return back()->with('error', 'Teacher not found.');
        }

        $success = 0;
        $errors = [];

        foreach ($request->book_ids as $bookId) {
            $book = Book::find($bookId);
            if (!$book) {
                $errors[] = "Book {$bookId} not found.";
                continue;
            }
            if (($book->available_copies ?? 0) < 1) {
                $errors[] = "No available copies for '{$book->title}'.";
                continue;
            }

            $alreadyBorrowed = Borrow::where('user_id', $request->user_id)
                ->where('book_id', $bookId)
                ->whereNull('returned_at')
                ->exists();
            if ($alreadyBorrowed) {
                $errors[] = "You have already borrowed '{$book->title}' and not returned it yet.";
                continue;
            }

            Borrow::create([
                'user_id' => $request->user_id,
                'book_id' => $bookId,
                'borrowed_at' => $request->borrowed_at,
                'due_date' => $request->due_date,
                'returned_at' => null,
                'role' => 'teacher',
            ]);

            $book->available_copies = max(0, ($book->available_copies ?? 0) - 1);
            if ($book->available_copies < 1) {
                $book->status = 'borrowed';
            }
            $book->save();

            $success++;
        }

        if ($success > 0) {
            $message = "{$success} book(s) borrowed successfully.";
            if (!empty($errors)) {
                $message .= ' Errors: ' . implode(' ', $errors);
            }
            return redirect()->route('teachers.index')->with('success', $message);
        }

        return back()->with('error', 'No books were borrowed. ' . implode(' ', $errors));
    }

    // Process a teacher book return
    public function processReturn(Request $request, $borrowId)
    {
        $borrow = Borrow::with(['book', 'user'])->where('id', $borrowId)->firstOrFail();

        $request->validate([
            'remark' => ['nullable', 'in:No Remarks,On Time,Late Return,Lost,Damage'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $dueDate = $borrow->due_date ? \Carbon\Carbon::parse($borrow->due_date) : null;
        $today = \Carbon\Carbon::now();
        $computedRemark = 'No Remarks';
        if ($dueDate && $today->gt($dueDate)) {
            $overdueDays = (int) ceil($today->diffInDays($dueDate));
            $computedRemark = "{$overdueDays} day(s) overdue";
        }
        $inputRemark = trim((string) $request->input('remark', ''));
        $borrow->remark = $inputRemark !== '' ? $inputRemark : $computedRemark;
        $borrow->notes = trim(($borrow->notes ? $borrow->notes . "\n" : '') . $request->input('notes', ''));
        $borrow->returned_at = now();
        $borrow->save();

        // Update user's remark if there's a remark from return (except 'No Remarks')
        if ($borrow->remark && $borrow->remark !== 'No Remarks') {
            $borrow->user->remark = $borrow->remark;
            $borrow->user->save();
        }

        // Update book status
        if ($borrow->book) {
            $borrow->book->status = 'available';
            $borrow->book->save();
        }
        return redirect()->route('books.index')->with('success', 'Book returned successfully!');
    }
}

