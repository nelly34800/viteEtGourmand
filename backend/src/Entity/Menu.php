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
    private int $minimumPeople;
    private float $pricePerPerson;
    private int $remainingQuantity;
    private array $dishes;
    private array $conditions;

    public function __construct(
        string $id,
        string $menuName,
        string $description,
        int $minimumPeople,
        float $pricePerPerson,
        int $remainingQuantity,
        array $dishes = [],
        array $conditions = []
    ) {

        $this->id = $id;
        $this->menuName = $menuName;
        $this->description = $description;
        $this->minimumPeople = $minimumPeople;
        $this->pricePerPerson = $pricePerPerson;
        $this->remainingQuantity = $remainingQuantity;
        $this->dishes = $dishes;
        $this->conditions = $conditions;

    }

    // Getters: permet d'accéder aux propriétés privées de l'objet
    public function getId(): string { return $this->id; }
    public function getMenuName(): string { return $this->menuName; }
    public function getDescription(): string { return $this->description; }
    public function getMinimumPeople(): int { return $this->minimumPeople; }
    public function getPricePerPerson(): float { return $this->pricePerPerson; }
    public function getRemainingQuantity(): int { return $this->remainingQuantity; }
    public function getDishes(): array { return $this->dishes; }
    public function getConditions(): array { return $this->conditions; }

     //setters: permet de modifier l'id de l'objet généré par la bdd (après sa création)
    public function setId(string $id): void
    {
        $this->id = $id;
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