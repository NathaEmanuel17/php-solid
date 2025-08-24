<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Infra\Repository\PdoCartRepository;
use App\Domain\Value\Money;
use App\Domain\Product\Food;
use App\Cart\CartItem;
use App\Infra\Database;

final class PdoCartRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (class_exists(Database::class)) {
            $pdo = Database::pdo();
            $pdo->exec('SET FOREIGN_KEY_CHECKS=0; TRUNCATE cart_items; TRUNCATE carts; SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    public function testCreateLoadAndAddItem(): void
    {
        $repo = new PdoCartRepository();
        $cartId = $repo->createCart(null);

        $itemId = $repo->addItemFromDomain($cartId, new CartItem(
            new Food('Pão', Money::fromFloat(5.50)), 2, 0.10
        ));
        $this->assertGreaterThan(0, $itemId);

        $agg = $repo->load($cartId);
        $this->assertSame($cartId, $agg->id);
        $this->assertNotEmpty($agg->cart->toArray()['items']);
    }
}
