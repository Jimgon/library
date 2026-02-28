**Product Summary**
This project is a web-based library management system for staff and admins to manage book inventory, student and teacher profiles, borrowing and returns, reporting, and utilities such as activity logs, archive/restore, and backups. Sources: `routes/web.php`, controllers `app/Http/Controllers/BookController.php`, `app/Http/Controllers/UserController.php`, `app/Http/Controllers/TeacherController.php`, `app/Http/Controllers/BorrowController.php`, `app/Http/Controllers/DashboardController.php`, `app/Http/Controllers/UtilitiesController.php`.

**Primary User Roles**
- Admin and staff accounts authenticate via `SystemUser` with role values `admin` or `staff`. Sources: `app/Models/SystemUser.php`, `app/Http/Controllers/Auth/LoginController.php`, `config/auth.php`.
- Students are stored in the `User` model and managed through the students UI. Sources: `app/Models/User.php`, `app/Http/Controllers/UserController.php`, `resources/views/users/*.blade.php`.
- Teachers are stored in a dedicated `Teacher` model and managed separately. Sources: `app/Models/Teacher.php`, `app/Http/Controllers/TeacherController.php`, `resources/views/users/teachers.blade.php`.

**Core Capabilities**
- Book inventory management with import, catalog listing, copy tracking, and printable lists. Sources: `routes/web.php`, `app/Http/Controllers/BookController.php`, `resources/views/books/*.blade.php`.
- Borrow and return workflows for students and teachers, including receipts and remarks. Sources: `routes/web.php`, `app/Http/Controllers/BorrowController.php`, `app/Http/Controllers/TeacherBorrowController.php`, `resources/views/borrow/*.blade.php`.
- Staff user management for admin-only accounts. Sources: `routes/web.php`, `app/Http/Controllers/UserManagementController.php`, `resources/views/staff/*.blade.php`.
- Reports and dashboard metrics. Sources: `app/Http/Controllers/DashboardController.php`, `resources/views/dashboard.blade.php`, `resources/views/reports.blade.php`.
- Utilities such as activity logs, archive/restore, and backups. Sources: `routes/web.php`, `app/Http/Controllers/UtilitiesController.php`, `resources/views/utilities/*.blade.php`.

**Where To Go Next**
- Backend entry points are summarized in `documentation/03-backend/routing-map.md`.
- Feature modules and code pointers are in `documentation/03-backend/modules/`.
- Known risks are tracked in `documentation/07-quality/known-issues.md`.
