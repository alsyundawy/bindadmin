<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $path = (string) config('database.path');
            $dir = dirname($path);

            if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
                throw new RuntimeException('Unable to create database directory.');
            }

            try {
                self::$instance = new PDO('sqlite:' . $path, null, null, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
                self::$instance->exec('PRAGMA foreign_keys = ON;');
                self::$instance->exec('PRAGMA journal_mode = WAL;');
            } catch (PDOException $e) {
                throw new RuntimeException('Database connection failed.');
            }
        }

        return self::$instance;
    }

    /**
     * Run schema only when database is empty / first install.
     * Does NOT reset admin password on subsequent requests.
     */
    public static function migrate(): void
    {
        $pdo = self::getInstance();

        $tableExists = $pdo->query(
            "SELECT name FROM sqlite_master WHERE type='table' AND name='users'"
        )->fetchColumn();

        if ($tableExists) {
            return;
        }

        $schemaPath = base_path('database/schema.sql');
        if (!is_readable($schemaPath)) {
            throw new RuntimeException('Database schema file is missing.');
        }

        $schema = file_get_contents($schemaPath);
        if ($schema === false) {
            throw new RuntimeException('Unable to read database schema.');
        }

        $pdo->exec($schema);

        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE username = ?');
        $stmt->execute([$hash, 'admin']);
    }
}
