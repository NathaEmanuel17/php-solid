<?php
declare(strict_types=1);

namespace App\Service;

use App\Cart\Cart;

final class CheckoutService
{
    public function receiptText(Cart $cart): string
    {
        $lines = ["===== RECIBO CAFETERIA ====="];
        foreach ($cart->items() as $i) {
            $lines[] = sprintf("%-25s %10s", $i->description(), (string) $i->subtotal());
        }
        $lines[] = str_repeat('-', 40);
        $lines[] = sprintf("%-25s %10s", "TOTAL", (string) $cart->total());
        return implode(PHP_EOL, $lines);
    }

    public function toArray(Cart $cart): array
    {
        return $cart->toArray();
    }
}
