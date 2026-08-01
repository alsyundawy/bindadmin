<?php

declare(strict_types=1);

// Secure session configuration (must be before session_start)
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.gc_maxlifetime', '7200');

if (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
) {
    ini_set('session.cookie_secure', '1');
}

session_start();

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/app/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

    if (is_readable($file)) {
        require $file;
    }
});

require dirname(__DIR__) . '/app/Helpers/functions.php';

date_default_timezone_set((string) config('app.timezone', 'Asia/Jakarta'));

try {
    \App\Models\Database::migrate();
} catch (Throwable $e) {
    http_response_code(500);
    if (config('app.debug')) {
        // NOSONAR - debug only, never enable in production
        echo 'DB Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    } else {
        echo 'Database initialization failed';
    }
    exit;
}

$rawUri = $_SERVER['REQUEST_URI'] ?? '/';
$uri = parse_url($rawUri, PHP_URL_PATH);
$uri = is_string($uri) ? (rtrim($uri, '/') ?: '/') : '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

function requireAuth(): void
{
    if (auth() === null) {
        flash('error', 'Silakan login terlebih dahulu');
        redirect('/login');
    }
}

try {
    if ($uri === '/login' && $method === 'GET') {
        (new \App\Controllers\AuthController())->loginForm();
        exit;
    }
    if ($uri === '/login' && $method === 'POST') {
        (new \App\Controllers\AuthController())->login();
        exit;
    }
    if ($uri === '/logout') {
        requireAuth();
        (new \App\Controllers\AuthController())->logout();
        exit;
    }

    requireAuth();

    if ($uri === '/' || $uri === '/dashboard') {
        (new \App\Controllers\DashboardController())->index();
        exit;
    }

    if ($uri === '/zones' && $method === 'GET') {
        (new \App\Controllers\ZoneController())->index();
        exit;
    }
    if ($uri === '/zones/create' && $method === 'GET') {
        (new \App\Controllers\ZoneController())->createForm();
        exit;
    }
    if ($uri === '/zones' && $method === 'POST') {
        (new \App\Controllers\ZoneController())->create();
        exit;
    }
    if (preg_match('#^/zones/([^/]+)$#', $uri, $m) && $method === 'GET') {
        (new \App\Controllers\ZoneController())->show(rawurldecode($m[1]));
        exit;
    }
    if (preg_match('#^/zones/([^/]+)/delete$#', $uri, $m) && $method === 'POST') {
        (new \App\Controllers\ZoneController())->delete(rawurldecode($m[1]));
        exit;
    }
    if (preg_match('#^/zones/([^/]+)/export$#', $uri, $m) && $method === 'GET') {
        (new \App\Controllers\ZoneController())->export(rawurldecode($m[1]));
        exit;
    }

    if (preg_match('#^/zones/([^/]+)/records$#', $uri, $m) && $method === 'POST') {
        (new \App\Controllers\RecordController())->store(rawurldecode($m[1]));
        exit;
    }
    if (preg_match('#^/zones/([^/]+)/records/(\d+)$#', $uri, $m) && $method === 'POST') {
        (new \App\Controllers\RecordController())->update(rawurldecode($m[1]), (int) $m[2]);
        exit;
    }
    if (preg_match('#^/zones/([^/]+)/records/(\d+)/delete$#', $uri, $m) && $method === 'POST') {
        (new \App\Controllers\RecordController())->destroy(rawurldecode($m[1]), (int) $m[2]);
        exit;
    }

    if ($uri === '/users' && $method === 'GET') {
        (new \App\Controllers\UserController())->index();
        exit;
    }
    if ($uri === '/users' && $method === 'POST') {
        (new \App\Controllers\UserController())->store();
        exit;
    }
    if (preg_match('#^/users/(\d+)$#', $uri, $m) && $method === 'POST') {
        (new \App\Controllers\UserController())->update((int) $m[1]);
        exit;
    }
    if (preg_match('#^/users/(\d+)/delete$#', $uri, $m) && $method === 'POST') {
        (new \App\Controllers\UserController())->destroy((int) $m[1]);
        exit;
    }

    if ($uri === '/logs') {
        (new \App\Controllers\LogController())->index();
        exit;
    }

    if ($uri === '/settings' && $method === 'GET') {
        (new \App\Controllers\SettingController())->index();
        exit;
    }
    if ($uri === '/settings' && $method === 'POST') {
        (new \App\Controllers\SettingController())->save();
        exit;
    }

    http_response_code(404);
    echo view('layouts.404', ['title' => '404 - Not Found']);
} catch (Throwable $e) {
    http_response_code(500);
    if (config('app.debug')) {
        // NOSONAR - intentional detailed error for development only
        echo '<pre style="background:#1e1e1e;color:#f8f8f2;padding:20px;margin:20px;border-radius:8px;">';
        echo '<strong style="color:#ff5555;">Error:</strong> '
            . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "\n\n";
        echo htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8');
        echo '</pre>';
    } else {
        echo 'Internal Server Error';
    }
}
