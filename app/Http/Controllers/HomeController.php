<?php
declare(strict_types=1);

namespace App\Http\Controllers;

final class HomeController extends BaseController
{
    public function index(): void
    {
        $app = htmlspecialchars($_ENV['APP_NAME'] ?? 'Minha App', ENT_QUOTES, 'UTF-8');
        $env = htmlspecialchars($_ENV['APP_ENV'] ?? 'local', ENT_QUOTES, 'UTF-8');

        $this->view($app, <<<HTML
            <h1>{$app}</h1>
            <p>Ambiente: <strong>{$env}</strong></p>
            <p>🎉 Roteador FastRoute ativo.</p>
            <p>Tente: <a href="/saudacao/Natha">/saudacao/Natha</a> ou <a href="/api/ping">/api/ping</a></p>
        HTML);
    }
}
