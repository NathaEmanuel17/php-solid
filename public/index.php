<?php
declare(strict_types=1);

require __DIR__ . '/../bootstrap/app.php';
$config = require __DIR__ . '/../config/app.php';
date_default_timezone_set($config['tz']);

use FastRoute\RouteCollector;

$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
    // agora todas as rotas vêm de routes/*.php
    registerRoutes($r);
});

// --- resto (dispatch) permanece igual ---
$httpMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';
if (false !== $pos = strpos($uri, '?')) $uri = substr($uri, 0, $pos);
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        respond('<h1>404</h1><p>Rota não encontrada.</p>', 404);
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        respond('<h1>405</h1><p>Método não permitido.</p>', 405);
        break;

    case FastRoute\Dispatcher::FOUND:
        [$class, $method] = $routeInfo[1];
        $vars = $routeInfo[2];
        (new $class())->{$method}(...array_values($vars));
        break;
}
