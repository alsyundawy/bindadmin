# DOCNOTE — BindAdmin

## Behaviour changes (v1.0.1)

### Database migration
- **Before:** `Database::migrate()` ran full schema and reset admin password to `admin123` on **every** HTTP request.
- **After:** Schema runs only when the `users` table does not exist (first install). Admin password is set once.

### Session
- Cookies: `HttpOnly`, `SameSite=Lax`, `Secure` when HTTPS is detected.
- Login regenerates session ID (`session_regenerate_id(true)`).
- Logout destroys session data and expires the session cookie.

### Zone names
- Rejected if they contain `..`, `/`, `\\`, or fail DNS-label pattern.
- Routes use `rawurlencode` / `rawurldecode` consistently.

### Redirects
- Absolute external URLs are rejected unless they start with configured `APP_URL`.

### Demo mode
- Controlled by `BIND_DEMO_MODE` in `.env` (default `true`).
- When `true`, zone files live under `storage/zones/` and `rndc` is not called.

## Dependencies
- PHP >= 8.2 with extensions: `pdo_sqlite`, `mbstring`, `json`
- No Composer runtime packages required (custom PSR-4 style autoload)
- Optional production: BIND9 + `rndc` binary usable by PHP-FPM user

## Security checklist for production
1. Set `APP_DEBUG=false`
2. Set a strong unique `APP_SECRET`
3. Set `BIND_DEMO_MODE=false` and configure real BIND paths
4. Change default `admin` / `admin123` immediately after first login
5. Serve only over HTTPS
6. Restrict panel access (firewall / VPN / allowlist)
7. Ensure PHP user can write only the intended zone directory
8. Keep SQLite file outside web root with restrictive permissions (`0600`, owner www-data)

## File structure notes
- Document root must be `public/` only
- `.env`, `database/*.sqlite`, and `storage/` must not be web-accessible

## Quality targets
- PHP 8.2+ strict types on application classes
- Prepared statements for all SQL
- CSRF on state-changing forms
- Output escaping via `e()`
- `escapeshellcmd` / `escapeshellarg` for `rndc`
