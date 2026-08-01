<?php

declare(strict_types=1);

namespace App\Models;

class ActivityLog
{
    /** @param mixed $details */
    public static function log(string $action, ?string $zone = null, $details = null): void
    {
        $user = auth();
        $stmt = Database::getInstance()->prepare(
            'INSERT INTO activity_logs (user_id, username, action, zone_name, details, ip_address)
             VALUES (?, ?, ?, ?, ?, ?)'
        );

        $detailsJson = null;
        if (is_array($details) || is_object($details)) {
            $detailsJson = json_encode($details, JSON_THROW_ON_ERROR);
        } elseif (is_string($details)) {
            $detailsJson = $details;
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        if (!is_string($ip) || !filter_var($ip, FILTER_VALIDATE_IP)) {
            $ip = null;
        }

        $stmt->execute([
            $user['id'] ?? null,
            $user['username'] ?? 'system',
            $action,
            $zone,
            $detailsJson,
            $ip,
        ]);
    }

    public static function recent(int $limit = 50): array
    {
        $limit = max(1, min(500, $limit));
        $stmt = Database::getInstance()->prepare(
            'SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT ?'
        );
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        foreach ($rows as &$row) {
            if (!empty($row['details'])) {
                $decoded = json_decode((string) $row['details'], true);
                $row['details'] = $decoded ?? $row['details'];
            }
        }
        unset($row);
        return $rows;
    }

    public static function byZone(string $zone, int $limit = 30): array
    {
        $limit = max(1, min(200, $limit));
        $stmt = Database::getInstance()->prepare(
            'SELECT * FROM activity_logs WHERE zone_name = ? ORDER BY created_at DESC LIMIT ?'
        );
        $stmt->bindValue(1, $zone);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function count(): int
    {
        return (int) Database::getInstance()->query('SELECT COUNT(*) FROM activity_logs')->fetchColumn();
    }
}
