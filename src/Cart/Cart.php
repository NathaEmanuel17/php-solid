<?php
declare(strict_types=1);

namespace App\Cart;

use App\Domain\Value\Money;

final class Cart
{
    /** @var CartItem[] */
    private array $items = [];
    private ?float $cartDiscountPercent = null;

    public function add(CartItem $item): void
    {
        $this->items[] = $item;
    }

    public function applyCartDiscount(float $percent): void
    {
        if ($percent < 0 || $percent > 1) throw new \DomainException('Desconto inválido');
        $this->cartDiscountPercent = $percent;
    }

    /** @return CartItem[] */
    public function items(): array
    {
        return $this->items;
    }

    public function total(): Money
    {
        $sum = new Money(0);
        foreach ($this->items as $item) {
            $sum = $sum->add($item->subtotal());
        }
        if ($this->cartDiscountPercent !== null) {
            $sum = $sum->multiply(1 - $this->cartDiscountPercent);
        }
        return $sum;
    }

    public function toArray(): array
    {
        return [
            'items' => array_map(fn (CartItem $i) => $i->toArray(), $this->items),
            'total' => $this->total()->toFloat(),
        ];
    }
}
