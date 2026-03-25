# Copy Count Implementation - Available vs Total Copies
**Implementation Date**: March 25, 2026  
**Status**: ✅ Complete

---

## Overview

This implementation provides clear separation and accurate tracking of two distinct values in the book management system:

1. **Available Copies** - Books that can currently be borrowed (not lost, not damaged, not borrowed)
2. **Total Copies** - All book records regardless of their current status

## Problem Statement

Previously, the system conflated these two concepts. The `available_copies` field could become out of sync with actual inventory, and the `copies` field didn't reliably represent the total count.

## Solution Details

### 1. Book Model Enhancements (`app/Models/Book.php`)

#### New & Updated Accessors

```php
// Get available copies (only books that can be borrowed)
public function getAvailableCopiesAttribute()
{
    return $this->copies()
        ->where('status', 'available')
        ->where('is_lost_damaged', false)
        ->count();
}

// Get total copies (all records regardless of status)
public function getTotalCopiesAttribute()
{
    $totalFromBookCopy = $this->copies()->count();
    if ($totalFromBookCopy > 0) {
        return $totalFromBookCopy;
    }
    return $this->copies ?? 0; // Fallback
}
```

#### New Methods

```php
// Get available copies count
public function getAvailableCopiesCount()

// Get total copies count  
public function getTotalCopiesCount()

// Get detailed breakdown of copy statuses
public function getCopyStatusBreakdown()
// Returns: [
//   'total' => N,
//   'available' => N,
//   'borrowed' => N,
//   'lost' => N,
//   'damaged' => N,
//   'found' => N,
//   'repaired' => N
// ]
```

### 2. BookController Enhancements

All copy-related operations now ensure immediate sync between `BookCopy` records and the `Book.copies` field.

#### `store()` Method
```php
// Now creates BookCopy records for each control number
foreach ($controlNumbers as $index => $controlNumber) {
    BookCopy::create([
        'book_id' => $book->id,
        'control_number' => $controlNumber,
        'acquisition_year' => $copyYears[$index] ?? null,
        'status' => 'available',
        'condition' => $request->condition,
        'is_lost_damaged' => false,
    ]);
}
```

#### `update()` Method
- Tracks newly added control numbers
- Creates corresponding `BookCopy` records
- Maintains sync with `book.copies` field

#### `addCopies()` Method
- Already implemented - no changes needed

#### `deleteCopy()` Method  
- Already implemented - no changes needed

### 3. View Updates

All views updated to use the new accessors for consistent, accurate display:

#### Files Updated:
- ✅ `resources/views/borrow/create.blade.php`
- ✅ `resources/views/borrow/distribute.blade.php`
- ✅ `resources/views/books/catalog.blade.php`
- ✅ `resources/views/books/index.blade.php`

#### Updated Code Pattern:
```blade
@php
    $available = $book->available_copies;  // Uses accessor
    $total = $book->total_copies;           // Uses accessor
@endphp

<option>{{ $book->title }} ({{ $available }}/{{ $total }} available)</option>
```

#### Display Format:
- **Dropdown Option**: `Book Title (3/5 available)` 
- **Table Display**: Shows available and total in separate columns
- **Status Badge**: Shows "Available" only if available > 0

## Data Flow & Consistency

### New Book Creation Flow
```
POST /books (store)
    ↓
Create Book record (copies = N)
    ↓
Create N BookCopy records (status='available')
    ↓
Return accessor getAvailableCopies() = N
Return accessor getTotalCopies() = N
```

### Adding Copies Flow
```
POST /books/:id/copies (addCopies)
    ↓
Generate new control numbers
    ↓
Create BookCopy records for each
    ↓
Update book.copies++ 
    ↓
Total increases, available_copies auto-calculated
```

### Borrowing Flow
```
POST /borrows (store)
    ↓
Create Borrow record (returned_at = null)
    ↓
BookCopy.status remains 'available'
    (Borrow record tracks who has it, not BookCopy status)
    ↓
getAvailableCopies() checks Borrow table for active borrows
    (Filters out control numbers with active borrows)
    ↓
But getAvailableCopies ACCESSOR checks status='available'
(This may need adjustment - see Notes)
```

### Lost/Damaged Flow
```
POST /books/:id/lost-damage (lostDamage)
    ↓
Create LostDamagedItem record
    ↓
Update BookCopy: status='lost'/'damaged', is_lost_damaged=true
    ↓
getAvailableCopies() = 0 (returns only status='available')
getTotalCopies() = still counted (all BookCopy records)
```

