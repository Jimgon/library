**Frontend Stack**
- Server-rendered Blade templates under `resources/views/`. Source: `resources/views/`.
- Layout uses Bootstrap 5 and icon CDNs. Source: `resources/views/layouts/app.blade.php`.
- Tailwind CSS is configured and compiled via Vite. Sources: `tailwind.config.cjs`, `resources/css/app.css`, `vite.config.js`.
- JS entrypoint is `resources/js/app.js`, which loads `resources/js/bootstrap.js`. Sources: `resources/js/app.js`, `resources/js/bootstrap.js`.

**Framework Notes**
- Inertia + React scaffolding exists in `resources/js/app.jsx`, and React dependencies are present in `package.json`. **Hypothesis:** The app currently renders primarily via Blade, as no Inertia root view was found in `resources/views/`. Sources: `resources/js/app.jsx`, `package.json`, `resources/views/`.

**Folder Layout**
- `resources/views/layouts/app.blade.php` defines the main layout and sidebar navigation. Source: `resources/views/layouts/app.blade.php`.
- Page templates exist for auth, books, borrow, users, staff, utilities, dashboard, and reports. Sources: `resources/views/*`.
