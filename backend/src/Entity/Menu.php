<?php

namespace App\Entity;

/**
 * Représente l'entité Menu correspondant
 * à la table `menu` en base de données.
 */
class Menu
{
    private string $id;
    private string $menuName;
    private string $description;
    private string $illustrationDishId; // stockage BDD
    private int $minimumPeople;
    private float $pricePerPerson;
    private int $remainingQuantity;
    private array $dishes;
    private array $conditions;
    private ?array $illustrationDish = null; // données enrichies pour le front

    public function __construct(
        string $id,
        string $menuName,
        string $description,
        string $illustrationDishId,
        int $minimumPeople,
        float $pricePerPerson,
        int $remainingQuantity,
        array $dishes = [],
        array $conditions = [],
        array $illustrationDish = []
    ) {

        $this->id = $id;
        $this->menuName = $menuName;
        $this->description = $description;
        $this->illustrationDishId = $illustrationDishId;
        $this->minimumPeople = $minimumPeople;
        $this->pricePerPerson = $pricePerPerson;
        $this->remainingQuantity = $remainingQuantity;
        $this->dishes = $dishes;
        $this->conditions = $conditions;
        $this->illustrationDish = $illustrationDish;

    }

    // Getters: permet d'accéder aux propriétés privées de l'objet
    public function getId(): string { return $this->id; }
    public function getMenuName(): string { return $this->menuName; }
    public function getDescription(): string { return $this->description; }
    public function getIllustrationDishId(): string { return $this->illustrationDishId; }
    public function getMinimumPeople(): int { return $this->minimumPeople; }
    public function getPricePerPerson(): float { return $this->pricePerPerson; }
    public function getRemainingQuantity(): int { return $this->remainingQuantity; }
    public function getDishes(): array { return $this->dishes; }
    public function getConditions(): array { return $this->conditions; }
    public function getIllustrationDish(): ?array { return $this->illustrationDish; }

     //setters: permet de modifier l'id de l'objet généré par la bdd (après sa création)
    public function setId(string $id): void
    {
        $this->id = $id;
    }
    // setter pour l'illustration (tableau de données enrichies, pas juste l'id)
    public function setIllustrationDish(array $dish): void
    {
        $this->illustrationDish = $dish;
    }
    // setter pour les plats du menu (utiles pour récupérer les allergènes et régimes alimentaires associés à chaque plat)
    public function setDishes(array $dishes): void
    {
        $this->dishes = $dishes;
    }

    public function addDish(array $dish): void
    {
        if (!in_array($dish, $this->dishes, true)) {
            $this->dishes[] = $dish;
        }
    }
    public function addCondition(array $condition): void
    {
        if (!in_array($condition, $this->conditions, true)) {
            $this->conditions[] = $condition;
        }
    }
}