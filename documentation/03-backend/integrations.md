**External Integrations**
- MySQL: the app uses the default `mysql` connection. Source: `config/database.php`.
- Backups: database backups are executed via `mysqldump` and zipped to `storage/app/backups`. Source: `app/Http/Controllers/UtilitiesController.php`.
- Mail: mailer defaults to `log` driver. Source: `config/mail.php`.
- Redis and Memcached are configured but optional. Sources: `config/database.php`, `config/cache.php`.
