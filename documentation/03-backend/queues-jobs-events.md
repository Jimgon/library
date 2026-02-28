**Queues And Jobs**
- Queue configuration exists with default driver `database`. Source: `config/queue.php`.
- The dev script runs `php artisan queue:listen --tries=1`. Source: `composer.json`.
- No job classes were found under `app/` and no queue dispatches were found in controllers. Source: `app/`.

**Events And Listeners**
- No custom events or listeners are defined in `app/`. Source: `app/`.

**Scheduled Tasks**
- No scheduled tasks are configured in `App\Console\Kernel`. Source: `app/Console/Kernel.php`.
