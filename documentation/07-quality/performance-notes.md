**Performance Notes**
- **N+1 in popular books calculation**: `DashboardController` groups borrows by book_id and then calls `Book::find` in a loop. This causes one query per book. Source: `app/Http/Controllers/DashboardController.php`.
- **Full collection scan for control number collision check**: `BookController@store` loads all books to check `control_numbers` for duplicates. This will grow linearly with the number of books. Source: `app/Http/Controllers/BookController.php`.
- **Unpaginated return list**: `BorrowController@returnIndex` loads all active borrows without pagination, which can grow large. Source: `app/Http/Controllers/BorrowController.php`.
