<?php

namespace App\Entity;

/**
 * Représente l'entité Dish correspondant
 * à la table `dish` en base de données.
 */
class Dish
{
    private string $id;
    private string $dishTitle;
    private string $description;
    private string $picture;
    private string $idCategoryDish;
    private ?string $categoryName;
    private array $diets;
    private array $allergens;

    public function __construct(
        string $id,
        string $dishTitle,
        string $description,
        string $picture,
        string $idCategoryDish,
        ?string $categoryName = null,
        array $diets = [],
        array $allergens = []
    ) {
        $this->id = $id;
        $this->dishTitle = $dishTitle;
        $this->description = $description;
        $this->picture = $picture;
        $this->idCategoryDish = $idCategoryDish;
        $this->categoryName = $categoryName;
        $this->diets = $diets;
        $this->allergens = $allergens;
    }
    // Getters: permet d'accéder aux propriétés privées de l'objet
    public function getId(): string { return $this->id; }
    public function getDishTitle(): string { return $this->dishTitle; }
    public function getDescription(): string { return $this->description; }
    public function getPicture(): string { return $this->picture; }
    public function getIdCategoryDish(): string { return $this->idCategoryDish; }
    public function getCategoryName(): ?string { return $this->categoryName; }
    public function getDiets(): array { return $this->diets; }
    public function getAllergens(): array { return $this->allergens; }

    //setters: permet de modifier l'id de l'objet généré par la bdd (après sa création)
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function addDiet(array $diet): void
    {
        if (!in_array($diet, $this->diets, true)) {
            $this->diets[] = $diet;
        }
    }

    public function addAllergen(array $allergen): void
    {
        if (!in_array($allergen, $this->allergens, true)) {
            $this->allergens[] = $allergen;
        }
    }
}