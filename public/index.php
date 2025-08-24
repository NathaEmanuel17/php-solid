<?php
declare(strict_types=1);

require __DIR__ . '/../bootstrap/app.php';

use FastRoute\RouteCollector;

// timezone da app (vem do config/app.php, se existir)
$config = is_file(__DIR__ . '/../config/app.php')
    ? require __DIR__ . '/../config/app.php'
    : ['tz' => 'UTC'];
date_default_timezone_set($config['tz'] ?? 'UTC');

// cria dispatcher
$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
    registerRoutes($r); // carrega rotas de routes/*.php
});

// método/URI da request
$httpMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// (opcional) CORS + preflight OPTIONS
/*
if ($httpMethod === 'OPTIONS') {
    respond('', 204, [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET,POST,PUT,PATCH,DELETE,OPTIONS',
        'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
    ]);
    exit;
}
*/

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        respond('<h1>404</h1><p>Rota não encontrada.</p>', 404);
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        respond('<h1>405</h1><p>Método não permitido.</p>', 405);
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars    = $routeInfo[2];

        if (is_array($handler) && isset($handler[0], $handler[1])) {
            [$class, $method] = $handler;
            (new $class())->{$method}(...array_values($vars));
            break;
        }

        if (is_callable($handler)) {
            $handler($vars);
            break;
        }

        respond('<h1>500</h1><p>Handler de rota inválido.</p>', 500);
        break;
}
