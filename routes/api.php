<?php
declare(strict_types=1);

use FastRoute\RouteCollector;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\CartApiController;

/** Registra rotas de API. */
return static function (RouteCollector $r): void {
    $r->addRoute('GET', '/api/ping', [ApiController::class, 'ping']);
    $r->addRoute('GET', '/api/carrinho', [CartApiController::class, 'show']);
};
