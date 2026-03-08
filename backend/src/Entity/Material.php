<?php

namespace App\Entity;

/**
 * Représente l'entité matériel correspondant
 * à la table `material` en base de données.
 */
class Material
{
    private ?string $id;
    private string $materialName;
    private int $quantityAvailable;
    private float $price;
    private string $idMaterialCategory;
    private ?string $materialCategoryName;
  

    public function __construct(
        ?string $id,
        string $materialName,
        int $quantityAvailable,
        float $price,
        string $idMaterialCategory,
        ?string $materialCategoryName = null,
    ) {
        $this->id = $id;
        $this->materialName = $materialName;
        $this->quantityAvailable = $quantityAvailable;
        $this->price = $price;
        $this->idMaterialCategory = $idMaterialCategory;
        $this->materialCategoryName = $materialCategoryName;
    }
    // Getters: permet d'accéder aux propriétés privées de l'objet
    public function getId(): string { return $this->id; }
    public function getMaterialName(): string { return $this->materialName; }
    public function getQuantityAvailable(): int { return $this->quantityAvailable; }
    public function getPrice(): float { return $this->price; }
    public function getIdMaterialCategory(): string { return $this->idMaterialCategory; }
    public function getMaterialCategoryName(): ?string { return $this->materialCategoryName; }

    //setters: permet de modifier l'id de l'objet généré par la bdd (après sa création)
    public function setId(string $id): void
    {
        $this->id = $id;
    }
}