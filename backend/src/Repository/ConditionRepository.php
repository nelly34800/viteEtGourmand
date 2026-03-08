<?php

namespace App\Repository;

use PDO;
use App\Entity\Condition;
use RuntimeException;
use Ramsey\Uuid\Uuid;

class ConditionRepository
{    private PDO $pdo;

    /**
     * Injection de la dépendance PDO.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function mapRowToCondition(array $row): Condition
    {
        // Transforme une ligne sql de la table `condition_menu` en objet Condition
        return new Condition(
            $row['id'],
            $row['condition_type'],
             $row['description']
        );
    }
    /**
     * Retourne un  tableau tous les conditions.
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT id, condition_type, `description` FROM condition_menu");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $conditions = [];

        foreach ($rows as $row) {
            $conditions[] = $this->mapRowToCondition($row);
        }

        return $conditions;
    }
    // Retourne une condition par son ID.
    public function findById(string $id): Condition
    {
        $stmt = $this->pdo->prepare("SELECT id, condition_type, `description` FROM condition_menu WHERE id = ?");
        $stmt->execute([$id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            // Si aucun résultat n'est trouvé lance une exception
            throw new RuntimeException('Condition not found');
        }

        return $this->mapRowToCondition($row);
    }
    /**
     * Insère une nouvelle condition.
     */
    public function create(Condition $condition): void
    {
        // Génération UUID côté PHP
        $conditionId = Uuid::uuid4()->toString();
        $condition->setId($conditionId);

        $stmt = $this->pdo->prepare("
            INSERT INTO condition_menu (id, condition_type, description)
            VALUES (?, ?, ?)
        ");
        // exécute la requête avec les données de la condition
        $stmt->execute([
            $condition->getId(),
            $condition->getConditionType(),
            $condition->getDescription(),
        ]);
    }
    /**
     * Met à jour une condition existant.
     */
    public function update(Condition $condition): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE condition_menu
            SET condition_type = ?, description = ?
            WHERE id = ?
        ");
        // exécute la requête avec les données de la condition.
        $stmt->execute([
            $condition->getConditionType(),
            $condition->getDescription(),
            $condition->getId()
        ]);
        // Si la condition n'existe pas retourne une erreur
        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('Condition not found');
        }
    }
    /**
     * Supprime une condition.
     */
    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM condition_menu WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('Condition not found');
        }
    }
}