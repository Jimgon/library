**Examples**
These endpoints are part of the web middleware group and expect session and CSRF handling. Source: `app/Http/Kernel.php`.

**Login (Form POST)**
```bash
curl -X POST http://localhost/login \
  -d "email=staff@example.com" \
  -d "password=secret" \
  -d "role=staff"
```
Source: `routes/web.php`, `app/Http/Controllers/Auth/LoginController.php`.

**Add Copies (JSON Response)**
```bash
curl -X POST http://localhost/books/{bookId}/add-copies \
  -d "additional_copies=2"
```
Source: `routes/web.php`, `app/Http/Controllers/BookController.php`.

**Get Next Control Base**
```bash
curl http://localhost/books/api/next-control-base
```
Source: `routes/web.php`, `app/Http/Controllers/BookController.php`.
