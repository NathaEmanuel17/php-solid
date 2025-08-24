<?php
declare(strict_types=1);

namespace App\Domain\Model;

/** @psalm-type CartTotals = array{total_items:int,total_cents:int,total:float,cart_discount:float|null} */
final class CartModel
{
    /** @param CartItemModel[] $items */
    public function __construct(
        public string $id,
        public array $items = [],
        public ?float $cartDiscount = null
    ) {}

    /** @return CartItemModel[] */
    public function items(): array { return $this->items; }

    public function totalItems(): int
    {
        return array_sum(array_map(fn(CartItemModel $i) => $i->qty, $this->items));
    }

    public function totalCents(): int
    {
        $sum = 0;
        foreach ($this->items as $i) $sum += $i->subtotalCentsWithTax();
        if ($this->cartDiscount !== null && $this->cartDiscount > 0) {
            $sum = (int) round($sum * (1 - $this->cartDiscount));
        }
        return $sum;
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'cart_discount' => $this->cartDiscount,
            'total_items' => $this->totalItems(),
            'total_cents' => $this->totalCents(),
            'total' => $this->totalCents() / 100,
            'items' => array_map(fn(CartItemModel $i) => $i->toArray(), $this->items),
        ];
    }
}
