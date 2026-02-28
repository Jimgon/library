# Library Management System

Laravel-based library management system for Subic National High School.

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+ and npm
- MySQL 8+

## Setup

1. Install dependencies:

```bash
composer install
npm install
```

2. Create environment file and app key:

```bash
copy .env.example .env
php artisan key:generate
```

3. Configure database in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=library_mgmt
DB_USERNAME=root
DB_PASSWORD=your_password
```

## Database Migrations and Seeding

Run migrations:

```bash
php artisan migrate
```

Seed sample data and default admin (optional):

```bash
php artisan db:seed
```

## Create the First Admin

If no admin exists, visit:

- `http://your-app-url/create-admin`

This creates the first admin and signs you in.

## Run the App

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

Open:

- `http://127.0.0.1:8000`

## Troubleshooting

- If you see `Table 'system_users' doesn't exist`, run:

```bash
php artisan migrate --path=database/migrations/2026_02_27_000001_create_system_users_table.php
```

- If `teachers` table already exists but migration is pending:
  - Option A: insert the migration record into the `migrations` table manually, or
  - Option B: drop `teachers` then re-run `php artisan migrate`.
