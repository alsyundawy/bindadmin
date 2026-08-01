<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\ActivityLog;

class LogController
{
    public function index(): void
    {
        $logs = ActivityLog::recent(100);
        echo view('logs.index', [
            'title' => 'Activity Log - BindAdmin',
            'logs' => $logs,
        ]);
    }
}
