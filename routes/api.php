<?php
declare(strict_types=1);

use FastRoute\RouteCollector;
use App\Http\Controllers\ProductApiController;
use App\Domain\Repository\ProductRepository;
use App\Http\Controllers\CartApiController;
use App\Domain\Repository\CartRepository;

return static function (RouteCollector $r): void {
    // Healthcheck
    $r->addRoute('GET', '/api/ping', fn() => json_response(['pong' => true]));

    // Produtos (closures, injetando pelo container)
    $r->addRoute('GET',  '/api/produtos', fn() =>
    (new ProductApiController(container()->get(ProductRepository::class)))->index()
    );

    $r->addRoute('GET',  '/api/produtos/{id}', fn(array $vars) =>
    (new ProductApiController(container()->get(ProductRepository::class)))->show($vars['id'])
    );

    $r->addRoute('POST', '/api/produtos', fn() =>
    (new ProductApiController(container()->get(ProductRepository::class)))->store()
    );

    // Carrinho (closures, injetando pelo container)
    $r->addRoute('GET', '/api/carrinho', fn() =>
    (new CartApiController(container()->get(CartRepository::class)))->show()
    );
    $r->addRoute('POST', '/api/carrinho/add', fn() =>
    (new CartApiController(container()->get(CartRepository::class)))->add()
    );
    $r->addRoute('PUT', '/api/carrinho/item/{id}', fn(array $vars) =>
    (new CartApiController(container()->get(CartRepository::class)))->updateItem($vars['id'])
    );
    $r->addRoute('DELETE', '/api/carrinho/item/{id}', fn(array $vars) =>
    (new CartApiController(container()->get(CartRepository::class)))->removeItem($vars['id'])
    );
    $r->addRoute('DELETE', '/api/carrinho', fn() =>
    (new CartApiController(container()->get(CartRepository::class)))->clear()
    );
    $r->addRoute('POST', '/api/carrinho/discount', fn() =>
    (new CartApiController(container()->get(CartRepository::class)))->setDiscount()
    );
};
