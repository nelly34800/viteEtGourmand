<?php

namespace App\Helper;

use RuntimeException;

class CsrfHelper
{
    public static function generate(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public static function validate(): void
    {
        $headers = getallheaders();

        if (
            !isset($headers['X-CSRF-Token']) ||
            $headers['X-CSRF-Token'] !== ($_SESSION['csrf_token'] ?? null)
        ) {
            throw new RuntimeException('Invalid CSRF token');
        }
    }
}