**Caching**
- Default cache store is `database`. Source: `config/cache.php`.
- Cache is used for control number generation in book creation and copy updates. Source: `app/Http/Controllers/BookController.php`.

**Sessions**
- Session driver defaults to `database`. Source: `config/session.php`.

**Filesystem Storage**
- Default disk is `local` with a private root at `storage/app/private`. Source: `config/filesystems.php`.
- Public disk is `storage/app/public` and requires `storage:link`. Source: `config/filesystems.php`.
- Backups are stored under `storage/app/backups`. Source: `app/Http/Controllers/UtilitiesController.php`.
