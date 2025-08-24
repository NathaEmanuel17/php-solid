<?php
declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Value\Money;

interface Priceable
{
    public function price(): Money;
}
