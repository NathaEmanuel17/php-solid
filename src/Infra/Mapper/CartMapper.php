<?php
declare(strict_types=1);

namespace App\Infra\Mapper;

use App\Cart\Cart;
use App\Cart\CartItem;
use App\Domain\Product\Food;
use App\Domain\Product\Beverage;
use App\Domain\Value\Money;

final class CartMapper
{
    /**
     * @param array<int,array<string,mixed>> $rows
     */
    public static function toDomain(array $rows, ?float $cartDiscount): Cart
    {
        $cart = new Cart();
        foreach ($rows as $r) {
            $price = Money::fromFloat(((int)$r['price_cents']) / 100);
            $product = ($r['category'] === 'food')
                ? new Food((string)$r['name'], $price)
                : new Beverage((string)$r['name'], $price, (bool)$r['is_iced']);

            $cart->add(new CartItem(
                priceable: $product,
                quantity: (int)$r['qty'],
                discountPercent: $r['discount_percent'] !== null ? (float)$r['discount_percent'] : null
            ));
        }
        if ($cartDiscount !== null) {
            $cart->applyCartDiscount($cartDiscount);
        }
        return $cart;
    }

    /**
     * Extrai dados de um CartItem (domínio) para persistência em DB.
     * @return array{name:string,category:string,price_cents:int,is_iced:int,qty:int,discount:float}
     */
    public static function fromDomainItem(CartItem $item): array
    {
        $p = $item->priceable();
        $qty = $item->quantity();
        $disc = $item->discountPercent() ?? 0.0;

        $name = method_exists($p, 'name') ? $p->name() : (new \ReflectionClass($p))->getShortName();
        $isIced = method_exists($p, 'isIced') ? (bool)$p->isIced() : false;
        $category = str_contains(strtolower($p::class), 'beverage') ? 'beverage' : 'food';

        // ← AQUI o ajuste: sem toCents(), usamos toFloat()*100
        $unit = method_exists($p, 'price') ? $p->price() : Money::fromFloat(0);
        $priceCents = (int) round($unit->toFloat() * 100);

        return [
            'name' => $name,
            'category' => $category,
            'price_cents' => $priceCents,
            'is_iced' => $isIced ? 1 : 0,
            'qty' => $qty,
            'discount' => $disc,
        ];
    }
}
