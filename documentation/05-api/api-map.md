**API Map**
There is no `routes/api.php` in this repository, and only `routes/web.php` is registered by the framework. Source: `bootstrap/app.php`.

**AJAX-Style JSON Endpoints**
- `POST /books/{book}/add-copies` -> returns JSON success or error. Source: `app/Http/Controllers/BookController.php`, `routes/web.php`.
- `GET /books/api/next-control-base` -> returns JSON `{ nextBase }`. Source: `app/Http/Controllers/BookController.php`, `routes/web.php`.

**Notes**
- All endpoints are in the web middleware group and require session auth due to the `auth` middleware group wrapping them. Source: `routes/web.php`.
