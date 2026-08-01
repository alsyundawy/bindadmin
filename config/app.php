<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'BindAdmin'),
    'url' => env('APP_URL', 'http://localhost'),
    'debug' => filter_var(env('APP_DEBUG', 'true'), FILTER_VALIDATE_BOOLEAN),
    'secret' => env('APP_SECRET', 'change-me-in-production'),
    'timezone' => 'Asia/Jakarta',
    'session_lifetime' => (int) env('SESSION_LIFETIME', 7200),
];
