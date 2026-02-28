**Purpose And Scope**
Manage student profiles and related import and bulk actions.

**User Stories And Flows**
- Staff can create, edit, view, and delete students. Source: `app/Http/Controllers/UserController.php`, `resources/views/users/*.blade.php`.
- Staff can import students from CSV. Source: `app/Http/Controllers/UserController.php`, `resources/views/users/teachers_import.blade.php`.
- Staff can update student remarks and bulk delete students. Source: `app/Http/Controllers/UserController.php`.

**Entry Points**
- `/users/*` resource routes -> `UserController`. Source: `routes/web.php`.
- `/users/import`, `/users/bulk-delete`, `/users/{user}/remark`. Source: `routes/web.php`.

**Key Classes And Responsibilities**
- `UserController`: student CRUD, import, remark updates, bulk delete. Source: `app/Http/Controllers/UserController.php`.
- `User` model: student records and borrows relationship. Source: `app/Models/User.php`.

**Data Model**
- `users` collection stores student and teacher records when `role` is set, but students are typically role-null. Source: `app/Models/User.php`, `app/Http/Controllers/UserController.php`.

**Validation And Authorization**
- Student validation includes name, grade/section, LRN uniqueness, and optional contact fields. Source: `app/Http/Controllers/UserController.php`.
- CSV import expects name, grade/strand/section, and optional LRN/contact data. Source: `app/Http/Controllers/UserController.php`.

**Side Effects**
- Activity logs are created for create, update, delete, import, and remark updates. Source: `app/Http/Controllers/UserController.php`, `app/Models/ActivityLog.php`.

**Config And Env Dependencies**
- None beyond database connection. Source: `config/database.php`.

**Error Cases And Edge Cases**
- CSV import skips rows with missing name or duplicate LRN. Source: `app/Http/Controllers/UserController.php`.
- Bulk delete requires at least one selected user. Source: `app/Http/Controllers/UserController.php`.

**Where To Start Reading Code**
1. `app/Http/Controllers/UserController.php`
2. `app/Models/User.php`
3. `resources/views/users/*.blade.php`

**Related Docs**
- documentation/03-backend/routing-map.md`n- documentation/04-frontend/pages-components.md`n- documentation/05-api/api-map.md`n
