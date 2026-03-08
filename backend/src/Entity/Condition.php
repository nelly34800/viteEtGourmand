<?php

namespace App\Entity;
/**
 * Représente l'entité Condition correspondant
 * à la table `condition_menu` en base de données.
 */
class Condition
{
    private ?string $id;
    private string $conditionType;
    private string $description;

    public function __construct(
            ?string $id,
            string $conditionType,
            string $description,
    ) {
        $this->id = $id;
        $this->conditionType = $conditionType;
        $this->description = $description;
    }
    // Getters: permet d'accéder aux propriétés privées de l'objet
    public function getId(): string { return $this->id; }
    public function getConditionType(): string { return $this->conditionType; }
    public function getDescription(): string { return $this->description; }

    //setters: permet de modifier l'id de l'objet généré par la bdd (après sa création)
    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
