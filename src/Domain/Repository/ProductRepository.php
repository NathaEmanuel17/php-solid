<?php
declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\ProductModel;

interface ProductRepository
{
    /** @return ProductModel[] */
    public function all(): array;
    public function find(string $id): ?ProductModel;
    public function create(ProductModel $product): ProductModel;
}
