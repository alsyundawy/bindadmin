# BindAdmin

**Modern BIND9 DNS Administration Panel**

Lightweight, secure web GUI for managing BIND9 zones and records — inspired by PowerDNS-Admin.  
Built with **Nginx · PHP 8.2+ · SQLite3 · Bootstrap 5.3 · Font Awesome 6.7**.

[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![UI](https://img.shields.io/badge/UI-Bootstrap%205.3-7952B3)](https://getbootstrap.com/)

---

## Features

| Area | Capabilities |
|------|----------------|
| **Zones** | Create, list, delete, export zone files |
| **Records** | A, AAAA, CNAME, MX, TXT, NS, PTR, SRV, CAA |
| **Users & Roles** | Admin · Operator · Viewer (RBAC) |
| **Security** | CSRF, secure sessions, password hashing, input validation |
| **Ops** | Activity log, zone backups before every write, serial auto-increment |
| **Modes** | **Demo mode** (no BIND required) · **Production** (`rndc` integration) |
| **UI** | Dark / light theme, responsive sidebar layout |

---

## Requirements

| Component | Version / notes |
|-----------|-----------------|
| PHP | **8.2+** with `pdo_sqlite`, `mbstring`, `json` |
| Web server | Nginx (recommended) or Apache |
| DNS (production) | BIND 9.x + `rndc` |
| Database | SQLite3 (file-based, zero external DB server) |

---

## Quick start (Demo mode)

Ideal for testing without installing BIND.

```bash
git clone https://github.com/alsyundawy/bindadmin.git
cd bindadmin
cp .env.example .env

# Edit APP_URL if needed; keep BIND_DEMO_MODE=true
cd public
php -S 0.0.0.0:8080
```

Open **http://localhost:8080**

| Field | Value |
|-------|--------|
| Username | `admin` |
| Password | `admin123` |

> Change the default password immediately after first login.

Zone files in demo mode are stored under `storage/zones/`.

---

## Production installation

### 1. System packages (Ubuntu / Debian)

```bash
sudo apt update
sudo apt install -y nginx php8.3-fpm php8.3-sqlite3 php8.3-mbstring \
                    bind9 bind9utils git
```

### 2. Application files

```bash
sudo mkdir -p /var/www
sudo git clone https://github.com/alsyundawy/bindadmin.git /var/www/bindadmin
cd /var/www/bindadmin
sudo cp .env.example .env
sudo chown -R www-data:www-data /var/www/bindadmin
sudo chmod -R 755 /var/www/bindadmin
sudo chmod -R 775 storage database
```

### 3. Configure `.env`

```bash
sudo nano /var/www/bindadmin/.env
```

Minimum production settings:

```ini
APP_NAME=BindAdmin
APP_URL=https://dns-admin.example.com
APP_DEBUG=false
APP_SECRET=generate-a-long-random-string-here

DB_PATH=database/bindadmin.sqlite

BIND_DEMO_MODE=false
BIND_ZONE_PATH=/etc/bind/zones
BIND_NAMED_CONF=/etc/bind/named.conf.local
BIND_RNDC=/usr/sbin/rndc
BIND_RNDC_KEY=/etc/bind/rndc.key
BIND_DEFAULT_TTL=3600
BIND_DEFAULT_NS1=ns1.example.com.
BIND_DEFAULT_NS2=ns2.example.com.
BIND_DEFAULT_EMAIL=hostmaster.example.com.
```

Generate a secret:

```bash
openssl rand -hex 32
```

### 4. BIND zone directory and permissions

```bash
sudo mkdir -p /etc/bind/zones
sudo chown root:bind /etc/bind/zones
sudo chmod 775 /etc/bind/zones
sudo usermod -aG bind www-data

sudo chmod 640 /etc/bind/rndc.key
sudo chown root:bind /etc/bind/rndc.key
```

> **Risk note:** Giving the web user write access to zone files and `rndc` increases blast radius if the panel is compromised. Prefer network isolation, HTTPS, and strong authentication.

### 5. Nginx virtual host

```nginx
server {
    listen 80;
    server_name dns-admin.example.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name dns-admin.example.com;

    ssl_certificate     /etc/ssl/certs/dns-admin.crt;
    ssl_certificate_key /etc/ssl/private/dns-admin.key;

    root /var/www/bindadmin/public;
    index index.php;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\. {
        deny all;
    }

    location ~ ^/(storage|database|config|app)/ {
        deny all;
    }
}
```

```bash
sudo nginx -t && sudo systemctl reload nginx
```

### 6. First login

1. Open `https://dns-admin.example.com`
2. Sign in with `admin` / `admin123`
3. Change password under **Users**
4. Create your first zone

---

## Project structure

```
bindadmin/
├── public/                 # Web root (only this should be public)
│   ├── index.php           # Front controller + router
│   └── .htaccess
├── app/
│   ├── Controllers/
│   ├── Models/
│   ├── Services/           # BindService (zone parser + rndc)
│   ├── Views/
│   └── Helpers/
├── config/
├── database/
├── storage/
├── .env.example
├── CHANGELOG.md
├── DOCNOTE.md
└── README.md
```

---

## Security checklist

- [ ] `APP_DEBUG=false`
- [ ] Strong unique `APP_SECRET`
- [ ] Default admin password changed
- [ ] HTTPS only
- [ ] Document root = `public/` only
- [ ] `.env`, `database/`, `storage/` not web-accessible
- [ ] Panel access limited (VPN / IP allowlist)
- [ ] SQLite file permissions restricted (`0600`, owner `www-data`)

---

## Configuration reference

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_URL` | Public base URL | `http://localhost` |
| `APP_DEBUG` | Verbose errors | `true` (set `false` in prod) |
| `APP_SECRET` | App secret | — |
| `DB_PATH` | SQLite path relative to project | `database/bindadmin.sqlite` |
| `BIND_DEMO_MODE` | File-based demo without BIND | `true` |
| `BIND_ZONE_PATH` | Directory for zone files | `/etc/bind/zones` |
| `BIND_RNDC` | Path to `rndc` | `/usr/sbin/rndc` |
| `BIND_DEFAULT_TTL` | Default TTL for new records | `3600` |

---

## Default credentials

| Username | Password | Role |
|----------|----------|------|
| `admin` | `admin123` | Administrator |

Database schema and initial admin user are created automatically on first request.

---

## Troubleshooting

| Symptom | Check |
|---------|--------|
| Blank / 500 page | PHP-FPM error log; set `APP_DEBUG=true` temporarily |
| Database connection failed | `php-sqlite3` installed; `database/` writable by `www-data` |
| Zone not reloading | `rndc status` as web user; key permissions; `BIND_DEMO_MODE` |
| Permission denied writing zone | Zone dir group `bind`, mode `775`; user in group `bind` |
| CSRF errors | Cookies enabled; same site; clock skew |

```bash
sudo -u www-data php -r 'echo "ok\n";'
sudo -u www-data rndc status
tail -f /var/log/nginx/error.log
tail -f /var/log/php8.3-fpm.log
```

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md). Behaviour notes: [DOCNOTE.md](DOCNOTE.md).

---

## License

MIT License — free for personal and commercial use.

---

## Credits

Inspired by [PowerDNS-Admin](https://github.com/PowerDNS-Admin/PowerDNS-Admin).  
Built for operators who prefer **BIND9** with a clean, modern control panel.
