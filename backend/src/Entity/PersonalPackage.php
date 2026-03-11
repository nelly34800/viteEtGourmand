<?php

namespace App\Entity;
/**
 * Représente l'entité PersonalPackage correspondant
 * à la table `personal_package` en base de données.
 */
class PersonalPackage
{
    private ?string $id;
    private string $eventType;
    private int $staffRatio;
    private float $packagePrice;

    public function __construct(
        ?string $id,
        string $eventType,
        int $staffRatio,
        float $packagePrice

    ) {

        $this->id = $id;
        $this->eventType = $eventType;
        $this->staffRatio = $staffRatio;
        $this->packagePrice = $packagePrice;
    }

    // Getters: permet d'accéder aux propriétés privées de l'objet
    public function getId(): string { return $this->id; }
    public function getEventType(): string { return $this->eventType; }
    public function getStaffRatio(): int { return $this->staffRatio; }
    public function getPackagePrice(): float { return $this->packagePrice; }

     //setters: permet de modifier l'id de l'objet généré par la bdd (après sa création)
    public function setId(string $id): void
    {
        $this->id = $id;
    }
}