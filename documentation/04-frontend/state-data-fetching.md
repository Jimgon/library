**State Management**
- The UI is largely server-rendered via Blade; state is managed by server responses and standard form submissions. Sources: `resources/views/*.blade.php`, `routes/web.php`.

**Client-Side Data Fetching**
- Global Axios setup exists in `resources/js/bootstrap.js` but no explicit frontend API clients were found in `resources/js/`. Source: `resources/js/bootstrap.js`, `resources/js/`.
- Some endpoints return JSON for AJAX use, such as book copy updates and control number base. Sources: `app/Http/Controllers/BookController.php`.

**Inertia/React**
- Inertia app bootstrap exists in `resources/js/app.jsx`. **Hypothesis:** It is not currently used because no Inertia root view is defined in `resources/views/`. Sources: `resources/js/app.jsx`, `resources/views/`.
