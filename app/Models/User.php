<?php

declare(strict_types=1);

namespace App\Models;

class User
{
    public static function find(int $id): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT u.*, r.name as role, r.display_name as role_display, r.permissions
             FROM users u
             JOIN roles r ON u.role_id = r.id
             WHERE u.id = ?'
        );
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if ($user === false) {
            return null;
        }

        $user['permissions'] = json_decode((string) ($user['permissions'] ?? '[]'), true) ?: [];
        return $user;
    }

    public static function findByUsername(string $username): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT u.*, r.name as role, r.display_name as role_display, r.permissions
             FROM users u
             JOIN roles r ON u.role_id = r.id
             WHERE u.username = ?'
        );
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user === false) {
            return null;
        }

        $user['permissions'] = json_decode((string) ($user['permissions'] ?? '[]'), true) ?: [];
        return $user;
    }

    public static function all(): array
    {
        $stmt = Database::getInstance()->query(
            'SELECT u.id, u.username, u.email, u.role_id, u.is_active, u.last_login, u.created_at,
                    r.name as role, r.display_name as role_display
             FROM users u
             JOIN roles r ON u.role_id = r.id
             ORDER BY u.username'
        );
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $stmt = Database::getInstance()->prepare(
            'INSERT INTO users (username, email, password, role_id, is_active) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['username'],
            $data['email'],
            password_hash((string) $data['password'], PASSWORD_DEFAULT),
            (int) ($data['role_id'] ?? 2),
            (int) ($data['is_active'] ?? 1),
        ]);
        return (int) Database::getInstance()->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $fields = [];
        $values = [];

        foreach (['username', 'email', 'role_id', 'is_active'] as $f) {
            if (array_key_exists($f, $data)) {
                $fields[] = "{$f} = ?";
                $values[] = $data[$f];
            }
        }

        if (!empty($data['password'])) {
            $fields[] = 'password = ?';
            $values[] = password_hash((string) $data['password'], PASSWORD_DEFAULT);
        }

        if ($fields === []) {
            return false;
        }

        $fields[] = "updated_at = datetime('now')";
        $values[] = $id;

        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = Database::getInstance()->prepare($sql);
        return $stmt->execute($values);
    }

    public static function delete(int $id): bool
    {
        $stmt = Database::getInstance()->prepare('DELETE FROM users WHERE id = ? AND id != 1');
        return $stmt->execute([$id]);
    }

    public static function updateLastLogin(int $id): void
    {
        $stmt = Database::getInstance()->prepare(
            "UPDATE users SET last_login = datetime('now') WHERE id = ?"
        );
        $stmt->execute([$id]);
    }

    public static function count(): int
    {
        return (int) Database::getInstance()->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }
}
