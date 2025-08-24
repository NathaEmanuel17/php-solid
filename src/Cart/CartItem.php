<?php
declare(strict_types=1);

namespace App\Cart;

use App\Domain\Contract\Priceable;
use App\Domain\Value\Money;

final class CartItem
{
    public function __construct(
        private Priceable $priceable,
        private int $quantity = 1,
        private ?float $discountPercent = null // 0.10 = 10% off
    ) {
        if ($quantity < 1) throw new \DomainException('Quantidade deve ser >= 1');
        if ($discountPercent !== null && ($discountPercent < 0 || $discountPercent > 1)) {
            throw new \DomainException('Desconto deve estar entre 0 e 1');
        }
    }

    public function description(): string
    {
        $desc = method_exists($this->priceable, 'name')
            ? $this->priceable->name()
            : (new \ReflectionClass($this->priceable))->getShortName();

        return "{$desc} x{$this->quantity}";
    }

    public function subtotal(): Money
    {
        $unit = $this->priceable->price();
        $total = $unit->multiply($this->quantity);
        if ($this->discountPercent !== null) {
            $total = $total->multiply(1 - $this->discountPercent);
        }
        return $total;
    }

    public function toArray(): array
    {
        return [
            'description' => $this->description(),
            'subtotal'    => $this->subtotal()->toFloat(),
        ];
    }
}
