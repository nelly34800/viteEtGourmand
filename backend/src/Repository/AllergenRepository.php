<?php

namespace App\Repository;

use PDO;
use App\Entity\Allergen;
use RuntimeException;
use Ramsey\Uuid\Uuid;

class AllergenRepository
{    private PDO $pdo;

    /**
     * Injection de la dépendance PDO.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function mapRowToAllergen(array $row): Allergen
    {
        // Transforme une ligne sql de la table `allergen` en objet Allergen
        return new Allergen(
            $row['id'],
            $row['allergen_name']
        );
    }
    /**
     * Retourne un  tableau tous les allergènes.
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT id, allergen_name FROM allergen");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $allergens = [];

        foreach ($rows as $row) {
            $allergens[] = $this->mapRowToAllergen($row);
        }

        return $allergens;
    }
    /**
     * Retourne un allergène par son ID.
     */
    public function findById(string $id): Allergen
    {
        $stmt = $this->pdo->prepare("SELECT * FROM allergen WHERE id = ?");
        $stmt->execute([$id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            // Si aucun résultat n'est trouvé lance une exception
            throw new RuntimeException('Allergen not found');
        }

        return $this->mapRowToAllergen($row);
    }
    /**
     * Insère un nouveau allergen.
     */
    public function create(Allergen $allergen): void
    {
        // Génération UUID côté PHP
        $allergenId = Uuid::uuid4()->toString();
        $allergen->setId($allergenId);

        $stmt = $this->pdo->prepare("
            INSERT INTO allergen (id, allergen_name)
            VALUES (?, ?)
        ");
        // exécute la requête avec les données de l'allergène
        $stmt->execute([
            $allergen->getId(),
            $allergen->getAllergenName()
        ]);
    }
    /**
     * Met à jour un l'allergène existant.
     */
    public function update(Allergen $allergen): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE allergen
            SET allergen_name = ?
            WHERE id = ?
        ");
        // exécute la requête avec les données de l'allergène
        $stmt->execute([
            $allergen->getAllergenName(),
            $allergen->getId()
        ]);
        // Si l'allergène n'existe pas retourne une erreur
        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('Allergen not found');
        }
    }
    /**
     * Supprime un allergène.
     */
    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM allergen WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('Allergen not found');
        }
    }
}