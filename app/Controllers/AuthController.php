<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Models\ActivityLog;

class AuthController
{
    public function loginForm(): void
    {
        if (auth() !== null) {
            redirect('/dashboard');
        }

        echo view('auth.login', [
            'title' => 'Login - BindAdmin',
        ]);
    }

    public function login(): void
    {
        if (!verify_csrf()) {
            flash('error', 'Invalid CSRF token');
            redirect('/login');
        }

        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            flash('error', 'Username atau password salah');
            $_SESSION['_old'] = ['username' => $username];
            redirect('/login');
        }

        $user = User::findByUsername($username);

        $valid = $user !== null
            && password_verify($password, (string) ($user['password'] ?? ''));

        if (!$valid) {
            usleep(200000);
            flash('error', 'Username atau password salah');
            $_SESSION['_old'] = ['username' => $username];
            redirect('/login');
        }

        if (empty($user['is_active'])) {
            flash('error', 'Akun nonaktif');
            redirect('/login');
        }

        session_regenerate_id(true);

        unset($user['password']);
        $_SESSION['user'] = $user;
        unset($_SESSION['_old']);

        User::updateLastLogin((int) $user['id']);
        ActivityLog::log('user.login');

        flash('success', 'Selamat datang, ' . $user['username']);
        redirect('/dashboard');
    }

    public function logout(): void
    {
        ActivityLog::log('user.logout');

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                (bool) $params['secure'],
                (bool) $params['httponly']
            );
        }
        session_destroy();

        session_start();
        flash('success', 'Anda telah logout');
        redirect('/login');
    }
}
