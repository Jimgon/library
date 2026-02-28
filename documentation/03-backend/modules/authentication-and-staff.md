**Purpose And Scope**
Handle authentication for staff/admin accounts and manage staff/admin users.

**User Stories And Flows**
- Staff/admin logs in using email, password, and role. Source: `app/Http/Controllers/Auth/LoginController.php`, `resources/views/auth/login.blade.php`.
- Admin manages staff accounts (list, create, edit, delete). Source: `app/Http/Controllers/UserManagementController.php`, `resources/views/staff/*.blade.php`, `routes/web.php`.

**Entry Points**
- `/login` (GET, POST) -> `Auth\LoginController`. Source: `routes/web.php`.
- `/logout` (GET) -> `Auth\LoginController@logout`. Source: `routes/web.php`.
- `/staff/*` -> `UserManagementController`. Source: `routes/web.php`.

**Key Classes And Responsibilities**
- `Auth\LoginController`: login form, validation, session login/logout. Source: `app/Http/Controllers/Auth/LoginController.php`.
- `UserManagementController`: CRUD for `SystemUser` staff/admin. Source: `app/Http/Controllers/UserManagementController.php`.
- `SystemUser` model: authentication entity with `email`, `password`, `role`. Source: `app/Models/SystemUser.php`.

**Data Model**
- Collection: `system_users` with `email`, `password`, `role`, `deleted_at`. Source: `app/Models/SystemUser.php`.

**Validation And Authorization**
- Login validates `email`, `password`, and `role` (`admin` or `staff`). Source: `app/Http/Controllers/Auth/LoginController.php`.
- Staff management validates unique email and role, and handles password changes with old password checks. Source: `app/Http/Controllers/UserManagementController.php`.
- No explicit role middleware is applied to `/staff` routes in `routes/web.php`. Source: `routes/web.php`.

**Side Effects**
- Activity logs are created for staff create/update/delete actions. Source: `app/Http/Controllers/UserManagementController.php`, `app/Models/ActivityLog.php`.

**Config And Env Dependencies**
- Auth provider uses `system_users` and session driver. Source: `config/auth.php`.

**Error Cases And Edge Cases**
- Invalid credentials return back with errors. Source: `app/Http/Controllers/Auth/LoginController.php`.
- Staff update requires correct old password when changing passwords. Source: `app/Http/Controllers/UserManagementController.php`.

**Where To Start Reading Code**
1. `app/Http/Controllers/Auth/LoginController.php`
2. `app/Http/Controllers/UserManagementController.php`
3. `app/Models/SystemUser.php`

**Related Docs**
- documentation/03-backend/routing-map.md`n- documentation/04-frontend/pages-components.md`n- documentation/05-api/api-map.md`n
