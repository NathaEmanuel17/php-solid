<?php
declare(strict_types=1);

namespace App\Domain\Trait;

trait HasUuid
{
    private string $id;

    private function bootUuid(): void
    {
        $this->id = bin2hex(random_bytes(16));
    }

    public function id(): string
    {
        return $this->id;
    }
}
