**Runtime Stack**
- Backend: Laravel `^12.0` running on PHP `^8.2`. Source: `composer.json`.
- Database driver: MySQL via the default `mysql` connection. Source: `config/database.php`.
- Frontend rendering: Blade templates under `resources/views/` with Bootstrap and icon CDNs in the main layout. Source: `resources/views/layouts/app.blade.php`.
- Asset tooling: Vite with Tailwind CSS configuration and `resources/js/app.js` entrypoint. Sources: `vite.config.js`, `tailwind.config.cjs`, `resources/css/app.css`, `resources/js/app.js`.

**Request Flow**
The app follows a standard Laravel flow: routes map to controllers, controllers use Eloquent models and return Blade views.

```mermaid
flowchart TD
  A[Browser] --> B[Routes in routes/web.php]
  B --> C[Controllers in app/Http/Controllers]
  C --> D[Models in app/Models (Eloquent)]
  C --> E[Blade views in resources/views]
  E --> A
```

Sources: `routes/web.php`, controllers in `app/Http/Controllers/`, models in `app/Models/`, views in `resources/views/`.

**Auth and Authorization**
- Session-based authentication with the `SystemUser` model and a custom login controller. Sources: `config/auth.php`, `app/Models/SystemUser.php`, `app/Http/Controllers/Auth/LoginController.php`.
- Role checks exist as middleware (`CheckRole`, `RoleMiddleware`) but are not wired to routes by default. Sources: `app/Http/Middleware/CheckRole.php`, `app/Http/Middleware/RoleMiddleware.php`, `routes/web.php`.

**Frontend Rendering**
- Blade views are the primary UI surface. Source: `resources/views/`.
- **Hypothesis:** Inertia + React scaffolding is present (`resources/js/app.jsx`, `@inertiajs/react` dependency), but no Inertia routes or root Blade view (`resources/views/app.blade.php`) were found, so React may be unused. Evidence: `resources/js/app.jsx`, `package.json`, `bootstrap/app.php`.
