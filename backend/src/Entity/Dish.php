<?php

namespace App\Entity;

/**
 * Représente l'entité Dish correspondant
 * à la table `dish` en base de données.
 */
class Dish
{
    private ?string $id;
    private string $dishTitle;
    private string $description;
    private string $picture;
    private string $idCategoryDish;
    private ?string $categoryName;

    public function __construct(
        string $dishTitle,
        string $description,
        string $picture,
        string $idCategoryDish,
        ?string $categoryName = null,
        ?string $id = null
    ) {

        $this->id = $id;
        $this->dishTitle = $dishTitle;
        $this->description = $description;
        $this->picture = $picture;
        $this->idCategoryDish = $idCategoryDish;
        $this->categoryName = $categoryName;
    }

    // Getters: permet d'accéder aux propriétés privées de l'objet
    public function getId(): ?string { return $this->id; }
    public function getDishTitle(): string { return $this->dishTitle; }
    public function getDescription(): string { return $this->description; }
    public function getPicture(): string { return $this->picture; }
    public function getIdCategoryDish(): string { return $this->idCategoryDish; }
    public function getCategoryName(): string { return $this->categoryName; }

     //setters: permet de modifier l'id de l'objet généré par la bdd (après sa création)
    public function setId(string $id): void
    {
        $this->id = $id;
    }
}