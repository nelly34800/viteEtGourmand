<?php

namespace App\Middleware;

use App\Helper\ResponseHelper;

class AuthMiddleware
{
    public static function requireAuth(): void
    {
        if (!isset($_SESSION['user'])) {
            ResponseHelper::json(['error' => 'Non authentifié'], 401);
        }
    }

    public static function requireRole(array $roles): void
    {
        self::requireAuth(); // ← on appelle l’auth ici

        $userRole = $_SESSION['user']['role'];

        if (!in_array($userRole, $roles)) {
            ResponseHelper::json(['error' => 'Accès refusé'], 403);
        }
    }
}