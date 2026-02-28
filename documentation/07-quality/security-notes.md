**Security Notes**
- **GET logout route**: `/logout` uses GET rather than POST, which is more susceptible to CSRF. Source: `routes/web.php`.
- **Role enforcement**: staff management routes are not protected by role middleware, so any authenticated user could potentially access them. Source: `routes/web.php`, `app/Http/Kernel.php`.
- **Hardcoded seed credentials**: `AdminSeeder` creates a default admin with a hardcoded password. This should never be used in production. Source: `database/seeders/AdminSeeder.php`.
