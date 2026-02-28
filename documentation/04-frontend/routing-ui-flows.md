**UI Flow Maps**

**Login Flow**
```mermaid
sequenceDiagram
  participant U as User
  participant L as /login (GET)
  participant C as LoginController@login
  U->>L: Visit login page
  L-->>U: Render login form
  U->>C: POST /login with email, password, role
  C-->>U: Redirect to /dashboard on success
```
Sources: `routes/web.php`, `app/Http/Controllers/Auth/LoginController.php`, `resources/views/auth/login.blade.php`.

**Borrow Books Flow**
```mermaid
sequenceDiagram
  participant U as Staff
  participant B as BorrowController
  participant M as Borrow model
  participant K as Book model
  U->>B: GET /borrow/create
  B-->>U: Render borrow form
  U->>B: POST /borrow
  B->>M: Create Borrow records
  B->>K: Update book availability and status
  B-->>U: Redirect with success or warning
```
Sources: `routes/web.php`, `app/Http/Controllers/BorrowController.php`.

**Return Books Flow**
```mermaid
sequenceDiagram
  participant U as Staff
  participant B as BorrowController
  participant M as Borrow model
  participant K as Book model
  U->>B: GET /borrow/return
  B-->>U: Render return list
  U->>B: POST /borrow/return/{borrow}
  B->>M: Update borrow remark and returned_at
  B->>K: Update book status and counts
  B-->>U: Redirect with result
```
Sources: `routes/web.php`, `app/Http/Controllers/BorrowController.php`.
