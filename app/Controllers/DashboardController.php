<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\BindService;
use App\Models\User;
use App\Models\ActivityLog;

class DashboardController
{
    public function index(): void
    {
        $bind = new BindService();
        $zones = $bind->listZones();
        $status = $bind->getServerStatus();
        $recentLogs = ActivityLog::recent(10);
        $userCount = User::count();

        $totalRecords = 0;
        foreach ($zones as $z) {
            $totalRecords += (int) ($z['records_count'] ?? 0);
        }

        echo view('dashboard.index', [
            'title' => 'Dashboard - BindAdmin',
            'zones' => $zones,
            'zoneCount' => count($zones),
            'totalRecords' => $totalRecords,
            'userCount' => $userCount,
            'status' => $status,
            'recentLogs' => $recentLogs,
            'demoMode' => $bind->isDemoMode(),
        ]);
    }
}
