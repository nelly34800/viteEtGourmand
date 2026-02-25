<?php

namespace App\Entity;

/**
 * Représente l'entité Schedule correspondant
 * à la table `schedule` en base de données.
 */
class Schedule
{
    private ?string $id;
    private string $scheduleName;
    private string $firstDay;
    private string $lastDay;
    private string $openingTime;
    private string $closingTime;

    public function __construct(
        string $scheduleName,
        string $firstDay,
        string $lastDay,
        string $openingTime,
        string $closingTime,
        ?string $id = null
    ) {
        if (empty($scheduleName)) {
        throw new InvalidArgumentException('Schedule name is required');
        }

        $this->id = $id;
        $this->scheduleName = $scheduleName;
        $this->firstDay = $firstDay;
        $this->lastDay = $lastDay;
        $this->openingTime = $openingTime;
        $this->closingTime = $closingTime;
    }

    // Getters: permet d'accéder aux propriétés privées de l'objet
    public function getId(): ?string { return $this->id; }
    public function getScheduleName(): string { return $this->scheduleName; }
    public function getFirstDay(): string { return $this->firstDay; }
    public function getLastDay(): string { return $this->lastDay; }
    public function getOpeningTime(): string { return $this->openingTime; }
    public function getClosingTime(): string { return $this->closingTime; }

     //setters: permet de modifier l'id de l'objet généré par la bdd (après sa création)
    public function setId(string $id): void
    {
        $this->id = $id;
    }
}