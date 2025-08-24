<?php
declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\CartAggregate;
use App\Cart\CartItem;

interface CartRepository
{
    public function createCart(?float $cartDiscount = null): string;
    public function ensureCart(string $cartId): bool;
    public function setCartDiscount(string $cartId, ?float $percent): void;

    public function load(string $cartId): CartAggregate;

    public function addItemFromDomain(string $cartId, CartItem $item): int;
    public function updateItem(string $cartId, int $itemId, ?int $qty = null, ?float $discount = null): bool;
    public function removeItem(string $cartId, int $itemId): bool;
    public function clear(string $cartId): void;
}
