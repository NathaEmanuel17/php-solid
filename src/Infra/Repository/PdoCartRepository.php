<?php
declare(strict_types=1);

namespace App\Infra\Repository;

use App\Infra\Database;
use App\Infra\Mapper\CartMapper;
use App\Domain\Model\CartAggregate;
use App\Cart\CartItem;
use App\Domain\Repository\CartRepository;

final class PdoCartRepository implements CartRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::pdo();
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }

    public function createCart(?float $cartDiscount = null): string
    {
        $id = bin2hex(random_bytes(16));
        $this->pdo->prepare('INSERT INTO carts (id, cart_discount_percent) VALUES (:id, :d)')
            ->execute(['id' => $id, 'd' => $cartDiscount]);
        return $id;
    }

    public function ensureCart(string $cartId): bool
    {
        $st = $this->pdo->prepare('SELECT 1 FROM carts WHERE id=:id');
        $st->execute(['id' => $cartId]);
        return (bool)$st->fetchColumn();
    }

    public function setCartDiscount(string $cartId, ?float $percent): void
    {
        $this->pdo->prepare('UPDATE carts SET cart_discount_percent=:p WHERE id=:id')
            ->execute(['p' => $percent, 'id' => $cartId]);
    }

    public function load(string $cartId): CartAggregate
    {
        $c = $this->pdo->prepare('SELECT id, cart_discount_percent FROM carts WHERE id=:id');
        $c->execute(['id' => $cartId]);
        $row = $c->fetch();
        if (!$row) {
            $cartId = $this->createCart(null);
            $row = ['id' => $cartId, 'cart_discount_percent' => null];
        }

        $it = $this->pdo->prepare('SELECT id, cart_id, name, category, price_cents, is_iced, qty, discount_percent FROM cart_items WHERE cart_id=:id ORDER BY id');
        $it->execute(['id' => $row['id']]);
        $rows = $it->fetchAll() ?: [];

        $cart = CartMapper::toDomain($rows, $row['cart_discount_percent'] !== null ? (float)$row['cart_discount_percent'] : null);

        return new CartAggregate(
            id: (string)$row['id'],
            cart: $cart,
            discount: $row['cart_discount_percent'] !== null ? (float)$row['cart_discount_percent'] : null
        );
    }

    public function addItemFromDomain(string $cartId, CartItem $item): int
    {
        $data = CartMapper::fromDomainItem($item);
        $sql = 'INSERT INTO cart_items (cart_id, name, category, price_cents, is_iced, qty, discount_percent)
                VALUES (:cart_id, :name, :category, :price_cents, :is_iced, :qty, :discount)';
        $st = $this->pdo->prepare($sql);
        $st->execute(['cart_id' => $cartId] + $data);
        return (int)$this->pdo->lastInsertId();
    }

    public function updateItem(string $cartId, int $itemId, ?int $qty = null, ?float $discount = null): bool
    {
        $sets = [];
        $params = ['id' => $itemId, 'cart_id' => $cartId];
        if ($qty !== null)      { $sets[] = 'qty=:qty'; $params['qty'] = $qty; }
        if ($discount !== null) { $sets[] = 'discount_percent=:disc'; $params['disc'] = $discount; }
        if (!$sets) return false;
        $st = $this->pdo->prepare('UPDATE cart_items SET '.implode(',', $sets).' WHERE id=:id AND cart_id=:cart_id');
        $st->execute($params);
        return $st->rowCount() > 0;
    }

    public function removeItem(string $cartId, int $itemId): bool
    {
        $st = $this->pdo->prepare('DELETE FROM cart_items WHERE id=:id AND cart_id=:cart_id');
        $st->execute(['id' => $itemId, 'cart_id' => $cartId]);
        return $st->rowCount() > 0;
    }

    public function clear(string $cartId): void
    {
        $this->pdo->prepare('DELETE FROM cart_items WHERE cart_id=:id')->execute(['id' => $cartId]);
    }
}
