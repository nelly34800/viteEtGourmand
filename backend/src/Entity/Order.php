<?php

namespace App\Entity;

/**
 * Représente l'entité Order correspondant
 * à la table `order` en base de données.
 */
class Order
{
    private string $id;
    private \DateTimeImmutable $orderDate;
    private \DateTimeImmutable $serviceDate;
    private string $deliveryAddress;
    private string $city;
    private string $postalCode;
    private int $numberOfPeople;
    private float $totalOrderPrice;
    private string $status;
    private bool $equipmentLoan;
    private bool $equipmentReturn;
    private string $idUser;
    private array $menus;
    private array $materials;
    private array $drinkPackages;
    private array $personalPackages;



    public function __construct(
        string $id,
        \DateTimeImmutable $orderDate,
        \DateTimeImmutable $serviceDate,
        string $deliveryAddress,
        string $city,
        string $postalCode,
        int $numberOfPeople,
        float $totalOrderPrice,
        string $status,
        bool $equipmentLoan,
        bool $equipmentReturn,
        string $idUser,
        array $menus = [],
        array $materials = [],
        array $drinkPackages = [],
        array $personalPackages = []
    ) {
        $this->id = $id;
        $this->orderDate = $orderDate;
        $this->serviceDate = $serviceDate;
        $this->deliveryAddress = $deliveryAddress;
        $this->city = $city;
        $this->postalCode = $postalCode;
        $this->numberOfPeople = $numberOfPeople;
        $this->totalOrderPrice = $totalOrderPrice;
        $this->status = $status;
        $this->equipmentLoan = $equipmentLoan;
        $this->equipmentReturn = $equipmentReturn;
        $this->idUser = $idUser;
        $this->menus = $menus;
        $this->materials = $materials;
        $this->drinkPackages = $drinkPackages;
        $this->personalPackages = $personalPackages;
    }
    // Getters: permet d'accéder aux propriétés privées de l'objet
    public function getId(): string { return $this->id; }
    public function getOrderDate(): \DateTimeImmutable { return $this->orderDate; }
    public function getServiceDate(): \DateTimeImmutable { return $this->serviceDate; }
    public function getDeliveryAddress(): string { return $this->deliveryAddress; }
    public function getCity(): string { return $this->city; }
    public function getPostalCode(): string { return $this->postalCode; }
    public function getNumberOfPeople(): int { return $this->numberOfPeople; }
    public function getTotalOrderPrice(): float { return $this->totalOrderPrice; }
    public function getStatus(): string { return $this->status; }
    public function getEquipmentLoan(): bool { return $this->equipmentLoan; }
    public function getEquipmentReturn(): bool { return $this->equipmentReturn; }
    public function getIdUser(): string { return $this->idUser; }
    public function getMenus(): array { return $this->menus; }
    public function getMaterials(): array { return $this->materials; }
    public function getDrinkPackages(): array { return $this->drinkPackages; }
    public function getPersonalPackages(): array { return $this->personalPackages; }

    //setters: permet de modifier l'id de l'objet généré par la bdd (après sa création)
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function addMenu(array $menu): void
    {
        if (!in_array($menu, $this->menus, true)) {
            $this->menus[] = $menu;
        }
    }

    public function addMaterial(array $material): void
    {
        if (!in_array($material, $this->materials, true)) {
            $this->materials[] = $material;
        }
    }
        public function addDrinkPackage(array $drinkPackage): void
    {
        if (!in_array($drinkPackage, $this->drinkPackages, true)) {
            $this->drinkPackages[] = $drinkPackage;
        }
    }
        public function addPersonalPackage(array $personalPackage): void
    {
        if (!in_array($personalPackage, $this->personalPackages, true)) {
            $this->personalPackages[] = $personalPackage;
        }
    }
}