<?php
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\CartApiController;
use App\Domain\Repository\CartRepository;
use App\Domain\Model\CartAggregate;
use App\Cart\Cart;
use App\Cart\CartItem;
use App\Domain\Product\Food;
use App\Domain\Value\Money;
use Tests\Helpers\Output;

final class CartApiControllerTest extends TestCase
{
    private function fakeRepo(): CartRepository
    {
        return new class implements CartRepository {
            public string $id = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
            public array $added = [];
            public ?float $discount = null;

            public function createCart(?float $cartDiscount = null): string { $this->discount = $cartDiscount; return $this->id; }
            public function ensureCart(string $cartId): bool { return $cartId === $this->id; }
            public function setCartDiscount(string $cartId, ?float $percent): void { $this->discount = $percent; }
            public function load(string $cartId): CartAggregate {
                $cart = new Cart();
                foreach ($this->added as $i) $cart->add($i);
                if ($this->discount !== null) $cart->applyCartDiscount($this->discount);
                return new CartAggregate($this->id, $cart, $this->discount);
            }
            public function addItemFromDomain(string $cartId, CartItem $item): int {
                $this->added[] = $item; return count($this->added);
            }
            public function updateItem(string $cartId, int $itemId, ?int $qty = null, ?float $discount = null): bool { return true; }
            public function removeItem(string $cartId, int $itemId): bool { return true; }
            public function clear(string $cartId): void { $this->added = []; }
        };
    }

    public function testShowCreatesCartWhenMissing(): void
    {
        $_GET = []; unset($_SERVER['HTTP_X_CART_ID']);
        $c = new CartApiController($this->fakeRepo());
        $res = \Tests\Helpers\Output::capture(fn() => $c->show());
        $this->assertSame(200, $res['status']);
        $this->assertArrayHasKey('cart_id', $res['json']);
    }

    public function testAddWithoutBodyReturns422(): void
    {
        $_GET = []; unset($_SERVER['HTTP_X_CART_ID']);
        $c = new CartApiController($this->fakeRepo());
        $res = \Tests\Helpers\Output::capture(fn() => $c->add());
        $this->assertSame(422, $res['status']);
        $this->assertArrayHasKey('error', $res['json']);
    }

    public function testSetDiscountDefaultsToZeroOk(): void
    {
        $_GET = []; unset($_SERVER['HTTP_X_CART_ID']);
        $c = new CartApiController($this->fakeRepo());
        $res = \Tests\Helpers\Output::capture(fn() => $c->setDiscount()); // sem body -> percent = 0 -> válido
        $this->assertSame(200, $res['status']);
        $this->assertArrayHasKey('cart', $res['json']);
    }
}
