<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Cart\Cart;
use App\Cart\CartItem;
use App\Domain\Product\Food;
use App\Domain\Product\Beverage;
use App\Domain\Value\Money;

final class CartTest extends TestCase
{
    public function testCartTotalsWithItemAndCartDiscount(): void
    {
        $cart = new Cart();
        $cart->add(new CartItem(new Food('Pão de queijo', Money::fromFloat(6.00)), 2, 0.10)); // 10% off nos 2
        $cart->add(new CartItem(new Beverage('Latte', Money::fromFloat(12.00), true), 1));    // sem desconto
        $cart->applyCartDiscount(0.05); // 5% no carrinho

        $arr = $cart->toArray();
        $this->assertArrayHasKey('total', $arr);
        $this->assertIsFloat($arr['total']);
        $this->assertGreaterThan(0, $arr['total']);
    }

    public function testCartItemValidations(): void
    {
        $this->expectException(\DomainException::class);
        new CartItem(new Food('Teste', Money::fromFloat(1.0)), 0);
    }
}
