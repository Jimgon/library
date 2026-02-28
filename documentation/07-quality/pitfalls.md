**Likely Pitfalls**
- **Session/cache/queue tables missing**: defaults are `database` drivers, which require `sessions`, `cache`, and `jobs` tables. Ensure migrations ran. Sources: `config/cache.php`, `config/session.php`, `config/queue.php`, `database/migrations/*`.
- **Borrow and return operations are not wrapped in transactions**: multiple writes occur (borrow record, book update, user remark update) without `DB::transaction`, which may cause partial updates on failure. Sources: `app/Http/Controllers/BorrowController.php`, `app/Http/Controllers/TeacherBorrowController.php`.
- **Logout via GET**: `/logout` is a GET route, which can be vulnerable to CSRF. Source: `routes/web.php`.
- **Role-based access control not enforced on staff routes**: `/staff/*` routes are not protected by a role middleware. Source: `routes/web.php`, `app/Http/Kernel.php`.
