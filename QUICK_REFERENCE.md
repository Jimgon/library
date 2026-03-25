# Available vs Total Copies - Quick Reference

## Display Changes

### Before
```
Book Title (3 available)  ← Ambiguous - is 3 total or available?
```

### After
```
Book Title (2/5 available)  ← Clear: 2 available out of 5 total
```

---

## Usage Examples

### In Views (Blade)
```blade
{{-- Get the two values from the book model accessors --}}
@php
    $available = $book->available_copies;  // Returns: 2
    $total = $book->total_copies;          // Returns: 5
@endphp

{{-- Display combined --}}
{{ $book->title }} ({{ $available }}/{{ $total }} available)
```

### In Controllers (PHP)
```php
$book = Book::find(1);

// Get clean counts
$available = $book->available_copies;           // 2
$total = $book->total_copies;                   // 5
$borrowed = $book->getBorrowedCopies()->count(); // 1
$lost = $book->getLostOrDamagedCopies()->count(); // 2

// Or get full breakdown
$breakdown = $book->getCopyStatusBreakdown();
// [
//     'total' => 5,
//     'available' => 2,
//     'borrowed' => 1,
//     'lost' => 1,
//     'damaged' => 1,
// ]
```

---

## Status Breakdown

When you have 5 copies of a book:

| Status | Copies | Counted in Total? | Counted in Available? |
|--------|--------|-------------------|-----------------------|
| Available | 2 | ✅ Yes | ✅ Yes |
| Borrowed | 1 | ✅ Yes | ❌ No |
| Lost | 1 | ✅ Yes | ❌ No |
| Damaged | 1 | ✅ Yes | ❌ No |
| Found/Repaired | 0 | ✅ Yes (if any) | ✅ Yes (if repaired) |

**Total: 5 copies**  
**Available: 2 copies**  
**Display: 2/5 available** ✓

---

## Database Mapping

### BookCopy Records (Source of Truth)
```
book_id | control_number | status    | is_lost_damaged | can_borrow?
--------|----------------|-----------|-----------------|------------
1       | 001-001        | available | false           | ✅ YES
1       | 001-002        | borrowed  | false           | ❌ Currently out
1       | 001-003        | lost      | true            | ❌ NEVER
1       | 001-004        | damaged   | true            | ❌ NEVER
1       | 001-005        | available | false           | ✅ YES
```

### Books Record (Summary)
```
id | title | copies | available_copies | total_copies
---|-------|--------|------------------|-------------
1  | ...   | 5      | 2 (calculated)   | 5 (calculated)
```

---

## Operations & Automatic Updates

### Create Book (3 copies)
```
POST /books
Input: title, isbn, copies=3, control_numbers=['001-001', '001-002', '001-003']

Result:
- books.copies = 3
- books.available_copies = 3 (calculated)
- books.total_copies = 3 (calculated)
- 3 BookCopy records created (all status='available')
```

### Borrow 1 Copy
```
POST /borrows
Input: book_id=1, copy_number='001-001', user_id=5

Result:
- books.available_copies = 2 (recalculated)
- books.total_copies = 3 (unchanged)
- Borrow record created
```

### Add 2 More Copies
```
POST /books/1/copies
Input: additional_copies=2

Result:
- books.copies = 5
- books.available_copies = 4 (recalculated)
- books.total_copies = 5 (recalculated)
- 2 new BookCopy records created
```

### Mark Copy as Lost
```
POST /books/1/lost-damage
Input: control_number='001-002'

Result:
- books.available_copies = 3 (recalculated)
- books.total_copies = 5 (unchanged)
- BookCopy.status = 'lost'
- BookCopy.is_lost_damaged = true
- LostDamagedItem record created
```

### Delete Copy (Already Archived)
```
POST /books/1/copies/delete
Input: control_number='001-001'

Result:
- books.copies = 4
- books.available_copies = 2 (recalculated)
- books.total_copies = 4 (recalculated)
- BookCopy record deleted
- BookArchive record created
```

---

## Querying Examples

### Get All Available Books (with > 0 available)
```php
Book::with('copies')
    ->whereHas('copies', function($q) {
        $q->where('status', 'available')
          ->where('is_lost_damaged', false);
    }, '>', 0)
    ->get();
```

### List All Lost Copies System-Wide
```php
BookCopy::where('status', 'lost')
    ->where('is_lost_damaged', true)
    ->with('book')
    ->get();
```

### Find Books with Inventory Issues
```php
// Get books where copies field doesn't match BookCopy count
Book::all()->filter(function($book) {
    return $book->copies != $book->copies()->count();
});
```

---

## Migration Path (If Needed)

For any existing books without BookCopy records:

```php
// In a migration or command:
$books = Book::all();
foreach ($books as $book) {
    $controlNumbers = $book->control_numbers ?? [];
    foreach ($controlNumbers as $index => $ctrl) {
        BookCopy::firstOrCreate(
            ['book_id' => $book->id, 'control_number' => $ctrl],
            [
                'acquisition_year' => $book->copy_years[$index] ?? null,
                'status' => 'available',
                'condition' => $book->copy_conditions[$index] ?? null,
                'is_lost_damaged' => in_array($ctrl, $book->lost_control_numbers ?? []),
            ]
        );
    }
}
```

---

## Key Benefits

✅ **Clear Semantics**: Developers and users alike understand what "available" vs "total" means  
✅ **No Duplication**: Each copy tracked once in the authoritative BookCopy table  
✅ **Computed Safely**: Accessors ensure calculations are always correct, never stale  
✅ **Consistent Display**: All views use same logic, no divergence  
✅ **Prevents Overbooking**: System can only show as available what truly is available  
✅ **Audit Trail**: Status changes tracked in BookCopy records  
✅ **Backward Compatible**: Falls back gracefully if BookCopy records missing
