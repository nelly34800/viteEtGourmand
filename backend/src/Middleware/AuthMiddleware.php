<?php

namespace App\Middleware;

class AuthMiddleware
{
    public static function requireRole(array $roles)
    {

        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Non authentifié']);
            exit;
        }

        $userRole = $_SESSION['user']['role'];

        if (!in_array($userRole, $roles)) {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }
    }
}
