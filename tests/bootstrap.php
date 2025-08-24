<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$root = dirname(__DIR__);

// Carrega SEMPRE o .env "normal" (como você pediu)
Dotenv\Dotenv::createImmutable($root)->safeLoad();

// Força ambiente de teste, mas mantém as mesmas credenciais do .env
$_ENV['APP_ENV'] = 'testing';
$_SERVER['APP_ENV'] = 'testing';

// Sobe migrations sem transação (MySQL dá commit implícito em DDL)
try {
    if (class_exists(\App\Infra\Database::class)) {
        $pdo = \App\Infra\Database::pdo();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'mysql') {
            try { $pdo->exec('SET NAMES utf8mb4'); } catch (Throwable $e) {}
            try { $pdo->exec('SET FOREIGN_KEY_CHECKS=0'); } catch (Throwable $e) {}
        }

        $migrationsDir = $root . '/database/migrations';
        if (is_dir($migrationsDir)) {
            $files = glob($migrationsDir . '/*.sql') ?: [];
            sort($files, SORT_NATURAL);
            foreach ($files as $file) {
                $sql = file_get_contents($file);
                if ($sql !== false && trim($sql) !== '') {
                    $pdo->exec($sql);
                }
            }
        }

        if ($driver === 'mysql') {
            try { $pdo->exec('SET FOREIGN_KEY_CHECKS=1'); } catch (Throwable $e) {}
        }
    }
} catch (Throwable $e) {
    fwrite(STDERR, 'Bootstrap error while preparing DB: ' . $e->getMessage() . PHP_EOL);
    throw $e;
}
