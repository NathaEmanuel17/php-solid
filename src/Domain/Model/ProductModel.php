<?php
declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Enum\Category;
use App\Domain\Value\Money;

final class ProductModel
{
    public function __construct(
        public readonly string $id,
        public string $name,
        public Category $category,
        public int $basePriceCents,
        public ?bool $isIced = null, // só aplica para bebidas
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            $row['id'],
            $row['name'],
            Category::from($row['category']),
            (int)$row['base_price_cents'],
            isset($row['is_iced']) ? (bool)$row['is_iced'] : null
        );
    }

    public function priceWithTax(): Money
    {
        $base = new Money($this->basePriceCents);
        $rate = match ($this->category) {
            Category::FOOD => 0.05,
            Category::BEVERAGE => 0.10,
        };
        $price = $base->add($base->multiply($rate));
        if ($this->category === Category::BEVERAGE && $this->isIced) {
            $price = $price->add(Money::fromFloat(1.00));
        }
        return $price;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category->value,
            'base_price' => $this->basePriceCents / 100,
            'is_iced' => $this->isIced,
            'price_with_tax' => $this->priceWithTax()->toFloat(),
        ];
    }
}
