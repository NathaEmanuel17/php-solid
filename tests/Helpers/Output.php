<?php
declare(strict_types=1);

namespace Tests\Helpers;

final class Output
{
    /** @return array{status:int, json:array<string,mixed>|array<int,mixed>|null, raw:string} */
    public static function capture(callable $fn): array
    {
        // captura status code via http_response_code()
        $statusBefore = http_response_code();
        ob_start();
        try {
            $fn();
        } finally {
            $raw = ob_get_clean();
        }
        $statusAfter = http_response_code();
        $status = is_int($statusAfter) ? $statusAfter : (is_int($statusBefore) ? $statusBefore : 200);
        $json = null;
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $json = $decoded;
            }
        }
        return ['status' => $status, 'json' => $json, 'raw' => $raw ?? ''];
    }
}
