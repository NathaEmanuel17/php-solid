<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$root = dirname(__DIR__);

// ==== .env ====
$dotenv = Dotenv\Dotenv::createImmutable($root);
$dotenv->load(); // exige que .env exista
// se quiser garantir variáveis obrigatórias, descomente:
// $dotenv->required(['DB_HOST','DB_DATABASE','DB_USERNAME','DB_PASSWORD']);

// ==== Debug mode ====
$debug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('display_startup_errors', $debug ? '1' : '0');
error_reporting($debug ? E_ALL : E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

// ==== Helpers ==== //

/**
 * Responde HTML/texto.
 */
function respond(
    string $body,
    int $status = 200,
    array $headers = ['Content-Type' => 'text/html; charset=utf-8']
): void {
    if (!headers_sent()) {
        http_response_code($status);
        foreach ($headers as $k => $v) {
            header($k . ': ' . $v);
        }
    }
    echo $body;
}

/**
 * Responde JSON.
 */
function json_response(
    array $data,
    int $status = 200,
    int $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
): void {
    $pretty = ($GLOBALS['debug'] ?? false) ? JSON_PRETTY_PRINT : 0;
    $body = json_encode($data, $flags | $pretty);
    respond($body === false ? '{}' : $body, $status, [
        'Content-Type' => 'application/json; charset=utf-8'
    ]);
}

/**
 * Carrega todos os arquivos em routes/*.php e registra no RouteCollector.
 */
function registerRoutes(\FastRoute\RouteCollector $r): void
{
    $dir = dirname(__DIR__) . '/routes';
    if (!is_dir($dir)) {
        return; // sem rotas
    }
    foreach (glob($dir . '/*.php') as $file) {
        $fn = require $file; // cada arquivo retorna uma closure
        if (is_callable($fn)) {
            $fn($r);
        }
    }
}

$container = new \App\Infra\Container();

function container(): \App\Infra\Container {
    /** @var \App\Infra\Container $container */
    global $container;
    return $container;
}

