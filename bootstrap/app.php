<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$root = dirname(__DIR__);

// .env
Dotenv\Dotenv::createImmutable($root)->safeLoad();

function respond(string $body, int $status = 200, array $headers = ['Content-Type' => 'text/html; charset=utf-8']): void {
    http_response_code($status);
    foreach ($headers as $k => $v) header("$k: $v");
    echo $body;
}
/**
 * Carrega todos os arquivos em routes/*.php e registra no RouteCollector.
 */
function registerRoutes(\FastRoute\RouteCollector $r): void
{
    $dir = dirname(__DIR__) . '/routes';
    foreach (glob($dir . '/*.php') as $file) {
        $fn = require $file;        // cada arquivo retorna uma closure
        if (is_callable($fn)) {
            $fn($r);                // chama a closure passando o RouteCollector
        }
    }
}

