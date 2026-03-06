<?php

namespace App\Repository;

use PDO;
use App\Entity\Diet;
use RuntimeException;
use Ramsey\Uuid\Uuid;

/**
 * Repository responsable de l'accès aux données
 * pour l'entité Diet.
 */
class DietRepository
{
   private PDO $pdo;

    /**
     * Injection de la dépendance PDO.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

        private function mapRowToDiet(array $row): Diet
    {
        // Transforme le tableau de la bdd en une instance de Diet (objet)
        return new Diet(
            $row['id'],
            $row['diet_name']
        );
    }
    /**
     * Retourne un tableau tous les régimes alimentaires.
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT id, diet_name FROM diet");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $diets = [];

        foreach ($rows as $row) {
            $diets[] = $this->mapRowToDiet($row);
        }

        return $diets;

    }
    // Retourne un régime alimentaire par son ID.
    public function findById(string $id): Diet
    {
        $stmt = $this->pdo->prepare("SELECT * FROM diet WHERE id = ?");
        $stmt->execute([$id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            // Si aucun résultat n'est trouvé lance une exception
            throw new RuntimeException('Diet not found');
        }

        return $this->mapRowToDiet($row);
    }
    /**
     * Insère un nouveau régime alimentaire.
     */
    public function create(Diet $diet): void
    {
        // Génération UUID côté PHP
        $dietId = Uuid::uuid4()->toString();
        $diet->setId($dietId);

        $stmt = $this->pdo->prepare("
            INSERT INTO diet (id, diet_name)
            VALUES (?, ?)
        ");
        // exécute la requête avec les données du régime alimentaire
        $stmt->execute([
            $diet->getId(),
            $diet->getDietName()
        ]);
    }
    /**
     * Met à jour un régime alimentaire existant.
     */
    public function update(Diet $diet): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE diet
            SET diet_name = ?
            WHERE id = ?
        ");
        // exécute la requête avec les données du régime alimentaire
        $stmt->execute([
            $diet->getDietName(),
             $diet->getId()
        ]);
        // Si le régime alimentaire n'existe pas retourne une erreur
        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('Diet not found');
        }
    }
    /**
     * Supprime un régime alimentaire.
     */
    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM diet WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('Diet not found');
        }
    }
}