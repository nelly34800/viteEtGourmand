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
    private float $latitude;
    private float $longitude;
    private int $numberOfPeople;
    private float $deliveryCharges;
    private float $totalExcludingTax;
    private float $totalIncludingTax;
    private string $status;
    private bool $equipmentLoan;
    private bool $equipmentReturn;
    private string $idUser;
    private ?string $userLastName;
    private ?string $userFirstName;
    private ?string $userEmail;
    private ?string $userPhone;
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
        float $latitude,
        float $longitude,
        int $numberOfPeople,
        float $deliveryCharges,
        float $totalExcludingTax,
        float $totalIncludingTax,
        string $status,
        bool $equipmentLoan,
        bool $equipmentReturn,
        string $idUser,
        ?string $userLastName = null,
        ?string $userFirstName = null,
        ?string $userEmail = null,
        ?string $userPhone = null,
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
        $this->latitude = $latitude;
        $this->longitude =  $longitude;
        $this->numberOfPeople = $numberOfPeople;
        $this->deliveryCharges = $deliveryCharges;
        $this->totalExcludingTax = $totalExcludingTax;
        $this->totalIncludingTax = $totalIncludingTax;
        $this->status = $status;
        $this->equipmentLoan = $equipmentLoan;
        $this->equipmentReturn = $equipmentReturn;
        $this->idUser = $idUser;
        $this->userLastName = $userLastName;
        $this->userFirstName = $userFirstName;
        $this->userEmail = $userEmail;
        $this->userPhone = $userPhone;
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
    public function getLatitude(): float { return $this->latitude; }
    public function getLongitude(): float { return $this->longitude; }
    public function getNumberOfPeople(): int { return $this->numberOfPeople; }
    public function getDeliveryCharges(): float { return $this->deliveryCharges; }
    public function getTotalExcludingTax(): float { return $this->totalExcludingTax; }
    public function getTotalIncludingTax(): float { return $this->totalIncludingTax; }
    public function getStatus(): string { return $this->status; }
    public function getEquipmentLoan(): bool { return $this->equipmentLoan; }
    public function getEquipmentReturn(): bool { return $this->equipmentReturn; }
    public function getIdUser(): string { return $this->idUser; }
    public function getUserLastName(): ?string { return $this->userLastName; }
    public function getUserFirstName(): ?string { return $this->userFirstName; }
    public function getUserEmail(): ?string { return $this->userEmail; }
    public function getUserPhone(): ?string { return $this->userPhone; }
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