<?php

namespace App\Repository;

use PDO;
use App\Entity\Dish;
use RuntimeException;
/**
 * Repository responsable de l'accès aux données
 * pour l'entité Dish.
 */
class DishRepository
{
    private PDO $pdo;

    /**
     * Injection de la dépendance PDO.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function hydrate(array $data): Dish
    {
        // Transforme le tableau de la bdd en une instance de Dish (objet)
        return new Dish(
            $data['dish_title'],
            $data['description'],
            $data['picture'],
            $data['id_category_dish'],
            $data['category_name'],
            $data['id']
        );
    }
    /**
     * Retourne un  tableau tous les plats.
     *
     * @return array
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT dish.*, category_dish.category_name FROM dish 
        LEFT JOIN category_dish ON dish.id_category_dish = category_dish.id");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $dishes = [];

        foreach ($results as $row) {
            $dishes[] = $this->hydrate($row);
        }

        return $dishes;
    }
    /**
     * Retourne un plat par son ID.
     */
    public function findById(string $id): Dish
    {
        $stmt = $this->pdo->prepare("SELECT dish.*, category_dish.category_name FROM dish
        LEFT JOIN category_dish ON dish.id_category_dish = category_dish.id WHERE dish.id = ?");
        $stmt->execute([$id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            // Si aucun résultat n'est trouvé lance une exception
            throw new RuntimeException('Dish not found');
        }

        return $this->hydrate($result);
    }
     /**
     * Insère un nouveau plat.
     */
    public function create(Dish $dish): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO dish (dish_title, description, picture, id_category_dish)
            VALUES (?, ?, ?, ?)
        ");
        // exécute la requête avec les données du plat
        $stmt->execute([
            $dish->getDishTitle(),
            $dish->getDescription(),
            $dish->getPicture(),
            $dish->getIdCategoryDish()
        ]);
    }
    /**
     * Met à jour un plat existant.
     */
    public function update(Dish $dish): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE dish
            SET dish_title = ?, description = ?, picture = ?
            WHERE id = ?
        ");
       
        $stmt->execute([
            $dish->getDishTitle(),
            $dish->getDescription(),
            $dish->getPicture(),
            $dish->getId()
        ]);
        // Si le plat n'existe pas retourne une erreur
        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('Dish not found');
        }
    }
    /**
     * Supprime un plat.
     */
    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM dish WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('Dish not found');
        }
    }
}