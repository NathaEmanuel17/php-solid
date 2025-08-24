<?php
declare(strict_types=1);

namespace App\Domain\Value;

final class Money
{
    public function __construct(private int $amountInCents) {}

    public static function fromFloat(float $value): self
    {
        return new self((int) round($value * 100));
    }

    public function add(self $other): self
    {
        return new self($this->amountInCents + $other->amountInCents);
    }

    public function subtract(self $other): self
    {
        return new self($this->amountInCents - $other->amountInCents);
    }

    public function multiply(float $factor): self
    {
        return new self((int) round($this->amountInCents * $factor));
    }

    public function toFloat(): float
    {
        return $this->amountInCents / 100;
    }

    public function __toString(): string
    {
        return number_format($this->toFloat(), 2, ',', '.');
    }
}
