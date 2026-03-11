<?php

namespace App\Entity;
/**
 * Représente l'entité Notice correspondant
 * à la table `notice` en base de données.
 */
class Notice
{
    private ?string $id;
    private int $note;
    private string $description;
    private string $signature;
    private string $status;
    private \DateTimeImmutable $date;
    private string $idOrder;

    public function __construct(
        ?string $id,
        int $note,
        string $description,
        string $signature,
        string $status,
        \DateTimeImmutable $date,
        string $idOrder

    ) {

        $this->id = $id;
        $this->note = $note;
        $this->description = $description;
        $this->signature = $signature;
        $this->status = $status;
        $this->date = $date;
        $this->idOrder = $idOrder;
    }

    // Getters: permet d'accéder aux propriétés privées de l'objet
    public function getId(): string { return $this->id; }
    public function getNote(): int { return $this->note; }
    public function getDescription(): string { return $this->description; }
    public function getSignature(): string { return $this->signature; }
    public function getStatus(): string { return $this->status; }
    public function getDate(): \DateTimeImmutable { return $this->date; }
    public function getIdOrder(): string { return $this->idOrder; }

     //setters: permet de modifier l'id de l'objet généré par la bdd (après sa création)
    public function setId(string $id): void
    {
        $this->id = $id;
    }
}