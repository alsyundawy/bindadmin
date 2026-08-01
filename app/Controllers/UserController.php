<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Models\Database;
use App\Models\ActivityLog;

class UserController
{
    public function index(): void
    {
        if (!is_admin()) {
            flash('error', 'Akses ditolak');
            redirect('/dashboard');
        }

        $users = User::all();
        $roles = Database::getInstance()
            ->query('SELECT id, name, display_name FROM roles ORDER BY id')
            ->fetchAll();

        echo view('users.index', [
            'title' => 'Users - BindAdmin',
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function store(): void
    {
        if (!is_admin() || !verify_csrf()) {
            flash('error', 'Akses ditolak');
            redirect('/users');
        }

        $username = trim((string) ($_POST['username'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $roleId = (int) ($_POST['role_id'] ?? 2);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($username === '' || $email === '' || $password === '') {
            flash('error', 'Username, email, dan password wajib diisi');
            redirect('/users');
        }

        if (strlen($password) < 6) {
            flash('error', 'Password minimal 6 karakter');
            redirect('/users');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Format email tidak valid');
            redirect('/users');
        }

        if (!preg_match('/^[a-zA-Z0-9_\-\.]{3,50}$/', $username)) {
            flash('error', 'Username hanya boleh huruf, angka, underscore, titik (3-50 karakter)');
            redirect('/users');
        }

        if (!in_array($roleId, [1, 2, 3], true)) {
            $roleId = 2;
        }

        try {
            User::create([
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'role_id' => $roleId,
                'is_active' => $isActive,
            ]);
            ActivityLog::log('user.create', null, ['username' => $username]);
            flash('success', 'User berhasil ditambahkan');
        } catch (\Throwable $e) {
            flash('error', 'Gagal membuat user. Username atau email mungkin sudah digunakan.');
        }

        redirect('/users');
    }

    public function update(int $id): void
    {
        if (!is_admin() || !verify_csrf()) {
            flash('error', 'Akses ditolak');
            redirect('/users');
        }

        if ($id < 1) {
            flash('error', 'User tidak valid');
            redirect('/users');
        }

        $username = trim((string) ($_POST['username'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $roleId = (int) ($_POST['role_id'] ?? 2);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $password = (string) ($_POST['password'] ?? '');

        if ($username === '' || $email === '') {
            flash('error', 'Username dan email wajib diisi');
            redirect('/users');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Format email tidak valid');
            redirect('/users');
        }

        if (!preg_match('/^[a-zA-Z0-9_\-\.]{3,50}$/', $username)) {
            flash('error', 'Username tidak valid');
            redirect('/users');
        }

        if (!in_array($roleId, [1, 2, 3], true)) {
            $roleId = 2;
        }

        if ($id === 1) {
            $roleId = 1;
            $isActive = 1;
        }

        $data = [
            'username' => $username,
            'email' => $email,
            'role_id' => $roleId,
            'is_active' => $isActive,
        ];

        if ($password !== '') {
            if (strlen($password) < 6) {
                flash('error', 'Password minimal 6 karakter');
                redirect('/users');
            }
            $data['password'] = $password;
        }

        try {
            User::update($id, $data);
            ActivityLog::log('user.update', null, ['id' => $id]);
            flash('success', 'User berhasil diupdate');
        } catch (\Throwable $e) {
            flash('error', 'Gagal mengupdate user.');
        }

        redirect('/users');
    }

    public function destroy(int $id): void
    {
        if (!is_admin() || !verify_csrf()) {
            flash('error', 'Akses ditolak');
            redirect('/users');
        }

        if ($id === 1) {
            flash('error', 'Tidak bisa menghapus admin utama');
            redirect('/users');
        }

        if ($id < 1) {
            flash('error', 'User tidak valid');
            redirect('/users');
        }

        $current = auth();
        if ($current !== null && (int) $current['id'] === $id) {
            flash('error', 'Tidak bisa menghapus akun sendiri');
            redirect('/users');
        }

        User::delete($id);
        ActivityLog::log('user.delete', null, ['id' => $id]);
        flash('success', 'User berhasil dihapus');
        redirect('/users');
    }
}
