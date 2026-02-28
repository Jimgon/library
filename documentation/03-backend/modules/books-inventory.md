**Purpose And Scope**
Manage library book inventory, catalog browsing, imports, copy tracking, and printable listings.

**User Stories And Flows**
- Staff can create, edit, and delete books. Source: `app/Http/Controllers/BookController.php`, `resources/views/books/*.blade.php`.
- Staff can import books from CSV. Source: `app/Http/Controllers/BookController.php`, `resources/views/books/import.blade.php`.
- Staff can browse a catalog view of available books. Source: `app/Http/Controllers/BookController.php`, `resources/views/books/catalog.blade.php`.
- Staff can add copies to an existing book, generating control numbers. Source: `app/Http/Controllers/BookController.php`.

**Entry Points**
- `/books/*` resource routes -> `BookController`. Source: `routes/web.php`.
- `/books/import`, `/books/catalog`, `/books/print`, `/books/{book}/add-copies`, `/books/api/next-control-base`. Source: `routes/web.php`.

**Key Classes And Responsibilities**
- `BookController`: CRUD, import, catalog, control number generation, and copy management. Source: `app/Http/Controllers/BookController.php`.
- `Book` model: SQL table `books` with SoftDeletes and derived accessors for borrowed count and available copies. Source: `app/Models/Book.php`.

**Data Model**
- `books` collection stores bibliographic fields and `control_numbers`. Source: `app/Models/Book.php`.

**Validation And Authorization**
- Book create/update enforce title, author, ISBN, category, copies, and other optional metadata. Source: `app/Http/Controllers/BookController.php`.
- CSV import validates file type and row requirements. Source: `app/Http/Controllers/BookController.php`.

**Side Effects**
- Activity logs for book add, update, delete, import, and copy updates. Source: `app/Http/Controllers/BookController.php`, `app/Models/ActivityLog.php`.
- Cache key `ctrl_base` is used to generate control numbers. Source: `app/Http/Controllers/BookController.php`.

**Config And Env Dependencies**
- Cache store used for control number base. Source: `config/cache.php`.

**Error Cases And Edge Cases**
- Duplicate ISBN is rejected during create and import. Source: `app/Http/Controllers/BookController.php`.
- Import returns warnings for missing fields or duplicate ISBNs. Source: `app/Http/Controllers/BookController.php`.

**Where To Start Reading Code**
1. `app/Http/Controllers/BookController.php`
2. `app/Models/Book.php`
3. `resources/views/books/*.blade.php`

**Related Docs**
- documentation/03-backend/routing-map.md`n- documentation/04-frontend/pages-components.md`n- documentation/05-api/api-map.md`n

