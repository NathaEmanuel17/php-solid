<?php
declare(strict_types=1);

namespace App\Domain\Product;

use App\Domain\Contract\Priceable;
use App\Domain\Contract\Taxable;
use App\Domain\Enum\Category;
use App\Domain\Trait\HasUuid;
use App\Domain\Value\Money;

abstract class Product implements Priceable, Taxable
{
    use HasUuid;

    public function __construct(
        protected string $name,
        protected Money $basePrice,
        protected Category $category
    ) {
        $this->bootUuid();
    }

    public function name(): string
    {
        return $this->name;
    }

    public function basePrice(): Money
    {
        return $this->basePrice;
    }

    public function category(): Category
    {
        return $this->category;
    }

    public function price(): Money
    {
        $tax = $this->basePrice->multiply($this->taxRate());
        return $this->basePrice->add($tax);
    }
}
