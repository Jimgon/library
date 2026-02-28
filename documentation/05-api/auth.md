**API Authentication**
- The application uses session-based authentication (`web` guard) with the `SystemUser` provider. Source: `config/auth.php`.
- No token-based or API guard authentication is configured in `config/auth.php`.
- Login is performed via `/login` and sets a session cookie. Source: `routes/web.php`, `app/Http/Controllers/Auth/LoginController.php`.
