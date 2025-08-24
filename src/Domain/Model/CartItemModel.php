<?php
declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Enum\Category;

final class CartItemModel
{
    public function __construct(
        public int $id,
        public string $cartId,
        public string $name,
        public Category $category,
        public int $priceCents,
        public int $qty,
        public ?float $discount = null,
        public bool $isIced = false
    ) {}

    /** @param array<string,mixed> $row */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int)$row['id'],
            cartId: (string)$row['cart_id'],
            name: (string)$row['name'],
            category: Category::from((string)$row['category']),
            priceCents: (int)$row['price_cents'],
            qty: (int)$row['qty'],
            discount: isset($row['discount_percent']) ? (float)$row['discount_percent'] : null,
            isIced: (bool)$row['is_iced']
        );
    }

    public function subtotalCentsWithoutTax(): int
    {
        $line = $this->priceCents * $this->qty;
        if ($this->discount !== null && $this->discount > 0) {
            $line = (int) round($line * (1 - $this->discount));
        }
        return $line;
    }

    public function taxMultiplier(): float
    {
        return $this->category === Category::Food ? 1.05 : 1.10;
    }

    public function subtotalCentsWithTax(): int
    {
        return (int) round($this->subtotalCentsWithoutTax() * $this->taxMultiplier());
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'cart_id' => $this->cartId,
            'name' => $this->name,
            'category' => $this->category->value,
            'price_cents' => $this->priceCents,
            'price' => $this->priceCents / 100,
            'qty' => $this->qty,
            'discount' => $this->discount,
            'is_iced' => $this->isIced,
            'subtotal_cents' => $this->subtotalCentsWithTax(),
            'subtotal' => $this->subtotalCentsWithTax() / 100,
        ];
    }
}
