<?php

namespace App\Entity;
/**
 * Représente l'entité Allergen correspondant
 * à la table `allergen` en base de données.
 */
class Allergen
{
    private ?string $id;
    private string $allergenName;

    public function __construct(
        ?string $id,
        string $allergenName

    ) {

        $this->id = $id;
        $this->allergenName = $allergenName;
    }

    // Getters: permet d'accéder aux propriétés privées de l'objet
    public function getId(): string { return $this->id; }
    public function getAllergenName(): string { return $this->allergenName; }

     //setters: permet de modifier l'id de l'objet généré par la bdd (après sa création)
    public function setId(string $id): void
    {
        $this->id = $id;
    }
}