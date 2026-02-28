**Data Model Overview**
This app uses SQL tables via Eloquent models, with MySQL configured as the default connection. Sources: `app/Models/*`, `config/database.php`.

**Tables and Models**
- `books` (model `Book`): fields include `title`, `author`, `isbn`, `status`, `category`, `copies`, `available_copies`, `publisher`, `edition`, `pages`, `source_of_funds`, `cost_price`, `published_year`, `purchase_price`, `acquisition_type`, `condition`, `copy_status`, `call_number`, `dewey_decimal`, `cutter_number`, `control_numbers`. Soft deletes enabled. Source: `app/Models/Book.php`.
- `borrows` (model `Borrow`): fields include `user_id`, `book_id`, `borrowed_at`, `due_date`, `returned_at`, `remark`, `notes`, `role`. Source: `app/Models/Borrow.php`.
- `users` (model `User`): fields include `name`, `email`, `gender`, `address`, `phone_number`, `role`, `first_name`, `last_name`, `grade_section`, `lrn`, `borrowed`, `remark`. Soft deletes enabled. Source: `app/Models/User.php`.
- `system_users` (model `SystemUser`): fields include `email`, `password`, `role`. Soft deletes enabled. Source: `app/Models/SystemUser.php`.
- `teachers` (model `Teacher`): fields include `name`, `first_name`, `last_name`, `gender`, `address`, `phone_number`, `email`, `remark`. Soft deletes enabled. Source: `app/Models/Teacher.php`.
- `distributed_books` (model `DistributedBook`): fields include `title`, `author`, `publisher`, `isbn`, `category`, `copies`, `available_copies`, `status`, `edition`, `pages`, `source_of_funds`, `cost_price`, `year`, `condition`. Soft deletes enabled. Source: `app/Models/DistributedBook.php`.
- `activity_logs` (model `ActivityLog`): fields include `user_id`, `action`, `target_type`, `target_id`, `details`. Source: `app/Models/ActivityLog.php`.
- `penalty_settings` (model `PenaltySetting`): fields include `borrow_days_allowed`, `penalty_per_day`. Source: `app/Models/PenaltySetting.php`.

**Relationships**
- `Book` has many `Borrow` records. Source: `app/Models/Book.php`.
- `Borrow` belongs to `User` and `Book`. Source: `app/Models/Borrow.php`.
- `User` has many `Borrow` records. Source: `app/Models/User.php`.
- `Teacher` has many `Borrow` records (by `user_id`). Source: `app/Models/Teacher.php`.
- `ActivityLog` belongs to `SystemUser` as the actor. Source: `app/Models/ActivityLog.php`.

**Constraints**
- `activity_logs.user_id` references `system_users.id` (nullable, `nullOnDelete`). Source: `database/migrations/2025_11_29_063414_create_activity_logs_table.php`.
