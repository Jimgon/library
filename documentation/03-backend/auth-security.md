**Auth Overview**
- Default guard is `web` with session driver; provider is `system_users` which uses the `SystemUser` model. Source: `config/auth.php`.
- Login is handled by `Auth\LoginController` which validates email, password, and role (`admin` or `staff`) and logs in via `Auth::login`. Source: `app/Http/Controllers/Auth/LoginController.php`.

**Roles And Authorization**
- Roles are stored on `SystemUser.role` and checked in views for admin-only UI. Source: `app/Models/SystemUser.php`, `resources/views/layouts/app.blade.php`.
- Middleware for role checks exists (`CheckRole`, `RoleMiddleware`) and is registered, but there are no route definitions using `role` middleware in `routes/web.php`. Sources: `app/Http/Middleware/CheckRole.php`, `app/Http/Middleware/RoleMiddleware.php`, `app/Http/Kernel.php`, `routes/web.php`.

**Sensitive Operations**
- Staff management routes exist under `/staff` and are not explicitly protected by a role middleware in routes. Source: `routes/web.php`.
- Utilities allow archive restore and permanent delete actions. Source: `app/Http/Controllers/UtilitiesController.php`, `routes/web.php`.

**CSRF**
- The `web` middleware group includes CSRF verification. Source: `app/Http/Kernel.php`.
