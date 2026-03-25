<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;
use App\Models\Borrow;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Total counts
        $totalBookTitles = Book::count();
        $totalBooks = Book::sum('copies');
        $totalUsers = User::count();
        $totalBorrows = Borrow::count();
        // Borrows with due date within 3 days and not returned
        $nearDueBorrows = Borrow::whereNull('returned_at')
            ->whereDate('due_date', '>=', now())
            ->whereDate('due_date', '<=', now()->addDays(3))
            ->with(['user', 'book'])
            ->get();
        // Students with unreturned books (paginated)
        $studentsWithUnreturned = User::whereHas('borrows', function ($q) {
                $q->whereNull('returned_at');
            })
            ->with(['borrows' => function ($q) {
                $q->whereNull('returned_at')->with('book');
            }])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(5, ['*'], 'studentsPage');

        // Available books (paginated)
        $availableBooks = Book::where('status', 'available')
            ->orderBy('title')
            ->paginate(5, ['*'], 'booksPage');
        
        // Enrich available books with accurate copy counts from BookCopy table
        $availableBooks->getCollection()->transform(function ($book) {
            $book->total_copies_actual = \App\Models\BookCopy::where('book_id', $book->id)->count();
            $book->available_copies_actual = \App\Models\BookCopy::where('book_id', $book->id)
                ->where('status', 'available')
                ->where('is_lost_damaged', false)
                ->count();
            return $book;
        });

        // Prepare data for Most Borrowed Books chart
        $mostBorrowedBooks = Borrow::select('book_id')
            ->whereNotNull('book_id')
            ->get()
            ->groupBy('book_id')
            ->map(fn($group) => count($group));

        $mostBorrowedBookLabels = [];
        $mostBorrowedBookData = [];

        foreach ($mostBorrowedBooks as $bookId => $count) {
            $book = Book::find($bookId);
            if ($book) {
                $mostBorrowedBookLabels[] = $book->title;
                $mostBorrowedBookData[] = $count;
            }
        }

        return view('dashboard', compact(
            'totalBooks',
            'totalUsers',
            'totalBorrows',
            'studentsWithUnreturned',
            'availableBooks',
            'mostBorrowedBookLabels',
            'mostBorrowedBookData'
            ,'nearDueBorrows'
        ));
    }

    public function reports(Request $request)
    {
        // Sample metrics/data for the reports view. Replace with real queries as needed.
        $totalTransactions = Borrow::count();
        $totalStudents = User::whereNull('deleted_at')->count();
        $totalTeachers = \App\Models\Teacher::whereNull('deleted_at')->count();
        $booksInCirculation = Borrow::whereNull('returned_at')->count();
        $overdueItems = Borrow::whereNull('returned_at')->where('due_date', '<', now())->count();

        // Popular books
        $popular = Borrow::select('book_id')
            ->whereNotNull('book_id')
            ->get()
            ->groupBy('book_id')
            ->map(fn($group) => count($group))
            ->sortDesc();

        $popularLabels = [];
        $popularData = [];
        foreach ($popular as $bookId => $count) {
            $book = Book::find($bookId);
            if ($book) {
                $popularLabels[] = $book->title;
                $popularData[] = $count;
            }
        }

        // Categories sample (aggregate from books)
        $categoryCounts = Book::select('category')
            ->get()
            ->groupBy('category')
            ->map(fn($g) => count($g));

        $categoryLabels = $categoryCounts->keys()->toArray();
        $categoryData = array_values($categoryCounts->toArray());

        // Monthly activity: last 12 months of borrowing activity
        $monthlyData = [];
        $monthlyLabels = [];
        
        // Start from 12 months ago through today
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $label = $month->format('M');
            $monthlyLabels[] = $label;
            
            // Count borrows created in this month (use created_at or borrowed_at if available)
            $count = Borrow::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            
            $monthlyData[] = $count;
        }

        // Detailed transactions with pagination and sorting
        $sortBy = $request->get('sort', 'borrowed_at');
        $sortOrder = $request->get('order', 'desc');
        $statusFilter = $request->get('status', 'all');

        // Validate sort parameters for security
        $sortBy = in_array($sortBy, ['id', 'borrowed_at', 'due_date', 'returned_at']) ? $sortBy : 'borrowed_at';
        $sortOrder = in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'desc';

        $transactionsQuery = Borrow::with(['book'])
            ->select('borrows.*');

        // Apply status filter
        if ($statusFilter === 'active') {
            $transactionsQuery->whereNull('returned_at');
        } elseif ($statusFilter === 'completed') {
            $transactionsQuery->whereNotNull('returned_at');
        }

        // Apply sorting
        $transactions = $transactionsQuery->orderBy($sortBy, $sortOrder)
            ->paginate(10, ['*'], 'transactionsPage');

        // Enrich transactions with borrower names
        $transactions->getCollection()->transform(function ($transaction) {
            $borrower = $transaction->role === 'teacher' 
                ? \App\Models\Teacher::withTrashed()->find($transaction->user_id)
                : \App\Models\User::withTrashed()->find($transaction->user_id);
            
            $transaction->borrower_name = $borrower 
                ? trim(($borrower->first_name ?? '') . ' ' . ($borrower->last_name ?? ''))
                : 'Unknown';
            
            $transaction->borrower_type = $transaction->role === 'teacher' ? 'Teacher' : 'Student';
            
            return $transaction;
        });

        return view('reports', compact(
            'totalTransactions','totalStudents','totalTeachers','booksInCirculation','overdueItems',
            'popularLabels','popularData','categoryLabels','categoryData','monthlyLabels','monthlyData',
            'transactions', 'sortBy', 'sortOrder', 'statusFilter'
        ));
    }
}
