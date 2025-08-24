<?php
declare(strict_types=1);

use FastRoute\RouteCollector;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GreetingController;

/** Registra rotas web. */
return static function (RouteCollector $r): void {
    $r->addRoute('GET', '/', [HomeController::class, 'index']);
    $r->addRoute('GET', '/home', [HomeController::class, 'index']);
    $r->addRoute('GET', '/saudacao/{nome}', [GreetingController::class, 'show']);
};
