<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Repository\CartRepository;
use App\Domain\Value\Money;
use App\Domain\Product\Food;
use App\Domain\Product\Beverage;
use App\Cart\CartItem;

final class CartApiController extends BaseController
{
    public function __construct(private CartRepository $repo)
    {

    }

    private function ensureCartId(): string
    {
        $cartId = $_GET['cart_id'] ?? $_SERVER['HTTP_X_CART_ID'] ?? null;
        if (is_string($cartId) && preg_match('/^[a-f0-9]{32}$/', $cartId)) {
            if ($this->repo->ensureCart($cartId)) return $cartId;
        }
        $newId = $this->repo->createCart(null);
        header('X-Cart-Id: ' . $newId);
        return $newId;
    }

    public function show(): void
    {
        $cartId = $this->ensureCartId();
        $agg = $this->repo->load($cartId);
        $this->json([
            'cart_id' => $agg->id,
            'discount'=> $agg->discount,
            'cart'    => $agg->cart->toArray(),
        ]);
    }

    public function add(): void
    {
        $cartId = $this->ensureCartId();
        $data = $this->readJson();

        $name     = trim((string)($data['name'] ?? ''));
        $category = (string)($data['category'] ?? '');
        $price    = (float)($data['price'] ?? 0);
        $qty      = (int)($data['qty'] ?? 1);
        $isIced   = (bool)($data['is_iced'] ?? false);
        $discount = (float)($data['discount'] ?? 0);

        if ($name === '' || $price <= 0 || !in_array($category, ['food','beverage'], true) || $qty <= 0) {
            $this->json(['error' => 'Dados inválidos'], 422);
            return;
        }

        $product = $category === 'food'
            ? new Food($name, Money::fromFloat($price))
            : new Beverage($name, Money::fromFloat($price), $isIced);

        $item = new CartItem($product, $qty, $discount);
        $itemId = $this->repo->addItemFromDomain($cartId, $item);

        header('X-Cart-Id: ' . $cartId);
        $agg = $this->repo->load($cartId);
        $this->json(['message' => 'Item adicionado', 'item_id' => $itemId, 'cart' => $agg->cart->toArray()], 201);
    }

    public function updateItem(string $id): void
    {
        $cartId = $this->ensureCartId();
        $data = $this->readJson();
        $qty      = array_key_exists('qty', $data) ? (int)$data['qty'] : null;
        $discount = array_key_exists('discount', $data) ? (float)$data['discount'] : null;

        if ($qty !== null && $qty <= 0) { $this->json(['error' => 'qty inválido'], 422); return; }
        if ($discount !== null && ($discount < 0 || $discount > 1)) { $this->json(['error' => 'discount inválido'], 422); return; }

        $ok = $this->repo->updateItem($cartId, (int)$id, $qty, $discount);
        if (!$ok) { $this->json(['error' => 'Item não encontrado'], 404); return; }

        header('X-Cart-Id: ' . $cartId);
        $agg = $this->repo->load($cartId);
        $this->json(['message' => 'Item atualizado', 'cart' => $agg->cart->toArray()]);
    }

    public function removeItem(string $id): void
    {
        $cartId = $this->ensureCartId();
        if (!$this->repo->removeItem($cartId, (int)$id)) {
            $this->json(['error' => 'Item não encontrado'], 404);
            return;
        }
        header('X-Cart-Id: ' . $cartId);
        $agg = $this->repo->load($cartId);
        $this->json(['message' => 'Item removido', 'cart' => $agg->cart->toArray()]);
    }

    public function clear(): void
    {
        $cartId = $this->ensureCartId();
        $this->repo->clear($cartId);
        header('X-Cart-Id: ' . $cartId);
        $agg = $this->repo->load($cartId);
        $this->json(['message' => 'Carrinho limpo', 'cart' => $agg->cart->toArray()]);
    }

    public function setDiscount(): void
    {
        $cartId = $this->ensureCartId();
        $p = (float)($this->readJson()['percent'] ?? 0);
        if ($p < 0 || $p > 1) { $this->json(['error' => 'percent inválido'], 422); return; }
        $this->repo->setCartDiscount($cartId, $p);
        header('X-Cart-Id: ' . $cartId);
        $agg = $this->repo->load($cartId);
        $this->json(['message' => 'Desconto aplicado', 'cart' => $agg->cart->toArray()]);
    }

    private function readJson(): array
    {
        $raw = file_get_contents('php://input') ?: '';
        $data = json_decode($raw, true);
        return (is_array($data) && json_last_error() === JSON_ERROR_NONE) ? $data : [];
    }
}
