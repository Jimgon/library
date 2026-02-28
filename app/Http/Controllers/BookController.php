<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\DistributedBook;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
        $books = \App\Models\Book::orderBy('title')->get();
        return view('books.print', compact('books'));
    }

    /**
     * Add copies to an existing book.
     */
    public function addCopies(Request $request, $bookId)
    {
        $request->validate([
            'additional_copies' => 'required|integer|min:1|max:1000',
        ]);

        $book = Book::find($bookId);

        if (!$book) {
            return response()->json(['error' => 'Book not found'], 404);
        }

        $additionalCopies = $request->input('additional_copies');
        $currentCopies = $book->copies ?? 0;
        $newTotal = $currentCopies + $additionalCopies;

        // Get current ctrl_base from cache
        $ctrlBase = Cache::get('ctrl_base', 0);

        // Generate new control numbers for the additional copies
        $newControlNumbers = $book->control_numbers ?? [];
        for ($i = 0; $i < $additionalCopies; $i++) {
            $ctrlBase++;
            $copyCopy = count($newControlNumbers) + $i + 1;
            $newControlNumbers[] = str_pad($ctrlBase, 3, '0', STR_PAD_LEFT) . '-' . str_pad($copyCopy, 3, '0', STR_PAD_LEFT);
        }

        // Update book with new copies and control numbers
        $book->copies = $newTotal;
        $book->available_copies = ($book->available_copies ?? 0) + $additionalCopies;
        $book->control_numbers = $newControlNumbers;
        $book->save();

        // Update cache
        Cache::put('ctrl_base', $ctrlBase);

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

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");

                if (method_exists(Book::class, 'currentBorrow')) {
                    $q->orWhereHas('currentBorrow.user', function ($u) use ($search) {
                        $u->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%");
                    });
                }
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $books = $query
            ->with(['borrows' => function($q) {
                $q->whereNull('returned_at')->with('user');
            }])
            ->orderBy('title')
            ->paginate(10)
            ->withQueryString();

        $categories = Book::query()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('books.index', compact('books', 'categories'));
    }

    public function catalog(Request $request)
    {
        $query = Book::where('status', 'available');

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
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        $categories = Book::where('status', 'available')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        // default categories to always show first
        $defaultCategories = ['MATH', 'SCIENCE', 'FILIPINO', 'ENGLISH', 'MAPEH', 'HISTORY'];

        // fetch distinct categories from DB, clean and exclude defaults
        $customCategories = Book::select('category')
            ->distinct()
            ->orderBy('category')
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

        $nextCtrlBase = str_pad(Cache::get('ctrl_base', 0) + 1, 3, '0', STR_PAD_LEFT);

        return view('books.catalog', compact('books', 'categories', 'allCategories', 'nextCtrlBase'));
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
            ->orderBy('title')
            ->paginate(10)
            ->withQueryString();

        return view('books.distribute', compact('books'));
    }

    /**
     * Show form to add a book specifically for distribution.
     */
    public function distributeCreate()
    {
        return view('books.distribute-create');
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

        return redirect()->route('books.distribute')->with('success', 'Book added for distribution.');
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
            return redirect()->route('books.distribute')->with('error', 'Excel import is currently not available. Please use CSV format for now.');
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
            return redirect()->route('books.distribute')->with('warning', 'Distributed books imported with some errors: ' . implode(', ', $errors));
        }

        return redirect()->route('books.distribute')->with('success', 'Distributed books imported successfully.');
    }

    /**
     * Show a distributed book details.
     */
    public function distributeShow($id)
    {
        $book = DistributedBook::with(['borrows' => function($q){ $q->whereNull('returned_at')->with('user'); }])->find($id);
        if (!$book) abort(404);
        return view('books.distribute-show', compact('book'));
    }

    /**
     * Edit form for a distributed book.
     */
    public function distributeEdit($id)
    {
        $book = DistributedBook::find($id);
        if (!$book) abort(404);
        return view('books.distribute-edit', compact('book'));
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

        return redirect()->route('books.distribute')->with('success', 'Distributed book updated.');
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

        return redirect()->route('books.distribute')->with('success', 'Distributed book deleted.');
    }

    public function show(Book $book)
    {
        $book->load('borrows.user');
        return view('books.show', compact('book'));
    }

    public function create()
    {
        // default categories to always show first
        $defaultCategories = ['MATH', 'SCIENCE', 'FILIPINO', 'ENGLISH', 'MAPEH', 'HISTORY'];

        // fetch distinct categories from DB, clean and exclude defaults
        $customCategories = Book::select('category')
            ->distinct()
            ->orderBy('category')
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

        $nextCtrlBase = str_pad(Cache::get('ctrl_base', 0) + 1, 3, '0', STR_PAD_LEFT);
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
            'other_category' => 'required_if:category,other|string|max:255',
            'copies'   => 'required|integer|min:1',
            'published_year' => 'nullable|integer|min:1900|max:'.(date('Y')+1),
            'pages' => 'nullable|integer|min:1',
            'edition' => 'nullable|string|max:255',
            'condition' => 'nullable|string|in:Brand New,Old',
            'acquisition_type' => 'nullable|string|max:255',
            'purchase_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'source_of_funds' => 'nullable|string|max:255',
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
        $categories = Book::select('category')->distinct()->orderBy('category')->pluck('category');
        $nextCtrlBase = str_pad(Cache::get('ctrl_base', 0) + 1, 3, '0', STR_PAD_LEFT);
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
            'call_number' => 'nullable|string|max:50',
            'copies'   => 'required|integer|min:1',
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

        // if copies changed or base blank we may need to regenerate control numbers
        $submitted = $request->input('control_numbers', []);
        if (!(is_array($submitted) && count($submitted) === (int) $request->copies)) {
            $base = trim($request->call_number ?: '');
            if ($base === '') {
                $next = Cache::increment('ctrl_base');
                $base = str_pad($next, 3, '0', STR_PAD_LEFT);
            } else {
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
        } else {
            $controlNumbers = $submitted;
        }

        $book->update([
            'title' => $request->title,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'isbn' => $request->isbn,
            'category' => $categoryValue,
            'call_number' => $request->call_number,
            'copies' => $request->copies,
            'control_numbers' => $controlNumbers,
            'published_year' => $request->published_year,
            'pages' => $request->pages,
            'edition' => $request->edition,
            'condition' => $request->condition,
            'acquisition_type' => $request->acquisition_type,
            'purchase_price' => $request->purchase_price,
            'cost_price' => $request->cost_price,
            'source_of_funds' => $request->source_of_funds,
        ]);

        // control numbers: use submitted if matching count, otherwise regenerate
        $submitted = $request->input('control_numbers', []);
        if (is_array($submitted) && count($submitted) === (int) $request->copies) {
            $controlNumbers = $submitted;
        } else {
            $base = trim($request->call_number ?: '');
            if ($base === '') {
                $base = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
            }
            $controlNumbers = [];
            for ($i = 1; $i <= $request->copies; $i++) {
                $controlNumbers[] = $base . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            }
        }
        $book->control_numbers = $controlNumbers;
        if ((int) $request->copies > $oldCopies) {
            $book->available_copies = ($book->available_copies ?? 0) + ((int) $request->copies - (int) $oldCopies);
        } elseif ((int) $request->copies < $oldCopies) {
            $book->available_copies = min(($book->available_copies ?? 0), (int) $request->copies);
        }
        $book->save();
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Updated Book',
            'details' => "Book '{$book->title}' updated.",
        ]);

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
}


