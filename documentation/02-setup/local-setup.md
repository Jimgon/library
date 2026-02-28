**Prerequisites**
- PHP `^8.2` and Composer. Source: `composer.json`.
- Node.js and npm for Vite assets. Source: `package.json`.
- MySQL server. Source: `config/database.php`.

**Local Setup**
1. Install PHP dependencies: `composer install`.
2. Install JS dependencies: `npm install`.
3. Copy and edit env file: `cp .env.example .env` and set required values. Source: `.env.example`.
4. Generate app key: `php artisan key:generate`.
5. Configure MySQL connection (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`). Source: `config/database.php`.
6. Run migrations: `php artisan migrate`.
7. Run the dev environment using the provided script: `composer run dev`. This starts the PHP server, queue listener, log tailing, and Vite dev server. Source: `composer.json`.

**Seeding**
- `DatabaseSeeder` creates sample students, books, and a default admin account. Source: `database/seeders/DatabaseSeeder.php`.
- `AdminSeeder` creates a default admin account with a hardcoded password. Do not use this in production. Source: `database/seeders/AdminSeeder.php`.
