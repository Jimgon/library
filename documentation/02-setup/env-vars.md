**Environment Variables**
This list is derived from `.env.example` and config files. Values are not included.

**Core App**
- `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`, `APP_LOCALE`, `APP_FALLBACK_LOCALE`, `APP_FAKER_LOCALE`, `APP_MAINTENANCE_DRIVER`. Sources: `.env.example`, `config/app.php`.

**Database (MySQL)**
- `DB_CONNECTION`: default is `mysql`. Source: `config/database.php`.
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`. Source: `config/database.php`.
- `DB_CACHE_CONNECTION`, `DB_QUEUE_CONNECTION`: optional connections for cache and queue drivers. Sources: `config/cache.php`, `config/queue.php`.

**Cache, Session, Queue**
- `CACHE_STORE`, `CACHE_PREFIX`. Source: `config/cache.php`.
- `SESSION_DRIVER`, `SESSION_LIFETIME`, `SESSION_ENCRYPT`, `SESSION_PATH`, `SESSION_DOMAIN`. Source: `config/session.php`.
- `QUEUE_CONNECTION`, `QUEUE_FAILED_DRIVER`. Source: `config/queue.php`.

**Filesystem and Backups**
- `FILESYSTEM_DISK`: default `local`. Source: `config/filesystems.php`.

**Mail**
- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`. Source: `config/mail.php`.

**Redis and Memcached**
- `REDIS_*`, `MEMCACHED_HOST`. Sources: `config/database.php`, `config/cache.php`.

**AWS and S3**
- `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`, `AWS_USE_PATH_STYLE_ENDPOINT`. Source: `config/filesystems.php`.
