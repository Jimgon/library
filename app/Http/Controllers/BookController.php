<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\DistributedBook;
use App\Models\ActivityLog;
use App\Models\LostDamagedItem;
use App\Models\BookArchive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class BookController extends Controller
{
    /**
     * Show the import books form.
     */
    public function showImportForm()
    {
        return view('books.import');
    }

    /**
     * Print all books (printable view).
     */
    public function printAll()
    {
        $books = Book::orderBy('title', 'asc')->get();
        return view('books.print', compact('books'));
    }

    /**
     * Add copies to an existing book.
     */
    public function addCopies(Request $request, $bookId)
    {
        $request->validate([
            'additional_copies' => 'required|integer|min:1|max:1000',
            'acquisition_year' => 'nullable|integer|min:1900|max:'.date('Y'),
            'copy_years' => 'nullable|array',
        ]);

        $book = Book::findOrFail($bookId);

        // if (!$book) {
        //     return response()->json(['error' => 'Book not found'], 404);
        // }

        $additionalCopies = $request->input('additional_copies');
        $currentCopies = $book->copies ?? 0;
        $newTotal = $currentCopies + $additionalCopies;

        // Generate new control numbers for the additional copies
        $newControlNumbers = $book->control_numbers ?? [];
        
        // Extract the base from existing control numbers
        $baseNumber = '001'; // default
        if (!empty($newControlNumbers) && is_array($newControlNumbers)) {
            $firstCtrl = $newControlNumbers[0];
            $parts = explode('-', $firstCtrl);
            if (count($parts) === 2) {
                $baseNumber = $parts[0];
            }
        }
        
        // Generate new control numbers for the additional copies using the same base
        for ($i = 0; $i < $additionalCopies; $i++) {
            $nextSuffix = count($newControlNumbers) + 1;
            $newControlNumbers[] = $baseNumber . '-' . str_pad($nextSuffix, 3, '0', STR_PAD_LEFT);
        }

        // Handle copy years
        $existingYears = $book->copy_years ?? [];
        $submittedYears = $request->input('copy_years', []);
        $newYears = array_merge($existingYears, array_slice($submittedYears, 0, $additionalCopies));
        
        // Fill missing years with null
        while (count($newYears) < $newTotal) {
            $newYears[] = null;
        }

        // Update book with new copies and control numbers
        $book->update([
            'copies' => $newTotal,
            'available_copies' => ($book->available_copies ?? 0) + $additionalCopies,
            'control_numbers' => $newControlNumbers,
            'copy_years' => $newYears,
        ]);
        $book->save();

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Added Copies to Book',
            'details' => "Added {$additionalCopies} copies to '{$book->title}' (Total: {$newTotal})",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Successfully added {$additionalCopies} copies to {$book->title}"
        ]);
    }

    /**
     * Delete a specific copy of a book
     */
    public function deleteCopy(Request $request, $bookId)
    {
        Log::info('DeleteCopy called', [
            'book_id' => $bookId,
            'request' => $request->all(),
        ]);
        $request->validate([
            'copy_index' => 'required|integer|min:0',
        ]);

        $book = Book::findOrFail($bookId);

        // if (!$book) {
        //     Log::error('Book not found', ['book_id' => $bookId]);
        //     return response()->json(['error' => 'Book not found'], 404);
        // }

        $copyIndex = $request->input('copy_index');
        $controlNumbers = $book->control_numbers ?? [];
        $copyYears = $book->copy_years ?? [];
        $copyStatus = $book->copy_status ?? [];
        $copyConditions = $book->copy_conditions ?? [];

        Log::info('DeleteCopy arrays', [
            'copy_index' => $copyIndex,
            'control_numbers' => $controlNumbers,
            'copy_years' => $copyYears,
            'copy_status' => $copyStatus,
            'copy_conditions' => $copyConditions,
        ]);

        // Check if copy exists
        if (!isset($controlNumbers[$copyIndex])) {
            Log::error('Copy not found at index', ['copy_index' => $copyIndex, 'control_numbers' => $controlNumbers]);
            return response()->json(['error' => 'Copy not found'], 404);
        }

        // Archive the copy before removing
        BookArchive::create([
            'title' => $book->title,
            'author' => $book->author,
            'isbn' => $book->isbn,
            'publisher' => $book->publisher,
            'year' => $copyYears[$copyIndex] ?? null,
            'ctrl_number' => $controlNumbers[$copyIndex] ?? null,
            'condition' => $copyConditions[$copyIndex] ?? null,
        ]);

        // Remove the copy at the specified index
        $removedCtrl = $controlNumbers[$copyIndex];
        unset($controlNumbers[$copyIndex]);
        unset($copyYears[$copyIndex]);
        unset($copyStatus[$copyIndex]);
        unset($copyConditions[$copyIndex]);

        // Re-index arrays
        $controlNumbers = array_values($controlNumbers);
        $copyYears = array_values($copyYears);
        $copyStatus = array_values($copyStatus);
        $copyConditions = array_values($copyConditions);

        // Update book
        $book->update([
            'copies' => max(0, ($book->copies ?? 0) - 1),
            'available_copies' => max(0, ($book->available_copies ?? 0) - 1),
            'control_numbers' => $controlNumbers,
            'copy_years' => $copyYears,
            'copy_status' => $copyStatus,
            'copy_conditions' => $copyConditions,
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Deleted Copy from Book',
            'details' => "Deleted copy {$removedCtrl} from '{$book->title}' (Remaining: {$book->copies}) and archived it.",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted and archived copy from {$book->title}"
        ]);
    }

    /**
     * Handle the import of books from a file (Excel, CSV).
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls',
        ]);

        $errors = [];
        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        if (in_array($extension, ['xlsx', 'xls'])) {
            // Temporarily disable Excel import due to compatibility issues
            return redirect()->route('books.index')->with('error', 'Excel import is currently not available. Please use CSV format for now.');
        } else {
            $handle = fopen($file->getRealPath(), 'r');
            $rows = [];
            while (($row = fgetcsv($handle)) !== false) {
                $rows[] = $row;
            }
            fclose($handle);
        }

        // Skip header row if present
        if (isset($rows[0]) && is_array($rows[0]) && count($rows[0]) >= 2) {
            array_shift($rows);
        }

        foreach ($rows as $row) {
            // Basic validation: at least title, author, publisher, isbn, category, copies
            if (empty($row[0]) || empty($row[1]) || empty($row[3]) || empty($row[4]) || empty($row[5])) {
                $errors[] = "Missing required fields (title, author, isbn, category, copies) in row: " . json_encode($row);
                continue;
            }

            // Check if ISBN already exists
            if (!empty($row[3]) && Book::where('isbn', $row[3])->exists()) {
                $errors[] = "ISBN {$row[3]} already exists.";
                continue;
            }

            Book::create([
                'title'    => $row[0],
                'author'   => $row[1],
                'publisher' => $row[2] ?? null,
                'isbn'     => $row[3],
                'category' => $row[4],
                'copies'   => $row[5] ?? 1,
                'status'   => 'available',
            ]);
        }

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Imported Books',
            'details' => 'Books imported from ' . strtoupper($extension) . ' file.' . (!empty($errors) ? ' Errors: ' . implode(', ', $errors) : ''),
        ]);

        if (!empty($errors)) {
            return redirect()->route('books.index')->with('warning', 'Books imported with some errors: ' . implode(', ', $errors));
        }

        return redirect()->route('books.index')->with('success', 'Books imported successfully.');
    }
    public function index(Request $request)
    {
        $query = Book::query();

        // Individual field search
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }
        if ($request->filled('author')) {
            $query->where('author', 'like', '%' . $request->input('author') . '%');
        }
        if ($request->filled('publisher')) {
            $query->where('publisher', 'like', '%' . $request->input('publisher') . '%');
        }
        if ($request->has('category') && $request->input('category') !== null && $request->input('category') !== '') {
            $query->where('category', $request->input('category'));
        }

        $books = $query
            ->with(['borrows' => function($q) {
                $q->whereNull('returned_at')->with('user');
            }])
            ->orderBy('title', 'asc')
            ->paginate(10)
            ->withQueryString();

        $categories = Book::query()
            ->select('category')
            ->distinct()
            ->orderBy('category', 'asc')
            ->pluck('category');

        return view('books.index', compact('books', 'categories'));
    }

    public function catalog(Request $request)
    {
        // Don't filter by status - show all books in catalog regardless of availability
        $query = Book::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $books = $query
            ->orderBy('title', 'asc')
            ->paginate(12)
            ->withQueryString();

        // fetch distinct categories from DB, clean and organize
        $customCategories = Book::query()
            ->select('category')
            ->distinct()
            ->orderBy('category', 'asc')
            ->pluck('category')
            ->map(function ($cat) {
                return trim($cat);
            })
            ->filter()
            ->unique()
            ->reject(function ($cat) {
                return $cat === '';
            })
            ->values();

        // Use custom categories only (since we're showing all books now)
        $categories = $customCategories->toArray();
        $allCategories = $categories; // Use same categories for both filters

        return view('books.catalog', compact('books', 'categories', 'allCategories'));
    }

    /**
     * Display books formatted for distribution listing.
     */
    public function distribute(Request $request)
    {
        $query = DistributedBook::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        $books = $query
            ->with(['borrows' => function($q) {
                $q->whereNull('returned_at')->with('user');
            }])
            ->orderBy('title', 'asc')
            ->paginate(10)
            ->withQueryString();

        // View does not exist, fallback to index
        return redirect()->route('books.index')->with('warning', 'Distributed books listing not available.');
    }

    /**
     * Show form to add a book specifically for distribution.
     */
    public function distributeCreate()
    {
        // View does not exist, fallback to index
        return redirect()->route('books.index')->with('warning', 'Distributed book create form not available.');
    }

    /**
     * Store a book created for distribution.
     */
    public function distributeStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'edition' => 'nullable|string|max:100',
            'pages' => 'nullable|integer|min:1',
            'source_of_funds' => 'nullable|string|max:255',
            'cost_price' => 'nullable|numeric|min:0',
            'year' => 'nullable|integer|min:1900|max:'.(date('Y')+1),
            'copies' => 'required|integer|min:1',
            'condition' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
        ]);

        $data = $request->only(['title','author','publisher','edition','pages','source_of_funds','cost_price','year','copies','condition','status','isbn','category']);
        $data['status'] = $data['status'] ?? 'for_distribute';
        $data['available_copies'] = $data['copies'] ?? 0;

        $book = DistributedBook::create($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Added Book for Distribution',
            'details' => "Book '{$book->title}' added for distribution.",
        ]);

        // Route does not exist, fallback to index
        return redirect()->route('books.index')->with('success', 'Book added for distribution.');
    }

    /**
     * Import distributed books from CSV/Excel (CSV preferred).
     */
    public function distributeImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls',
        ]);

        $errors = [];
        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        if (in_array($extension, ['xlsx', 'xls'])) {
            // Route does not exist, fallback to index
            return redirect()->route('books.index')->with('error', 'Excel import is currently not available. Please use CSV format for now.');
        } else {
            $handle = fopen($file->getRealPath(), 'r');
            $rows = [];
            while (($row = fgetcsv($handle)) !== false) {
                $rows[] = $row;
            }
            fclose($handle);
        }

        // Skip header row if present
        if (isset($rows[0]) && is_array($rows[0]) && count($rows[0]) >= 2) {
            array_shift($rows);
        }

        foreach ($rows as $row) {
            // Map CSV columns (supports extended distribution columns):
            // 0: title, 1: author, 2: publisher, 3: isbn, 4: category, 5: copies,
            // 6: edition, 7: pages, 8: source_of_funds, 9: cost_price, 10: year, 11: condition, 12: status

            if (empty($row[0]) || empty($row[1]) || (empty($row[5]) && !isset($row[5]))) {
                $errors[] = "Missing required fields (title, author, copies) in row: " . json_encode($row);
                continue;
            }

            $isbn = $row[3] ?? null;
            if (!empty($isbn) && DistributedBook::where('isbn', $isbn)->exists()) {
                $errors[] = "ISBN {$isbn} already exists in distribution.";
                continue;
            }

            DistributedBook::create([
                'title' => $row[0],
                'author' => $row[1],
                'publisher' => $row[2] ?? null,
                'isbn' => $isbn,
                'category' => $row[4] ?? null,
                'copies' => isset($row[5]) ? (int) $row[5] : 1,
                'available_copies' => isset($row[5]) ? (int) $row[5] : 1,
                'edition' => $row[6] ?? null,
                'pages' => isset($row[7]) && is_numeric($row[7]) ? (int) $row[7] : null,
                'source_of_funds' => $row[8] ?? null,
                'cost_price' => isset($row[9]) && is_numeric($row[9]) ? (float) $row[9] : null,
                'year' => isset($row[10]) && is_numeric($row[10]) ? (int) $row[10] : null,
                'condition' => $row[11] ?? null,
                'status' => $row[12] ?? 'for_distribute',
            ]);
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Imported Distributed Books',
            'details' => 'Distributed books imported from CSV.' . (!empty($errors) ? ' Errors: ' . implode(', ', $errors) : ''),
        ]);

        if (!empty($errors)) {
            // Route does not exist, fallback to index
            return redirect()->route('books.index')->with('warning', 'Distributed books imported with some errors: ' . implode(', ', $errors));
        }

        // Route does not exist, fallback to index
        return redirect()->route('books.index')->with('success', 'Distributed books imported successfully.');
    }

    /**
     * Show a distributed book details.
     */
    public function distributeShow($id)
    {
        $book = DistributedBook::with(['borrows' => function($q){ $q->whereNull('returned_at')->with('user'); }])->find($id);
        if (!$book) abort(404);
        // View does not exist, fallback to index
        return redirect()->route('books.index')->with('warning', 'Distributed book details not available.');
    }

    /**
     * Edit form for a distributed book.
     */
    public function distributeEdit($id)
    {
        $book = DistributedBook::find($id);
        if (!$book) abort(404);
        // View does not exist, fallback to index
        return redirect()->route('books.index')->with('warning', 'Distributed book edit form not available.');
    }

    /**
     * Update a distributed book.
     */
    public function distributeUpdate(Request $request, $id)
    {
        $book = DistributedBook::find($id);
        if (!$book) return back()->with('error', 'Distributed book not found.');

        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'edition' => 'nullable|string|max:100',
            'pages' => 'nullable|integer|min:1',
            'source_of_funds' => 'nullable|string|max:255',
            'cost_price' => 'nullable|numeric|min:0',
            'year' => 'nullable|integer|min:1900|max:'.(date('Y')+1),
            'copies' => 'required|integer|min:0',
            'condition' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
        ]);

        $oldCopies = $book->copies ?? 0;
        $data = $request->only(['title','author','publisher','edition','pages','source_of_funds','cost_price','year','copies','condition','status','isbn','category']);

        $book->update($data);
        if ((int) ($data['copies'] ?? 0) > $oldCopies) {
            $book->available_copies = ($book->available_copies ?? 0) + ((int) $data['copies'] - (int) $oldCopies);
        } elseif ((int) ($data['copies'] ?? 0) < $oldCopies) {
            $book->available_copies = min(($book->available_copies ?? 0), (int) $data['copies']);
        }
        $book->save();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Updated Distributed Book',
            'details' => "Distributed book '{$book->title}' updated.",
        ]);

        // Route does not exist, fallback to index
        return redirect()->route('books.index')->with('success', 'Distributed book updated.');
    }

    /**
     * Delete a distributed book (separate from main books collection).
     */
    public function distributeDestroy($id)
    {
        $item = DistributedBook::find($id);

        if (!$item) {
            return back()->with('error', 'Distributed book not found.');
        }

        $title = $item->title;
        $item->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Deleted Distributed Book',
            'details' => "Distributed book '{$title}' deleted.",
        ]);

        // Route does not exist, fallback to index
        return redirect()->route('books.index')->with('success', 'Distributed book deleted.');
    }

    public function show(Book $book)
    {
        $book->load('borrows.user');
        
        // Return JSON if requested via AJAX
        if (request()->expectsJson() || request()->wantsJson()) {
            return response()->json($book, 200);
        }
        
        // View does not exist, fallback to index
        return redirect()->route('books.index')->with('warning', 'Book details not available.');
    }

    public function create()
    {
        // default categories to always show first
        $defaultCategories = ['MATH', 'SCIENCE', 'FILIPINO', 'ENGLISH', 'MAPEH', 'HISTORY'];

        // fetch distinct categories from DB, clean and exclude defaults
        $customCategories = Book::select('category')
            ->distinct()
            ->orderBy('category', 'asc')
            ->pluck('category')
            ->map(function ($cat) {
                return trim($cat);
            })
            ->filter()
            ->unique()
            ->reject(function ($cat) use ($defaultCategories) {
                return in_array(strtoupper($cat), $defaultCategories) || $cat === '';
            })
            ->values();

        // merge defaults + custom (defaults first)
        $allCategories = array_values(array_merge($defaultCategories, $customCategories->toArray()));

        // Calculate next control base from highest existing base in database
        $highestBase = 0;
        $books = Book::all();
        foreach ($books as $book) {
            if ($book->call_number) {
                $base = intval($book->call_number);
                if ($base > $highestBase) {
                    $highestBase = $base;
                }
            }
        }
        // Also check cache and use whichever is higher
        $cacheBase = Cache::get('ctrl_base', 0);
        $nextBase = max($highestBase, $cacheBase) + 1;
        $nextCtrlBase = str_pad($nextBase, 3, '0', STR_PAD_LEFT);
        
        return view('books.create', compact('allCategories', 'nextCtrlBase'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'author'   => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'isbn'     => 'required|numeric|digits_between:10,20',
            'category' => 'required|string|max:255',
            'other_category' => 'nullable|required_if:category,other|string|max:255',
            'call_number' => 'nullable|string|max:50|unique:books,call_number',
            'copies'   => 'required|integer|min:1',
            'published_year' => 'nullable|integer|min:1900|max:'.(date('Y')+1),
            'pages' => 'nullable|integer|min:1',
            'edition' => 'nullable|string|max:255',
            'condition' => 'nullable|string|in:Brand New,Old',
            'acquisition_type' => 'nullable|string|max:255',
            'purchase_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'source_of_funds' => 'nullable|string|max:255',
            'copy_year' => 'nullable|array',
            'copy_status' => 'nullable|array',
        ]);

        if (Book::where('isbn', $request->isbn)->exists()) {
            return back()->withErrors(['isbn' => 'This ISBN already exists.'])->withInput();
        }

        $categoryValue = trim($request->category === 'other' ? $request->other_category : $request->category);

        // prepare control numbers for each copy
        $submitted = $request->input('control_numbers', []);
        if (is_array($submitted) && count($submitted) === (int) $request->copies) {
            $controlNumbers = $submitted;
        } else {
            $base = trim($request->call_number ?: '');
            if ($base === '') {
                // use cache to keep global sequential base
                $next = Cache::increment('ctrl_base');
                $base = str_pad($next, 3, '0', STR_PAD_LEFT);
            } else {
                // if user manually provided numeric base, bump cache if needed
                if (preg_match('/^(\d{1,3})$/', $base, $m)) {
                    $num = intval($m[1]);
                    $current = Cache::get('ctrl_base', 0);
                    if ($num > $current) {
                        Cache::put('ctrl_base', $num);
                    }
                }
            }
            $controlNumbers = [];
            for ($i = 1; $i <= $request->copies; $i++) {
                $controlNumbers[] = $base . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            }
        }

        // Validate that control numbers don't already exist in any book
        $existingBooks = Book::all();
        foreach ($existingBooks as $book) {
            if (is_array($book->control_numbers)) {
                $duplicates = array_intersect($controlNumbers, $book->control_numbers);
                if (!empty($duplicates)) {
                    return back()->withErrors(['copies' => 'Control number(s) ' . implode(', ', $duplicates) . ' already exist in the system. Please refresh the page.'])->withInput();
                }
            }
        }

        // Prepare copy years and copy status arrays
        $copyYears = $request->input('copy_year', []);
        $copyStatus = $request->input('copy_status', []);
        
        // Ensure arrays are properly indexed and have correct count
        $copyYears = array_values(array_slice($copyYears, 0, $request->copies));
        $copyStatus = array_values(array_slice($copyStatus, 0, $request->copies));
        
        // Fill remaining slots with defaults if needed
        while (count($copyYears) < $request->copies) {
            $copyYears[] = null;
        }
        while (count($copyStatus) < $request->copies) {
            $copyStatus[] = 'available';
        }

        $book = Book::create([
            'title'    => $request->title,
            'author'   => $request->author,
            'publisher' => $request->publisher,
            'isbn'     => $request->isbn,
            'category' => $categoryValue,
            'call_number' => $request->call_number,
            'copies'   => $request->copies,
            'available_copies' => $request->copies,
            'control_numbers' => $controlNumbers,
            'copy_years' => $copyYears,
            'copy_status' => $copyStatus,
            'status'   => 'available',
            'published_year' => $request->published_year,
            'pages' => $request->pages,
            'edition' => $request->edition,
            'condition' => $request->condition,
            'acquisition_type' => $request->acquisition_type,
            'purchase_price' => $request->purchase_price,
            'cost_price' => $request->cost_price,
            'source_of_funds' => $request->source_of_funds,
        ]);

        // After successful creation, ensure cache is updated to prevent reuse of this base number
        if (preg_match('/^(\d{1,3})/', implode('', $controlNumbers), $m)) {
            $baseNum = intval($m[1]);
            $currentCache = Cache::get('ctrl_base', 0);
            if ($baseNum >= $currentCache) {
                Cache::put('ctrl_base', $baseNum);
            }
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Added Book',
            'details' => "Book '{$book->title}' by {$book->author} added.",
        ]);

        return redirect()->route('books.index')->with('success', 'Book added successfully.');
    }

    public function edit(Book $book)
    {
        $categories = Book::select('category')->distinct()->orderBy('category', 'asc')->pluck('category');
        
        // Calculate next control base from highest existing base in database
        $highestBase = 0;
        $books = Book::all();
        foreach ($books as $b) {
            if ($b->call_number) {
                $base = intval($b->call_number);
                if ($base > $highestBase) {
                    $highestBase = $base;
                }
            }
        }
        // Also check cache and use whichever is higher
        $cacheBase = Cache::get('ctrl_base', 0);
        $nextBase = max($highestBase, $cacheBase) + 1;
        $nextCtrlBase = str_pad($nextBase, 3, '0', STR_PAD_LEFT);
        
        return view('books.edit', compact('book', 'categories', 'nextCtrlBase'));
    }

    public function update(Request $request, Book $book)
    {
        $oldCopies = $book->copies ?? 0;
        $request->validate([
            'title'    => 'required|string|max:255',
            'author'   => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'isbn'     => 'required|string|max:20',
            'category' => 'required|string|max:255',
            'other_category' => 'required_if:category,other|string|max:255',
            'call_number' => 'nullable|string|max:50|unique:books,call_number,' . $book->id,
            'copies'   => 'required|integer|min:0',
            'published_year' => 'nullable|integer|min:1900|max:'.(date('Y')+1),
            'pages' => 'nullable|integer|min:1',
            'edition' => 'nullable|string|max:255',
            'condition' => 'nullable|string|in:Brand New,Old',
            'acquisition_type' => 'nullable|string|max:255',
            'purchase_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'source_of_funds' => 'nullable|string|max:255',
        ]);

        if (Book::where('isbn', $request->isbn)->where('id', '!=', $book->id)->exists()) {
            return back()->withErrors(['isbn' => 'This ISBN already exists.'])->withInput();
        }

        $categoryValue = $request->category === 'other' ? $request->other_category : $request->category;

        // Add new copies to existing
        $addCopies = (int) $request->copies;
        $newTotalCopies = $oldCopies + $addCopies;

        // Prepare control numbers
        $controlNumbers = $book->control_numbers ?? [];
        $base = trim($request->call_number ?: '');
        if ($addCopies > 0) {
            // Find the highest suffix used so far
            $maxSuffix = 0;
            foreach ($controlNumbers as $cn) {
                $parts = explode('-', $cn);
                if (count($parts) === 2 && $parts[0] === $base) {
                    $num = intval($parts[1]);
                    if ($num > $maxSuffix) $maxSuffix = $num;
                }
            }
            for ($i = 1; $i <= $addCopies; $i++) {
                $controlNumbers[] = $base . '-' . str_pad($maxSuffix + $i, 3, '0', STR_PAD_LEFT);
            }
        }

        // Save per-copy condition (copy_condition[])
        $copyConditions = $request->input('copy_condition', []);
        // If user removed or added copies, ensure the array matches the number of control numbers
        if (count($copyConditions) < count($controlNumbers)) {
            // Fill missing with 'Brand New'
            $copyConditions = array_pad($copyConditions, count($controlNumbers), 'Brand New');
        } elseif (count($copyConditions) > count($controlNumbers)) {
            $copyConditions = array_slice($copyConditions, 0, count($controlNumbers));
        }
        $book->update([
            'title' => $request->title,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'isbn' => $request->isbn,
            'category' => $categoryValue,
            'call_number' => $request->call_number,
            'copies' => $newTotalCopies,
            'control_numbers' => $controlNumbers,
            'published_year' => $request->published_year,
            'pages' => $request->pages,
            'edition' => $request->edition,
            'condition' => $request->condition,
            'acquisition_type' => $request->acquisition_type,
            'purchase_price' => $request->purchase_price,
            'cost_price' => $request->cost_price,
            'source_of_funds' => $request->source_of_funds,
            'copy_conditions' => $copyConditions,
        ]);

        // Update available_copies
        $book->update([
            'available_copies' => ($book->available_copies ?? 0) + $addCopies,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Updated Book',
            'details' => "Book '{$book->title}' updated.",
        ]);

        return redirect()->route('books.index')->with('success', 'Book updated successfully.');

        return redirect()->route('books.index')->with('success', 'Book updated successfully.');
    }

    public function destroy(Book $book)
    {
        $title = $book->title;
        $author = $book->author;

        $book->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Deleted Book',
            'details' => "Book '{$title}' by {$author} deleted.",
        ]);

        return redirect()->route('books.index')->with('success', 'Book deleted successfully.');
    }

    /**
     * Get the next control number base via AJAX.
     */
    public function getNextControlBase()
    {
        $nextBase = Cache::get('ctrl_base', 0) + 1;
        $paddedBase = str_pad($nextBase, 3, '0', STR_PAD_LEFT);
        return response()->json(['nextBase' => $paddedBase]);
    }

    /**
     * Show lost and damaged items page.
     */
    public function lostDamage(Request $request)
    {
        $ctrlNumberSearch = $request->query('ctrl_number', '');
        $bookSearch = $request->query('book', '');
        $borrowerSearch = $request->query('borrower', '');
        $borrowedDateSearch = $request->query('borrowed_date', '');
        $filterType = $request->query('type', '');

        // Get all active records for counting
        $allRecords = LostDamagedItem::where('status', 'active')
            ->with(['borrow.user', 'book', 'borrower'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Build filtered records query
        $query = LostDamagedItem::where('status', 'active')
            ->with(['borrow.user', 'book', 'borrower']);

        // Apply type filter
        if ($filterType && in_array($filterType, ['lost', 'damaged'])) {
            $query->where('type', $filterType);
        }

        // Apply borrowed date search filter at query level for efficiency
        if ($borrowedDateSearch) {
            $query->whereHas('borrow', function($q) use ($borrowedDateSearch) {
                $q->whereDate('borrowed_at', '=', $borrowedDateSearch);
            });
        }

        $records = $query->orderBy('created_at', 'desc')->get()
            ->map(function($record) {
                // Determine borrower name - check all possible sources
                $borrower_name = 'Unknown';
                $borrower_lrn = 'N/A';
                
                // First priority: borrow.user (student)
                if ($record->borrow && $record->borrow->user) {
                    $user = $record->borrow->user;
                    $borrower_name = $user->name ?? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?? 'Unknown';
                    $borrower_lrn = $user->lrn ?? 'N/A';
                }
                // Fallback: Query user directly using user_id from lost_damaged_items
                elseif ($record->user_id) {
                    $user = \App\Models\User::find($record->user_id);
                    if ($user) {
                        $borrower_name = $user->name ?? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?? 'Unknown';
                        $borrower_lrn = $user->lrn ?? 'N/A';
                    }
                }
                // Second priority: Teacher (if borrow has role=teacher)
                if ($borrower_name === 'Unknown' && $record->borrow && $record->borrow->role === 'teacher') {
                    $teacher = \App\Models\Teacher::find($record->user_id);
                    if ($teacher && $teacher->name) {
                        $borrower_name = $teacher->name;
                        $borrower_lrn = 'N/A';
                    }
                }
                // Third priority: Direct borrower relationship  
                if ($borrower_name === 'Unknown' && $record->borrower && $record->borrower->name) {
                    $borrower_name = $record->borrower->name ?? trim(($record->borrower->first_name ?? '') . ' ' . ($record->borrower->last_name ?? '')) ?? 'Unknown';
                    $borrower_lrn = $record->borrower->lrn ?? 'N/A';
                }
                
                $record->borrower_name = $borrower_name;
                $record->borrower_lrn = $borrower_lrn;
                return $record;
            });

        // Apply borrower search filter after enrichment
        if ($borrowerSearch) {
            $records = $records->filter(function($record) use ($borrowerSearch) {
                return stripos($record->borrower_name, $borrowerSearch) !== false;
            });
        }

        // Apply control number search filter after enrichment
        if ($ctrlNumberSearch) {
            $records = $records->filter(function($record) use ($ctrlNumberSearch) {
                $ctrlNum = $record->borrow?->copy_number ?? $record->copy_number ?? '';
                return stripos($ctrlNum, $ctrlNumberSearch) !== false;
            });
        }

        // Apply book search filter after enrichment
        if ($bookSearch) {
            $records = $records->filter(function($record) use ($bookSearch) {
                $bookTitle = $record->book ? $record->book->title : 'Unknown';
                return stripos($bookTitle, $bookSearch) !== false;
            });
        }

        // Count by type (from unfiltered records)
        $lostCount = $allRecords->where('type', 'lost')->count();
        $damagedCount = $allRecords->where('type', 'damaged')->count();
        $totalCount = $allRecords->count();

        // Get history logs (non-active records)
        $history = LostDamagedItem::where('status', '!=', 'active')
            ->with(['borrow.user', 'book', 'borrower'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function($item) {
                // Determine borrower name - check all possible sources
                $borrower_name = 'Unknown';
                
                // First priority: borrow.user (student)
                if ($item->borrow && $item->borrow->user) {
                    $user = $item->borrow->user;
                    $borrower_name = $user->name ?? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?? 'Unknown';
                }
                // Fallback: Query user directly using user_id from lost_damaged_items
                elseif ($item->user_id) {
                    $user = \App\Models\User::find($item->user_id);
                    if ($user) {
                        $borrower_name = $user->name ?? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?? 'Unknown';
                    }
                }
                // Second priority: Teacher (if borrow has role=teacher)
                if ($borrower_name === 'Unknown' && $item->borrow && $item->borrow->role === 'teacher') {
                    $teacher = \App\Models\Teacher::find($item->user_id);
                    if ($teacher && $teacher->name) {
                        $borrower_name = $teacher->name;
                    }
                }
                // Third priority: Direct borrower relationship
                if ($borrower_name === 'Unknown' && $item->borrower && $item->borrower->name) {
                    $borrower_name = $item->borrower->name ?? trim(($item->borrower->first_name ?? '') . ' ' . ($item->borrower->last_name ?? '')) ?? 'Unknown';
                }
                
                return (object) [
                    'type' => $item->type,
                    'action' => $item->status === 'returned' ? 'Returned' : ($item->status === 'replaced' ? 'Replaced' : ucfirst($item->status)),
                    'ctrl_number' => $item->borrow?->copy_number ?? $item->copy_number ?? 'N/A',
                    'book_title' => $item->book ? $item->book->title : 'Unknown',
                    'borrower' => $borrower_name,
                    'borrowed_date' => $item->borrow?->borrowed_at,
                    'remarks' => $item->remarks ?? '—',
                    'created_at' => $item->created_at,
                ];
            });

        return view('books.lost-damage', compact('lostCount', 'damagedCount', 'totalCount', 'records', 'history', 'ctrlNumberSearch', 'bookSearch', 'borrowerSearch', 'borrowedDateSearch', 'filterType'));
    }

    /**
     * Mark a lost/damaged item as returned.
     */
    public function lostDamagedReturn(LostDamagedItem $lostDamagedItem)
    {
        $lostDamagedItem->update(['status' => 'returned']);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Marked as Returned',
            'details' => "Lost/damaged item for book '{$lostDamagedItem->book?->title}' marked as returned.",
        ]);

        return redirect()->route('books.lost-damage')->with('success', 'Item marked as returned.');
    }

    /**
     * Mark a lost/damaged item as replaced.
     */
    public function lostDamagedReplace(LostDamagedItem $lostDamagedItem)
    {
        $lostDamagedItem->update(['status' => 'replaced']);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Marked as Replaced',
            'details' => "Lost/damaged item for book '{$lostDamagedItem->book?->title}' marked as replaced.",
        ]);

        return redirect()->route('books.lost-damage')->with('success', 'Item marked as replaced.');
    }
}

