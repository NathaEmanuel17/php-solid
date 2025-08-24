<?php
declare(strict_types=1);

require __DIR__ . '/../bootstrap/app.php';

use App\Infra\Database;

$pdo = Database::pdo();
$path = dirname(__DIR__) . '/database';

try {
    // Executa migrations
    foreach (glob($path . '/migrations/*.sql') as $file) {
        $sql = file_get_contents($file);
        echo "Running migration: " . basename($file) . PHP_EOL;
        $pdo->exec($sql);
    }

    // Executa seeders
    foreach (glob($path . '/seeders/*.sql') as $file) {
        $sql = file_get_contents($file);
        echo "Seeding: " . basename($file) . PHP_EOL;
        $pdo->exec($sql);
    }

    echo "✅ Migrations + seeds concluídos com sucesso" . PHP_EOL;

} catch (Throwable $e) {
    fwrite(STDERR, "❌ Erro: {$e->getMessage()}" . PHP_EOL);
    exit(1);
}