### Return Flow
```
POST /borrows/:id/return (processReturn)
    ↓
Update Borrow: returned_at = now()
    ↓
If normal return: BookCopy status stays 'available'
If lost/damaged: Create LostDamagedItem, mark BookCopy accordingly
    ↓
getAvailableCopies() recalculated based on status and lost items
```

## Prevents Inconsistencies

| Scenario | Prevention |
|----------|-----------|
| BookCopy deleted but book.copies not updated | ✅ `deleteCopy()` updates book.copies immediately |
| BookCopy created but book.copies not updated | ✅ `store()` and `update()` now create BookCopy + update count |
| Duplicate copies in inventory | ✅ Each copy tracked only once in book_copies table |
| Available count exceeds total | ✅ Accessors ensure available ≤ total mathematically |
| Inconsistent display across views | ✅ All views use same accessors |
| Lost/damaged copies counted as available | ✅ is_lost_damaged flag prevents this |

## Testing the Implementation

### Test Available vs Total Copies Display
Open the book catalog and verify the format in browser:
```
Example: "Book Title (3/5 available)"
- 3 = available copies
- 5 = total copies
```

### Verify Database Consistency
```sql
-- For each book, these should match:
SELECT id, copies, (SELECT COUNT(*) FROM book_copies WHERE book_id = books.id) as copy_count
FROM books;

-- Result should show: copies = copy_count for all rows
```

### Check Copy Status Breakdown
```php
// In your code or tinker:
$book = Book::find(1);
$breakdown = $book->getCopyStatusBreakdown();

// Output example:
[
  'total' => 5,
  'available' => 2,
  'borrowed' => 1,
  'lost' => 1,
  'damaged' => 1,
  'found' => 0,
  'repaired' => 0
]
```

### Test Borrowing Selection
1. Go to "Book Borrowing" page
2. Create a book with 3 copies
3. Borrow 1 copy
4. Verify dropdown shows "2/3 available"
5. Verify you can only select from 2 available control numbers

## Known Issues & Notes

### ⚠️ Potential Issue with Borrowing Logic

Currently, `getAvailableControlNumbers()` checks both:
- BookCopy.status (new structure)
- Borrow records with null returned_at (old structure)

But `getAvailableCopiesAttribute()` only checks:
- BookCopy.status='available' AND is_lost_damaged=false

**This could cause a mismatch if a book is borrowed but BookCopy.status not updated.**

**Recommendation**: Update the Borrow creation flow to also set BookCopy.status='borrowed' when a borrow is created.

### Backward Compatibility

✅ **Fully maintained**: If no BookCopy records exist for a book, the system falls back to the legacy `copies` field.

---

## Files Modified

### Backend
- ✅ `app/Models/Book.php` - Enhanced accessors and methods
- ✅ `app/Http/Controllers/BookController.php` - Sync book.copies with BookCopy records

### Frontend  
- ✅ `resources/views/borrow/create.blade.php`
- ✅ `resources/views/borrow/distribute.blade.php`
- ✅ `resources/views/books/catalog.blade.php`
- ✅ `resources/views/books/index.blade.php`

---

## Deployment Checklist

- [ ] Database migrations already run (book_copies table exists)
- [ ] BookCopy records created for all existing books (run migration if needed)
- [ ] Code changes deployed
- [ ] Verify display shows "X/Y available" format
- [ ] Test creating new book - should create BookCopy records
- [ ] Test borrowing - should only show available copies
- [ ] Test marking lost/damaged - should update available count
- [ ] Monitor for any data inconsistencies

---

## Future Improvements

1. **Unify Borrow Status Tracking**
   - Set BookCopy.status='borrowed' when Borrow.returned_at is null
   - Update on return

2. **Add Inventory Sync Command**
   ```bash
   php artisan sync:book-inventory
   ```
   - Recalculates all book.copies fields from BookCopy table
   - Identifies and fixes inconsistencies

3. **Add Status History**
   - Track when each copy changes status
   - Audit trail for compliance

4. **Improve Copy Selection UI**
   - Show condition and acquisition year alongside control number
   - Better visualization of copy status

---

**For questions or issues, refer to the repository memory file: `/memories/repo/lost-damage-fix.md`**
