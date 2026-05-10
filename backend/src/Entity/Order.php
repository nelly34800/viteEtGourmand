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
    private float $distanceKm;
    private int $numberOfPeople;
    private float $deliveryCharges;
    private float $totalAmount;
    private string $status;
    private ?\DateTimeImmutable $statusChangedAt;
    private bool $equipmentLoan;
    private bool $equipmentReturn;
    private ?string $cancellationReason;
    private ?string $contactMode;
    private string $idUser;
    private ?string $userLastName;
    private ?string $userFirstName;
    private ?string $userEmail;
    private ?string $userPhone;
    private bool $hasNotice;
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
        float $distanceKm,
        int $numberOfPeople,
        float $deliveryCharges,
        float $totalAmount,
        string $status,
        bool $equipmentLoan,
        bool $equipmentReturn,
        string $idUser,
        bool $hasNotice= false,
        ?\DateTimeImmutable $statusChangedAt,
        ?string $cancellationReason = null,
        ?string $contactMode = null,
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
        $this->distanceKm = $distanceKm;
        $this->numberOfPeople = $numberOfPeople;
        $this->deliveryCharges = $deliveryCharges;
        $this->totalAmount = $totalAmount;
        $this->status = $status;
        $this->statusChangedAt = $statusChangedAt;
        $this->equipmentLoan = $equipmentLoan;
        $this->equipmentReturn = $equipmentReturn;
        $this->cancellationReason = $cancellationReason;
        $this->contactMode = $contactMode;
        $this->idUser = $idUser;
        $this->userLastName = $userLastName;
        $this->userFirstName = $userFirstName;
        $this->userEmail = $userEmail;
        $this->userPhone = $userPhone;
        $this->hasNotice = $hasNotice;
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
    public function getDistanceKm(): float { return $this->distanceKm; }
    public function getNumberOfPeople(): int { return $this->numberOfPeople; }
    public function getDeliveryCharges(): float { return $this->deliveryCharges; }
    public function getTotalAmount(): float { return $this->totalAmount; }
    public function getStatus(): string { return $this->status; }
    public function getStatusChangedAt(): ?\DateTimeImmutable { return $this->statusChangedAt; }
    public function getEquipmentLoan(): bool { return $this->equipmentLoan; }
    public function getEquipmentReturn(): bool { return $this->equipmentReturn; }
    public function getCancellationReason(): ?string { return $this->cancellationReason; }
    public function getContactMode(): ?string { return $this->contactMode; }
    public function getIdUser(): string { return $this->idUser; }
    public function getUserLastName(): ?string { return $this->userLastName; }
    public function getUserFirstName(): ?string { return $this->userFirstName; }
    public function getUserEmail(): ?string { return $this->userEmail; }
    public function getUserPhone(): ?string { return $this->userPhone; }
    public function hasNotice(): bool { return $this->hasNotice; }
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