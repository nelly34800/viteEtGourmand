<?php

namespace App\Entity;

/**
 * Représente l'entité PasswordReset correspondant
 * à la table `Password_reset` en base de données.
 */
class PasswordReset
{

    private ?string $id;
    private string $token;
    private string $expiresAt;
    private string $idUser;


    public function __construct(
        ?string $id,
        string $token,
        string $expiresAt,
        string $idUser
    ) {
        $this->id = $id;
        $this->token = $token;
        $this->expiresAt = $expiresAt;
        $this->idUser = $idUser;
    }
    // Getters: permet d'accéder aux propriétés privées de l'objet
    public function getId(): string { return $this->id; }
    public function getToken(): string { return $this->token; }
    public function getExpiresAt(): string { return $this->expiresAt; }
    public function getIdUser(): string { return $this->idUser; }
}