<?php

namespace App\Entity;

/**
 * Représente l'entité User correspondant
 * à la table `user` en base de données.
 */
class User
{
    private ?string $id;
    private string $lastName;
    private string $firstName;
    private string $email;
    private string $password;
    private string $postalAddress;
    private string $city;
    private string $postalCode;
    private string $phone;
    private string $idRole;
    private ?string $roleName;

    public function __construct(
        ?string $id,
        string $lastName,
        string $firstName,
        string $email,
        string $password,
        string $postalAddress,
        string $city,
        string $postalCode,
        string $phone,
        string $idRole,
        ?string $roleName = null
    ) {
        $this->id = $id;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->email = $email;
        $this->password = $password;
        $this->postalAddress = $postalAddress;
        $this->city = $city;
        $this->postalCode = $postalCode;
        $this->phone = $phone;
        $this->idRole = $idRole;
        $this->roleName = $roleName;
    }

    // Getters: permet d'accéder aux propriétés privées de l'objet
    public function getId(): string { return $this->id; }
    public function getLastName(): string { return $this->lastName; }
    public function getFirstName(): string { return $this->firstName; }
    public function getEmail(): string { return $this->email; }
    public function getPassword(): string { return $this->password; }
    public function getPostalAddress(): string { return $this->postalAddress; }
    public function getCity(): string { return $this->city; }
    public function getPostalCode(): string { return $this->postalCode; }
    public function getPhone(): string { return $this->phone; }
    public function getIdRole(): string { return $this->idRole; }
    public function getRoleName(): string { return $this->roleName; }

    //setters: permet de modifier l'id de l'objet généré par la bdd (après sa création)
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->password);
    }
}