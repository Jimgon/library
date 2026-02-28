**Application Logs**
- Laravel logging uses the `stack` channel by default with `single` as default stack member. Source: `config/logging.php`.
- Logs are written to `storage/logs/laravel.log` for `single` and `daily`. Source: `config/logging.php`.

**Activity Logs**
- The app records user actions in `activity_logs` via the `ActivityLog` model. Source: `app/Models/ActivityLog.php`.
- Activity logs are displayed in the Utilities logs page. Source: `app/Http/Controllers/UtilitiesController.php`, `resources/views/utilities/activity-log.blade.php`.
