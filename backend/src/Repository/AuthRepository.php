<?php

namespace App\Repository;

use PDO;
use RuntimeException;

/**
 * Repository responsable de l'accès aux données
 * pour l'authentification des utilisateurs.
 */
class AuthRepository
{
    private PDO $pdo;

    /**
     * Injection de la dépendance PDO.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
}
