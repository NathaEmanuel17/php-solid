<?php
declare(strict_types=1);

namespace App\Domain\Model;

use App\Cart\Cart;

final class CartAggregate
{
    public function __construct(
        public string $id,
        public Cart $cart,
        public ?float $discount = null
    ) {}
}
