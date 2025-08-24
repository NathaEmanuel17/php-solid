<?php
declare(strict_types=1);

namespace App\Http\Controllers;

final class GreetingController extends BaseController
{
    public function show(string $nome): void
    {
        $nome = htmlspecialchars($nome, ENT_QUOTES, 'UTF-8');
        $this->view('Saudação', "<h1>Olá, {$nome} 👋</h1>");
    }
}
