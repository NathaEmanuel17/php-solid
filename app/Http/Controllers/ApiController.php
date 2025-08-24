<?php
declare(strict_types=1);

namespace App\Http\Controllers;

final class ApiController extends BaseController
{
    public function ping(): void
    {
        $this->json([
            'ok' => true,
            'app' => $_ENV['APP_NAME'] ?? 'Minha App',
            'time' => (new \DateTimeImmutable())->format(DATE_ATOM),
        ]);
    }
}
