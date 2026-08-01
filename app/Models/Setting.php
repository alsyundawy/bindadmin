<?php

declare(strict_types=1);

namespace App\Models;

class Setting
{
    /**
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $stmt = Database::getInstance()->prepare('SELECT value FROM settings WHERE key = ?');
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        return $row !== false ? $row['value'] : $default;
    }

    /**
     * @param mixed $value
     */
    public static function set(string $key, $value): void
    {
        $stmt = Database::getInstance()->prepare(
            "INSERT INTO settings (key, value, updated_at) VALUES (?, ?, datetime('now'))
             ON CONFLICT(key) DO UPDATE SET value = excluded.value, updated_at = datetime('now')"
        );
        $encoded = is_array($value) || is_object($value)
            ? json_encode($value, JSON_THROW_ON_ERROR)
            : (string) $value;
        $stmt->execute([$key, $encoded]);
    }

    public static function all(): array
    {
        $rows = Database::getInstance()->query('SELECT key, value FROM settings')->fetchAll();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['key']] = $row['value'];
        }
        return $result;
    }
}
