<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Setting;
use App\Models\ActivityLog;

class SettingController
{
    public function index(): void
    {
        if (!is_admin()) {
            flash('error', 'Akses ditolak');
            redirect('/dashboard');
        }

        $settings = Setting::all();
        echo view('settings.index', [
            'title' => 'Settings - BindAdmin',
            'settings' => $settings,
            'bindConfig' => [
                'zone_path' => config('bind.zone_path'),
                'demo_mode' => config('bind.demo_mode'),
                'default_ttl' => config('bind.default_ttl'),
                'default_ns1' => config('bind.default_ns1'),
                'default_ns2' => config('bind.default_ns2'),
            ],
        ]);
    }

    public function save(): void
    {
        if (!is_admin() || !verify_csrf()) {
            flash('error', 'Akses ditolak');
            redirect('/settings');
        }

        $siteName = trim((string) ($_POST['site_name'] ?? 'BindAdmin'));
        $recordsPerPage = (int) ($_POST['records_per_page'] ?? 50);

        $siteName = mb_substr(preg_replace('/[\x00-\x1f\x7f]/', '', $siteName) ?? 'BindAdmin', 0, 100);
        $recordsPerPage = max(10, min(500, $recordsPerPage));

        Setting::set('site_name', $siteName);
        Setting::set('records_per_page', (string) $recordsPerPage);

        ActivityLog::log('settings.update');
        flash('success', 'Settings berhasil disimpan');
        redirect('/settings');
    }
}
