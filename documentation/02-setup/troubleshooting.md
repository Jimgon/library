**Troubleshooting**
- MySQL connection errors: verify `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` in `.env`. Source: `config/database.php`.
- Missing tables (sessions, cache, queue): ensure migrations have been run, including cache/queue tables. Sources: `database/migrations/*`, `config/cache.php`, `config/queue.php`, `config/session.php`.
- Backup creation fails: backup uses `mysqldump` from PATH. Ensure MySQL client tools are installed and available in PATH. Source: `app/Http/Controllers/UtilitiesController.php`.
- Excel import fails: both books and users imports reject Excel files with a message to use CSV. Sources: `app/Http/Controllers/BookController.php`, `app/Http/Controllers/UserController.php`.
