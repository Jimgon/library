**Purpose And Scope**
Provide dashboard summaries and report metrics for system usage.

**User Stories And Flows**
- Staff can view a dashboard with totals, due soon borrows, available books, and popular book counts. Source: `app/Http/Controllers/DashboardController.php`, `resources/views/dashboard.blade.php`.
- Staff can view reports with totals, popular books, categories, and monthly activity. Source: `app/Http/Controllers/DashboardController.php`, `resources/views/reports.blade.php`.

**Entry Points**
- `/dashboard` -> `DashboardController@index`. Source: `routes/web.php`.
- `/reports` -> `DashboardController@reports`. Source: `routes/web.php`.

**Key Classes And Responsibilities**
- `DashboardController`: aggregates counts and lists for views. Source: `app/Http/Controllers/DashboardController.php`.

**Data Model**
- Uses `Book`, `User`, `Borrow`, and `Teacher` models for counts and charts. Source: `app/Http/Controllers/DashboardController.php`.

**Validation And Authorization**
- No additional validation beyond auth middleware. Source: `routes/web.php`.

**Side Effects**
- None. The controller reads data and returns views. Source: `app/Http/Controllers/DashboardController.php`.

**Where To Start Reading Code**
1. `app/Http/Controllers/DashboardController.php`
2. `resources/views/dashboard.blade.php`
3. `resources/views/reports.blade.php`

**Related Docs**
- documentation/03-backend/routing-map.md`n- documentation/04-frontend/pages-components.md`n- documentation/05-api/api-map.md`n
