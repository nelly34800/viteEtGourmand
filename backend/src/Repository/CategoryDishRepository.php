<?php

namespace App\Repository;

use PDO;

class CategoryDishRepository
{    private PDO $pdo;

    /**
     * Injection de la dépendance PDO.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    /**
     * Retourne un  tableau tous les catégories des plats.
     *
     * @return array
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT id, category_name FROM category_dish");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findById(string $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM category_dish WHERE id = ?");
        $stmt->execute([$id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        return $result;
    }
}