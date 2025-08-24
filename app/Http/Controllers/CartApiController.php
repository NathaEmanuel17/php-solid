<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Cart\Cart;
use App\Cart\CartItem;
use App\Domain\Product\Food;
use App\Domain\Product\Beverage;
use App\Domain\Value\Money;
use App\Service\CheckoutService;

final class CartApiController extends BaseController
{
    public function show(): void
    {
        // monta um carrinho “demo”
        $cart = new Cart();
        $cart->add(new CartItem(new Food('Pão de queijo', Money::fromFloat(6.00)), 2));            // comida 5%
        $cart->add(new CartItem(new Beverage('Café expresso', Money::fromFloat(8.00), false), 1)); // bebida 10%
        $cart->add(new CartItem(new Beverage('Latte gelado', Money::fromFloat(12.00), true), 1, 0.15)); // 15% off
        $cart->applyCartDiscount(0.05); // 5% off no carrinho

        $service = new CheckoutService();

        $this->json([
            'cart'    => $service->toArray($cart),
            'receipt' => $service->receiptText($cart),
        ]);
    }
}
