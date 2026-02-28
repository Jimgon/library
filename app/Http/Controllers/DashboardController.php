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
        $totalBooks = Book::count();
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

    public function reports()
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

        return view('reports', compact(
            'totalTransactions','totalStudents','totalTeachers','booksInCirculation','overdueItems',
            'popularLabels','popularData','categoryLabels','categoryData','monthlyLabels','monthlyData'
        ));
    }
}
