**Purpose And Scope**
Provide utilities such as activity log viewing, archive/restore of deleted records, and database backups.

**User Stories And Flows**
- Staff can view activity logs. Source: `app/Http/Controllers/UtilitiesController.php`, `resources/views/utilities/activity-log.blade.php`.
- Staff can view archived (soft-deleted) records and restore or permanently delete them. Source: `app/Http/Controllers/UtilitiesController.php`, `resources/views/utilities/archive*.blade.php`.
- Staff can create database backups and download them. Source: `app/Http/Controllers/UtilitiesController.php`, `resources/views/utilities/backups.blade.php`.

**Entry Points**
- `/utilities/*` routes -> `UtilitiesController`. Source: `routes/web.php`.

**Key Classes And Responsibilities**
- `UtilitiesController`: logs, archive, restore, permanent delete, backups. Source: `app/Http/Controllers/UtilitiesController.php`.
- `ActivityLog` model: stores user actions. Source: `app/Models/ActivityLog.php`.

**Data Model**
- Uses soft deletes on `Book`, `User`, `Teacher`, and `SystemUser` for archive/restore. Sources: `app/Models/Book.php`, `app/Models/User.php`, `app/Models/Teacher.php`, `app/Models/SystemUser.php`.

**Validation And Authorization**
- Archive actions require a valid model key (`book`, `student`, `teacher`, `staff`). Source: `app/Http/Controllers/UtilitiesController.php`.

**Side Effects**
- Activity logs are written for restore and delete actions. Source: `app/Http/Controllers/UtilitiesController.php`, `app/Models/ActivityLog.php`.
- Backups execute `mongodump.exe` and create zip files under `storage/app/backups`. Source: `app/Http/Controllers/UtilitiesController.php`.

**Config And Env Dependencies**
- MySQL connection details are required for backup. Source: `config/database.php`, `app/Http/Controllers/UtilitiesController.php`.

**Error Cases And Edge Cases**
- Backup download returns 404 if a file is missing. Source: `app/Http/Controllers/UtilitiesController.php`.

**Where To Start Reading Code**
1. `app/Http/Controllers/UtilitiesController.php`
2. `resources/views/utilities/*.blade.php`

**Related Docs**
- documentation/03-backend/routing-map.md`n- documentation/04-frontend/pages-components.md`n- documentation/05-api/api-map.md`n

