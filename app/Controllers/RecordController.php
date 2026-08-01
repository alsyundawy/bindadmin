<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\BindService;

class RecordController
{
    private BindService $bind;

    public function __construct()
    {
        $this->bind = new BindService();
    }

    public function store(string $zone): void
    {
        if (!verify_csrf()) {
            flash('error', 'Invalid CSRF token');
            redirect('/zones/' . rawurlencode($zone));
        }

        try {
            $this->bind->addRecord($zone, [
                'name' => trim((string) ($_POST['name'] ?? '@')),
                'type' => (string) ($_POST['type'] ?? 'A'),
                'content' => trim((string) ($_POST['content'] ?? '')),
                'ttl' => (int) ($_POST['ttl'] ?? 3600),
                'priority' => isset($_POST['priority']) && $_POST['priority'] !== ''
                    ? (int) $_POST['priority']
                    : null,
            ]);
            flash('success', 'Record berhasil ditambahkan');
        } catch (\Throwable $e) {
            flash('error', $e->getMessage());
        }

        redirect('/zones/' . rawurlencode($zone));
    }

    public function update(string $zone, int $index): void
    {
        if (!verify_csrf()) {
            flash('error', 'Invalid CSRF token');
            redirect('/zones/' . rawurlencode($zone));
        }

        if ($index < 0) {
            flash('error', 'Record tidak valid');
            redirect('/zones/' . rawurlencode($zone));
        }

        try {
            $this->bind->updateRecord($zone, $index, [
                'name' => trim((string) ($_POST['name'] ?? '@')),
                'type' => (string) ($_POST['type'] ?? 'A'),
                'content' => trim((string) ($_POST['content'] ?? '')),
                'ttl' => (int) ($_POST['ttl'] ?? 3600),
                'priority' => isset($_POST['priority']) && $_POST['priority'] !== ''
                    ? (int) $_POST['priority']
                    : null,
            ]);
            flash('success', 'Record berhasil diupdate');
        } catch (\Throwable $e) {
            flash('error', $e->getMessage());
        }

        redirect('/zones/' . rawurlencode($zone));
    }

    public function destroy(string $zone, int $index): void
    {
        if (!verify_csrf()) {
            flash('error', 'Invalid CSRF token');
            redirect('/zones/' . rawurlencode($zone));
        }

        if ($index < 0) {
            flash('error', 'Record tidak valid');
            redirect('/zones/' . rawurlencode($zone));
        }

        try {
            $this->bind->deleteRecord($zone, $index);
            flash('success', 'Record berhasil dihapus');
        } catch (\Throwable $e) {
            flash('error', $e->getMessage());
        }

        redirect('/zones/' . rawurlencode($zone));
    }
}
