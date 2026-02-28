**Migration Notes**
- Migrations define the full SQL schema used by the app. Sources: `database/migrations/*`, `config/database.php`.
- Tables include users, system_users, books, borrows, teachers, distributed_books, activity_logs, penalty_settings, cache, and queue tables. Sources: `database/migrations/*`.
- Activity logs link back to system users via a nullable foreign key. Source: `database/migrations/2025_11_29_063414_create_activity_logs_table.php`.

**Action Items**
- Run `php artisan migrate` after configuring MySQL in `.env`. Source: `config/database.php`.
