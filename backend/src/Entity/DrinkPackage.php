<?php

namespace App\Entity;
/**
 * Représente l'entité DrinkPackage correspondant
 * à la table `drink_package` en base de données.
 */
class DrinkPackage
{
    private ?string $id;
    private string $drinkPackageName;
    private float $pricePerPerson;

    public function __construct(
        ?string $id,
        string $drinkPackageName,
        float $pricePerPerson,
    ) {

        $this->id = $id;
        $this->drinkPackageName = $drinkPackageName;
        $this->pricePerPerson = $pricePerPerson;
    }

    // Getters: permet d'accéder aux propriétés privées de l'objet
    public function getId(): string { return $this->id; }
    public function getDrinkPackageName(): string { return $this->drinkPackageName; }
    public function getPricePerPerson(): int { return $this->pricePerPerson; }
    
     //setters: permet de modifier l'id de l'objet généré par la bdd (après sa création)
    public function setId(string $id): void
    {
        $this->id = $id;
    }
}