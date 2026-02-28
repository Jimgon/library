**Purpose And Scope**
Manage teacher profiles separate from students.

**User Stories And Flows**
- Staff can list, create, edit, and delete teachers. Source: `app/Http/Controllers/TeacherController.php`, `resources/views/users/teachers.blade.php`.
- Staff can import teachers from CSV. Source: `app/Http/Controllers/TeacherController.php`, `resources/views/users/teachers_import.blade.php`.

**Entry Points**
- `/teachers/*` routes -> `TeacherController`. Source: `routes/web.php`.

**Key Classes And Responsibilities**
- `TeacherController`: teacher CRUD and CSV import. Source: `app/Http/Controllers/TeacherController.php`.
- `Teacher` model: SQL table `teachers`. Source: `app/Models/Teacher.php`.

**Data Model**
- `teachers` collection stores name, contact info, email, and remark fields. Source: `app/Models/Teacher.php`.

**Validation And Authorization**
- Email uniqueness is enforced during create and update. Source: `app/Http/Controllers/TeacherController.php`.

**Side Effects**
- Activity logs are created for create, update, delete, and import actions. Source: `app/Http/Controllers/TeacherController.php`, `app/Models/ActivityLog.php`.

**Config And Env Dependencies**
- None beyond database connection. Source: `config/database.php`.

**Error Cases And Edge Cases**
- Import rows with missing name or email are skipped with errors in session flash. Source: `app/Http/Controllers/TeacherController.php`.

**Where To Start Reading Code**
1. `app/Http/Controllers/TeacherController.php`
2. `app/Models/Teacher.php`
3. `resources/views/users/teachers*.blade.php`

**Related Docs**
- documentation/03-backend/routing-map.md`n- documentation/04-frontend/pages-components.md`n- documentation/05-api/api-map.md`n

