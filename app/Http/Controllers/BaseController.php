<?php
declare(strict_types=1);

namespace App\Http\Controllers;

abstract class BaseController
{
    protected function json(array $data, int $status = 200): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    protected function html(string $content, int $status = 200): void
    {
        header('Content-Type: text/html; charset=utf-8');
        http_response_code($status);
        echo $content;
    }

    protected function view(string $title, string $body): void
    {
        $html = <<<HTML
        <!doctype html>
        <html lang="pt-BR"><head>
          <meta charset="UTF-8"><title>{$title}</title>
          <meta name="viewport" content="width=device-width, initial-scale=1">
        </head><body>
          {$body}
        </body></html>
        HTML;
        $this->html($html);
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $_POST[$key] ?? $default;
    }

    protected function redirect(string $url, int $status = 302): void
    {
        http_response_code($status);
        header("Location: {$url}");
    }
}
