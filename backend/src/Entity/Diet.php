<?php

namespace App\Entity;

/**
 * Représente l'entité Diet correspondant
 * à la table `diet` en base de données.
 */
class Diet
{
    private ?string $id;
    private string $dietName;

    public function __construct(
        ?string $id,
        string $dietName,
    ) {

        $this->id = $id;
        $this->dietName = $dietName;
    }

    // Getters: permet d'accéder aux propriétés privées de l'objet
    public function getId(): string { return $this->id; }
    public function getDietName(): string { return $this->dietName; }

     //setters: permet de modifier l'id de l'objet généré par la bdd (après sa création)
    public function setId(string $id): void
    {
        $this->id = $id;
    }
}