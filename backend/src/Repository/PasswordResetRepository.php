<?php

namespace App\Repository;

use PDO;
use App\Entity\PasswordReset;
/**
 * Repository responsable de la modification du mot de passe 
 */
class PasswordResetRepository
{
    /**
     * Injection de la dépendance PDO. 
     */
    public function __construct(private PDO $pdo) {}
    // création du token temporaire
    public function create(PasswordReset $passwordReset): bool
    {
        // insertion du token
        $stmt = $this->pdo->prepare("
            INSERT INTO `password_reset` 
            (id, token, expires_at, id_user)
            VALUES (:id, :token, :expires_at, :id_user)
        ");

        return $stmt->execute([
            ':id' => $passwordReset->getId(),
            ':token' => $passwordReset->getToken(),
            ':expires_at' => $passwordReset->getExpiresAt(),
            ':id_user' => $passwordReset->getIdUser(),
        ]);
    }
    // vérification si token est valide
    public function findValidToken(string $token): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT id_user, token, expires_at 
              FROM password_reset WHERE token = :token 
              AND expires_at > NOW()
        ");
        $stmt->execute([':token' => $token]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;

    }

    public function deleteToken(string $token): void
    {
      //supprime le token 
        $stmt = $this->pdo->prepare("
        DELETE FROM `password_reset` WHERE token = :token");
        $stmt->execute([':token' => $token]);
    }
}