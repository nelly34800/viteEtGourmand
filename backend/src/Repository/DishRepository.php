<?php

namespace App\Repository;

use PDO;
use App\Entity\Dish;
use RuntimeException;
use Ramsey\Uuid\Uuid;
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

    private function hydrate(array $rows): array
    {
        // Transforme le tableau de la bdd en une instance de Dish (objet)
        $dishes = [];

        foreach ($rows as $row) {
            $dishId = $row['dish_id'];

            // Si on n'a pas encore créé l'objet Dish
            if (!isset($dishes[$dishId])) {
                $dishes[$dishId] = new Dish(
                    $dishId,
                    $row['dish_title'],
                    $row['description'],
                    $row['picture'],
                    $row['id_category_dish'],
                    $row['category_name']
                );
            }

            // Ajouter le régime si pas déjà présent
             if ($row['diet_id'] !== null) {
                $dishes[$dishId]->addDiet([
                  'id' => $row['diet_id'],
                  'name' => $row['diet_name']
                  ]);
            }

            // Ajouter l'allergènesi  pas déjà présent
            if ($row['allergen_id'] !== null) {
            $dishes[$dishId]->addAllergen([
                  'id' => $row['allergen_id'],
                  'name' => $row['allergen_name']
                  ]);
            }
        }

    // À la fin, $dishes contient tous les objets Dish avec leurs tableaux diet et allergen
    return array_values($dishes);
    }
    /**
     * Retourne un  tableau tous les plats
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query
            ("SELECT 
                dish.id AS dish_id,
                dish.dish_title,
                dish.description,
                dish.picture,
                dish.id_category_dish,
                category_dish.category_name,
                diet.id AS diet_id,
                diet.diet_name,
                allergen.id AS allergen_id,
                allergen.allergen_name
            FROM dish
            LEFT JOIN category_dish ON dish.id_category_dish = category_dish.id
            LEFT JOIN diet_dish ON dish.id = diet_dish.id_dish
            LEFT JOIN diet ON diet_dish.id_diet = diet.id
            LEFT JOIN allergen_dish ON dish.id = allergen_dish.id_dish
            LEFT JOIN allergen ON allergen_dish.id_allergen = allergen.id");

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->hydrate($rows);
    }
    /**
     * Retourne un plat par son ID.
     */
    public function findById(string $id): Dish
    {
        $stmt = $this->pdo->prepare
            ("SELECT 
                dish.id AS dish_id,
                dish.dish_title,
                dish.description,
                dish.picture,
                dish.id_category_dish,
                category_dish.category_name,
                diet.id AS diet_id,
                diet.diet_name,
                allergen.id AS allergen_id,
                allergen.allergen_name
            FROM dish
            LEFT JOIN category_dish ON dish.id_category_dish = category_dish.id
            LEFT JOIN diet_dish ON dish.id = diet_dish.id_dish
            LEFT JOIN diet ON diet_dish.id_diet = diet.id
            LEFT JOIN allergen_dish ON dish.id = allergen_dish.id_dish
            LEFT JOIN allergen ON allergen_dish.id_allergen = allergen.id WHERE dish.id = ?");

        $stmt->execute([$id]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$rows) {
            throw new RuntimeException('Dish not found');
        }

        return $this->hydrate($rows)[0];
        }
     /**
     * Insère un nouveau plat.
     */
    public function create(Dish $dish): void
{
    // Génération UUID côté PHP
    $dishId = Uuid::uuid4()->toString();
    $dish->setId($dishId);

    try {
        // démarrage de la transaction
        $this->pdo->beginTransaction();

        // insertion du plat
        $stmt = $this->pdo->prepare("
            INSERT INTO dish (id, dish_title, description, picture, id_category_dish)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $dish->getId(),
            $dish->getDishTitle(),
            $dish->getDescription(),
            $dish->getPicture(),
            $dish->getIdCategoryDish()
        ]);

        // insertion dans table pivot diet
        $stmtDiet = $this->pdo->prepare("
            INSERT INTO diet_dish (id_diet, id_dish) VALUES (?, ?)
        ");
        foreach ($dish->getDiets() as $dietId) {
            $stmtDiet->execute([$dietId, $dishId]);
        }

        // insertion dans table pivot allergen
        $stmtAllergen = $this->pdo->prepare("
            INSERT INTO allergen_dish (id_allergen, id_dish) VALUES (?, ?)
        ");
        foreach ($dish->getAllergens() as $allergenId) {
            $stmtAllergen->execute([$allergenId, $dishId]);
        }

        // commit
        $this->pdo->commit();

    } catch (\Exception $e) {
        // rollback en cas d’erreur
        $this->pdo->rollBack();
        throw new RuntimeException("Error while creating the dish : " . $e->getMessage());
    }
}
    /**
     * Met à jour un plat existant.
     */
    public function update(Dish $dish): void
    {
        try {
            // démarrage de la transaction
            $this->pdo->beginTransaction();
            // modifie le plat
            $stmt = $this->pdo->prepare("
                UPDATE dish 
                SET dish_title = ?, description = ?, picture = ?, id_category_dish = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $dish->getDishTitle(),
                $dish->getDescription(),
                $dish->getPicture(),
                $dish->getIdCategoryDish(),
                $dish->getId()
            ]);

            // supprime anciennes relations
            $this->pdo->prepare("DELETE FROM diet_dish WHERE id_dish = ?")
                      ->execute([$dish->getId()]);

            $this->pdo->prepare("DELETE FROM allergen_dish WHERE id_dish = ?")
                      ->execute([$dish->getId()]);

            // réinsertion
            $stmtDiet = $this->pdo->prepare("
                INSERT INTO diet_dish (id_diet, id_dish) VALUES (?, ?)
            ");

            foreach ($dish->getDiets() as $dietId) {
                $stmtDiet->execute([$dietId, $dish->getId()]);
            }

            $stmtAllergen = $this->pdo->prepare("
                INSERT INTO allergen_dish (id_allergen, id_dish) VALUES (?, ?)
            ");

            foreach ($dish->getAllergens() as $allergenId) {
                $stmtAllergen->execute([$allergenId, $dish->getId()]);
            }

            $this->pdo->commit();

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
    /**
     * Supprime un plat.
     */
    public function delete(string $id): void
    {
        try {

            $this->pdo->beginTransaction();

            // supprimer d'abord les pivots
            $this->pdo->prepare("DELETE FROM diet_dish WHERE id_dish = ?")
                      ->execute([$id]);

            $this->pdo->prepare("DELETE FROM allergen_dish WHERE id_dish = ?")
                      ->execute([$id]);

            // supprimer le plat
            $stmt = $this->pdo->prepare("DELETE FROM dish WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) {
                throw new RuntimeException('Dish not found');
            }

            $this->pdo->commit();

        } catch (\Throwable $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}