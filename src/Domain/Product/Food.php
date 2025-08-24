<?php
declare(strict_types=1);

namespace App\Domain\Product;

use App\Domain\Enum\Category;
use App\Domain\Value\Money;

final class Food extends Product
{
    public function __construct(string $name, Money $basePrice)
    {
        parent::__construct($name, $basePrice, Category::FOOD);
    }

    public function taxRate(): float
    {
        return 0.05;
    }
}
