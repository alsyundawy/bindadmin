<?php

declare(strict_types=1);

namespace App\Models;

class Template
{
    public static function all(?string $type = null): array
    {
        if ($type !== null) {
            $stmt = Database::getInstance()->prepare(
                'SELECT * FROM templates WHERE type = ? ORDER BY name'
            );
            $stmt->execute([$type]);
            return $stmt->fetchAll();
        }

        return Database::getInstance()
            ->query('SELECT * FROM templates ORDER BY type, name')
            ->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::getInstance()->prepare('SELECT * FROM templates WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    public static function create(array $data): int
    {
        $stmt = Database::getInstance()->prepare(
            'INSERT INTO templates (name, type, description, content, created_by) VALUES (?, ?, ?, ?, ?)'
        );
        $user = auth();
        $stmt->execute([
            $data['name'],
            $data['type'],
            $data['description'] ?? null,
            $data['content'],
            $user['id'] ?? null,
        ]);
        return (int) Database::getInstance()->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $stmt = Database::getInstance()->prepare(
            "UPDATE templates SET name = ?, type = ?, description = ?, content = ?, updated_at = datetime('now') WHERE id = ?"
        );
        return $stmt->execute([
            $data['name'],
            $data['type'],
            $data['description'] ?? null,
            $data['content'],
            $id,
        ]);
    }

    public static function delete(int $id): bool
    {
        $stmt = Database::getInstance()->prepare('DELETE FROM templates WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
