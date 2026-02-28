**Purpose And Scope**
Handle borrowing and returning of books for students and teachers, including receipts and remarks.

**User Stories And Flows**
- Staff can borrow books for students with a limit of 3 active borrows. Source: `app/Http/Controllers/BorrowController.php`.
- Staff can borrow books for teachers from the distribution workflow. Source: `app/Http/Controllers/BorrowController.php`.
- Staff can process returns and apply remarks and notes. Source: `app/Http/Controllers/BorrowController.php`.
- Receipts can be printed for a borrow record. Source: `app/Http/Controllers/BorrowController.php`, `resources/views/borrow/receipt.blade.php`.

**Entry Points**
- `/borrow/*` routes -> `BorrowController` and `TeacherBorrowController`. Source: `routes/web.php`.

**Key Classes And Responsibilities**
- `BorrowController`: student borrow, teacher distribution borrow, returns, receipts. Source: `app/Http/Controllers/BorrowController.php`.
- `TeacherBorrowController`: teacher borrow flow and returns. Source: `app/Http/Controllers/TeacherBorrowController.php`.
- `Borrow` model: stores borrow records. Source: `app/Models/Borrow.php`.

**Data Model**
- `borrows` collection with user and book references and return metadata. Source: `app/Models/Borrow.php`.

**Validation And Authorization**
- Borrow validation includes user, dates, and max book count. Source: `app/Http/Controllers/BorrowController.php`.
- Return validation restricts remark values and notes length. Source: `app/Http/Controllers/BorrowController.php`, `app/Http/Controllers/TeacherBorrowController.php`.

**Side Effects**
- Updates book availability and status on borrow and return. Source: `app/Http/Controllers/BorrowController.php`, `app/Http/Controllers/TeacherBorrowController.php`.
- Writes activity logs for borrow and return events. Source: `app/Http/Controllers/BorrowController.php`, `app/Models/ActivityLog.php`.
- Updates borrower remark field when a return is late or has notes. Source: `app/Http/Controllers/BorrowController.php`.

**Config And Env Dependencies**
- Penalty settings are read via the `penalty_settings` table. Source: `app/Http/Controllers/BorrowController.php`.

**Error Cases And Edge Cases**
- Borrow fails when a book is unavailable or when a borrower exceeds the limit. Source: `app/Http/Controllers/BorrowController.php`.
- Return processing supports multiple borrow IDs with quantity limits. Source: `app/Http/Controllers/BorrowController.php`.

**Where To Start Reading Code**
1. `app/Http/Controllers/BorrowController.php`
2. `app/Http/Controllers/TeacherBorrowController.php`
3. `app/Models/Borrow.php`
4. `resources/views/borrow/*.blade.php`

**Related Docs**
- documentation/03-backend/routing-map.md`n- documentation/04-frontend/pages-components.md`n- documentation/05-api/api-map.md`n
