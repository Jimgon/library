**Code Health Observations**
- Inertia + React scaffold exists (`resources/js/app.jsx`, `@inertiajs/react` in `package.json`), but the app appears to render Blade views directly. **Hypothesis:** Inertia is currently unused. Sources: `resources/js/app.jsx`, `package.json`, `resources/views/`.
- `routes/utilities-backup.php` defines routes that are not loaded by `bootstrap/app.php` (only `routes/web.php` and `routes/console.php` are registered). Source: `bootstrap/app.php`, `routes/utilities-backup.php`.
- `app/Imports/BooksImport.php` is empty and unused. Source: `app/Imports/BooksImport.php`.
