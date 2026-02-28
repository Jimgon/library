**Deployment Notes**
- Build frontend assets with `npm run build`. Source: `package.json`.
- Vite entrypoints are `resources/css/app.css` and `resources/js/app.js`. Source: `vite.config.js`.
- Ensure `APP_KEY`, `APP_URL`, and MySQL connection values are set. Sources: `config/app.php`, `config/database.php`.
- If using the queue worker, run `php artisan queue:listen --tries=1` or a supervisor-managed queue worker. Source: `composer.json`, `config/queue.php`.
- If you need public storage, run `php artisan storage:link`. Source: `config/filesystems.php`.
- Backups require `mysqldump` to be available on PATH. Source: `app/Http/Controllers/UtilitiesController.php`.
