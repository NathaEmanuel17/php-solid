<?php
declare(strict_types=1);

namespace App\Domain\Enum;

enum Category: string
{
    case FOOD = 'food';
    case BEVERAGE = 'beverage';
}