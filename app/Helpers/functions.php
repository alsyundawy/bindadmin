<?php

declare(strict_types=1);

if (!function_exists('env')) {
    /**
     * @param mixed $default
     * @return mixed
     */
    function env(string $key, $default = null)
    {
        static $loaded = false;
        static $vars = [];

        if (!$loaded) {
            $envFile = dirname(__DIR__, 2) . '/.env';
            if (is_readable($envFile)) {
                $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                if ($lines !== false) {
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if ($line === '' || str_starts_with($line, '#')) {
                            continue;
                        }
                        if (!str_contains($line, '=')) {
                            continue;
                        }
                        [$k, $v] = explode('=', $line, 2);
                        $vars[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
                    }
                }
            }
            $loaded = true;
        }

        return $vars[$key] ?? $default;
    }
}

if (!function_exists('config')) {
    /**
     * @param mixed $default
     * @return mixed
     */
    function config(string $key, $default = null)
    {
        static $configs = [];

        $parts = explode('.', $key);
        $file = array_shift($parts);

        if ($file === null || $file === '') {
            return $default;
        }

        if (!preg_match('/^[a-z0-9_-]+$/i', $file)) {
            return $default;
        }

        if (!isset($configs[$file])) {
            $path = dirname(__DIR__, 2) . '/config/' . $file . '.php';
            $configs[$file] = is_readable($path) ? require $path : [];
        }

        $value = $configs[$file];
        foreach ($parts as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return $default;
            }
            $value = $value[$part];
        }

        return $value;
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        $base = dirname(__DIR__, 2);
        if ($path === '') {
            return $base;
        }
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('public_path')) {
    function public_path(string $path = ''): string
    {
        return base_path('public' . ($path !== '' ? '/' . ltrim($path, '/') : ''));
    }
}

if (!function_exists('storage_path')) {
    function storage_path(string $path = ''): string
    {
        return base_path('storage' . ($path !== '' ? '/' . ltrim($path, '/') : ''));
    }
}

if (!function_exists('view')) {
    function view(string $name, array $data = []): string
    {
        if (!preg_match('/^[a-z0-9_.\/-]+$/i', $name)) {
            throw new RuntimeException('Invalid view name.');
        }

        $viewFile = base_path('app/Views/' . str_replace('.', '/', $name) . '.php');
        if (!is_readable($viewFile)) {
            throw new RuntimeException("View [{$name}] not found.");
        }

        $render = static function (string $file, array $data): string {
            extract($data, EXTR_SKIP);
            ob_start();
            include $file;
            return (string) ob_get_clean();
        };

        return $render($viewFile, $data);
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url, int $code = 302): never
    {
        if (preg_match('#^https?://#i', $url)) {
            $appUrl = (string) config('app.url', '');
            if ($appUrl === '' || !str_starts_with($url, $appUrl)) {
                $url = '/';
            }
        }

        header('Location: ' . $url, true, $code);
        exit;
    }
}

if (!function_exists('json_response')) {
    function json_response(array $data, int $code = 200): never
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_THROW_ON_ERROR);
        exit;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('verify_csrf')) {
    function verify_csrf(): bool
    {
        $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!is_string($token) || $token === '') {
            return false;
        }
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        if (!is_string($sessionToken) || $sessionToken === '') {
            return false;
        }
        return hash_equals($sessionToken, $token);
    }
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('old')) {
    /**
     * @param mixed $default
     * @return mixed
     */
    function old(string $key, $default = '')
    {
        return $_SESSION['_old'][$key] ?? $default;
    }
}

if (!function_exists('flash')) {
    /**
     * @param mixed $value
     * @return mixed
     */
    function flash(string $key, $value = null)
    {
        if (func_num_args() === 2) {
            $_SESSION['_flash'][$key] = $value;
            return null;
        }

        $val = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $val;
    }
}

if (!function_exists('auth')) {
    function auth(): ?array
    {
        $user = $_SESSION['user'] ?? null;
        return is_array($user) ? $user : null;
    }
}

if (!function_exists('is_admin')) {
    function is_admin(): bool
    {
        $user = auth();
        return $user !== null && ($user['role'] ?? '') === 'admin';
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return rtrim((string) config('app.url', ''), '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        return rtrim((string) config('app.url', ''), '/') . '/' . ltrim($path, '/');
    }
}
