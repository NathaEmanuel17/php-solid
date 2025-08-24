<?php
declare(strict_types=1);

namespace App\Infra;

use App\Domain\Repository\ProductRepository;
use App\Infra\Repository\PdoProductRepository;
use App\Domain\Repository\CartRepository;
use App\Infra\Repository\PdoCartRepository;

final class Container
{
    private array $singletons = [];

    public function get(string $id): mixed
    {
        return $this->singletons[$id] ??= match ($id) {
            ProductRepository::class => new PdoProductRepository(),
            CartRepository::class    => new PdoCartRepository(),
            default => throw new \RuntimeException("Sem binding para {$id}"),
        };
    }
}
