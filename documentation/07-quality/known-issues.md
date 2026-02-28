**Confirmed Issues**
1. Auth middleware is applied to all web routes, including `/login`.
   - `Authenticate` is part of the `web` middleware group, and login routes are defined in `routes/web.php` without a `guest` override. This can cause login access issues. Sources: `app/Http/Kernel.php`, `routes/web.php`.
