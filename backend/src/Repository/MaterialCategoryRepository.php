<?php

namespace App\Repository;

use PDO;

class MaterialCategoryRepository
{    private PDO $pdo;

    /**
     * Injection de la dépendance PDO.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    /**
     * Retourne un  tableau tous les catégories de materiel.
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT id, material_category_name FROM material_category");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Retourne une catégorie de matériel par son ID.
     */
    public function findById(string $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id, material_category_name FROM material_category WHERE id = ?");
        $stmt->execute([$id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        return $result;
    }
}