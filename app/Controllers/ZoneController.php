<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\BindService;
use App\Models\ActivityLog;

class ZoneController
{
    private BindService $bind;

    public function __construct()
    {
        $this->bind = new BindService();
    }

    public function index(): void
    {
        $zones = $this->bind->listZones();
        echo view('zones.index', [
            'title' => 'Zones - BindAdmin',
            'zones' => $zones,
            'demoMode' => $this->bind->isDemoMode(),
        ]);
    }

    public function createForm(): void
    {
        echo view('zones.create', [
            'title' => 'Create Zone - BindAdmin',
        ]);
    }

    public function create(): void
    {
        if (!verify_csrf()) {
            flash('error', 'Invalid CSRF token');
            redirect('/zones');
        }

        $zone = trim((string) ($_POST['name'] ?? ''));
        $ip = trim((string) ($_POST['ip'] ?? ''));
        $ns1 = trim((string) ($_POST['ns1'] ?? ''));
        $ns2 = trim((string) ($_POST['ns2'] ?? ''));

        if ($zone === '') {
            flash('error', 'Nama zone wajib diisi');
            redirect('/zones/create');
        }

        try {
            $this->bind->createZone($zone, [
                'ip' => $ip !== '' ? $ip : null,
                'ns1' => $ns1 !== '' ? $ns1 : null,
                'ns2' => $ns2 !== '' ? $ns2 : null,
            ]);
            flash('success', "Zone {$zone} berhasil dibuat");
            redirect('/zones/' . rawurlencode($zone));
        } catch (\Throwable $e) {
            flash('error', $e->getMessage());
            $_SESSION['_old'] = [
                'name' => $zone,
                'ip' => $ip,
                'ns1' => $ns1,
                'ns2' => $ns2,
            ];
            redirect('/zones/create');
        }
    }

    public function show(string $zone): void
    {
        try {
            $info = $this->bind->getZoneInfo($zone);
            if (empty($info['exists'])) {
                flash('error', 'Zone tidak ditemukan');
                redirect('/zones');
            }
            $records = $this->bind->getRecords($zone);
            $logs = ActivityLog::byZone($zone, 20);

            echo view('zones.show', [
                'title' => $zone . ' - BindAdmin',
                'zone' => $info,
                'records' => $records,
                'logs' => $logs,
            ]);
        } catch (\Throwable $e) {
            flash('error', $e->getMessage());
            redirect('/zones');
        }
    }

    public function delete(string $zone): void
    {
        if (!verify_csrf()) {
            flash('error', 'Invalid CSRF token');
            redirect('/zones');
        }

        try {
            $this->bind->deleteZone($zone);
            flash('success', "Zone {$zone} berhasil dihapus");
        } catch (\Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('/zones');
    }

    public function export(string $zone): void
    {
        try {
            $content = $this->bind->exportZone($zone);
            $safeName = preg_replace('/[^a-zA-Z0-9.\-]/', '_', $zone) ?: 'zone';
            header('Content-Type: text/plain; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $safeName . '.zone"');
            header('X-Content-Type-Options: nosniff');
            echo $content;
            exit;
        } catch (\Throwable $e) {
            flash('error', $e->getMessage());
            redirect('/zones/' . rawurlencode($zone));
        }
    }
}
