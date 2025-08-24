<?php
declare(strict_types=1);

namespace App\Domain\Product;

use App\Domain\Enum\Category;
use App\Domain\Value\Money;

final class Beverage extends Product
{
    public function __construct(
        string $name,
        Money $basePrice,
        private bool $isIced = false
    ) {
        parent::__construct($name, $basePrice, Category::BEVERAGE);
    }

    public function isIced(): bool
    {
        return $this->isIced;
    }

    public function taxRate(): float
    {
        return 0.10;
    }

    public function price(): Money
    {
        $price = parent::price();
        if ($this->isIced) {
            $price = $price->add(Money::fromFloat(1.00));
        }
        return $price;
    }
}
