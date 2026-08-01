# Changelog

All notable changes to BindAdmin are documented in this file.

## [1.0.2] - 2026-08-02

### Fixed
- Added missing `: void` return types on Dashboard, Log, Setting controllers
- Setting model: safer JSON encoding with `JSON_THROW_ON_ERROR`
- Template model: null-safe type checks

### Documentation
- Professional README with full install and deploy guide
- DOCNOTE expanded with security checklist

## [1.0.1] - 2026-08-02

### Security
- Fixed critical bug: `Database::migrate()` no longer resets admin password on every request
- Secured session configuration (`use_strict_mode`, `httponly`, `samesite`, conditional `secure`)
- Session fixation prevention via `session_regenerate_id(true)` on successful login
- Proper session destruction on logout (cookie invalidation)
- Open-redirect protection in `redirect()` helper
- Path traversal prevention for zone names, view names, and config file keys
- Zone file content sanitization (strip control characters / newlines)
- Input validation for users (username pattern, email, password length, role whitelist)
- Soft brute-force delay on failed login
- CSRF verification enforced on all state-changing actions
- XSS mitigation via consistent `e()` / `htmlspecialchars` with `ENT_SUBSTITUTE`
- Command execution uses `escapeshellcmd` + `escapeshellarg` for `rndc`
- Error messages no longer leak internal paths in production mode

### Fixed
- `migrate()` only runs schema on first install (empty DB)
- Missing `declare(strict_types=1)` across PHP application classes
- Return types added to controller methods
- User model no longer selects password hash in `all()` listing
- Self-delete and primary-admin protection improved
- File writes use `LOCK_EX`
- `rawurldecode` / `rawurlencode` consistency for zone names in routes

### Changed
- Autoloader and helpers use safer path resolution
- PDO WAL mode enabled for SQLite
- Activity log IP validation
- Bound integer parameters for LIMIT queries

## [1.0.0] - 2026-08-02

### Added
- Initial BindAdmin release: Nginx + PHP + SQLite BIND9 admin GUI
- Dashboard, Zones, Records, Users/Roles, Activity Log, Settings
- Demo mode for development without real BIND
- Bootstrap 5.3 + Font Awesome 6.7 dark UI
