# DOCNOTE — BindAdmin

## Behaviour changes (v1.0.1)

### Database migration
- **Before:** `Database::migrate()` ran full schema + reset admin password to `admin123` on **every** HTTP request.
- **After:** Schema runs only when the `users` table does not exist (first install). Admin password is set once.

### Session
- Cookies: HttpOnly, SameSite=Lax, Secure when HTTPS.
- Login regenerates session ID. Logout destroys session + cookie.

### Zone names
- Rejected if they contain `..`, `/`, `\\`, or fail DNS-label pattern.

### Redirects
- Absolute external URLs rejected unless they start with configured APP_URL.

### Demo mode
- Controlled by BIND_DEMO_MODE in .env (default true).

## Dependencies
- PHP >= 8.2 with pdo_sqlite, mbstring, json

## Security checklist
1. APP_DEBUG=false in production
2. Strong APP_SECRET
3. BIND_DEMO_MODE=false for real BIND
4. Change default admin/admin123 immediately
5. HTTPS only
6. Restrict panel access
