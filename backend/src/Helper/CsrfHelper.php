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

        // 1. Récupération du header en ignorant la casse (car les serveurs de prod modifient souvent la casse des headers)
        $clientToken = null;
        foreach ($headers as $key => $value) {
            if (strtolower($key) === 'x-csrf-token') {
                $clientToken = $value;
                break;
            }
        }

        $serverToken = $_SESSION['csrf_token'] ?? null;

        // 2. Écriture de logs pour Heroku afin de voir ce qui cloche
        if (!$clientToken) {
            error_log("[CSRF Debug] Échec : Aucun header X-CSRF-Token reçu de la part du client.");
        }
        if (!$serverToken) {
            error_log("[CSRF Debug] Échec : Aucun token CSRF n'existe dans la session PHP ($ _SESSION est vide ou expiré).");
        }
        if ($clientToken && $serverToken && $clientToken !== $serverToken) {
            error_log("[CSRF Debug] Échec : Le token client ($clientToken) ne correspond pas au token serveur ($serverToken).");
        }

        // 3. Validation stricte
        if (!$clientToken || $clientToken !== $serverToken) {
            throw new \RuntimeException('Invalid CSRF token');
        }
    }
}