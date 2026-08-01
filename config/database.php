<?php

declare(strict_types=1);

return [
    'driver' => 'sqlite',
    'path' => dirname(__DIR__) . '/' . ltrim((string) env('DB_PATH', 'database/bindadmin.sqlite'), '/'),
];
