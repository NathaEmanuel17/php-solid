<?php
declare(strict_types=1);

namespace App\Infra;

final class Database
{
    private static ?\PDO $pdo = null;

    public static function pdo(): \PDO
    {
        if (self::$pdo) return self::$pdo;

        $cfg = require dirname(__DIR__, 2) . '/config/db.php';

        $dsn = sprintf(
            "mysql:host=%s;port=%d;dbname=%s;charset=%s",
            $cfg['host'],
            $cfg['port'],
            $cfg['database'],
            $cfg['charset']
        );

        self::$pdo = new \PDO($dsn, $cfg['username'], $cfg['password'], $cfg['options'] ?? []);
        return self::$pdo;
    }
}
