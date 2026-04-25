<?php

namespace App\Repository;

use PDO;
use App\Entity\Menu;
use RuntimeException;
use Ramsey\Uuid\Uuid;
/**
 * Repository responsable de l'accès aux données
 * pour l'entité Menu.
 */
class MenuRepository
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
        // Transforme le tableau de la bdd en une instance de Menu (objet)
        $menus = [];

        foreach ($rows as $row) {
            $menuId = $row['menu_id'];

            // Si on n'a pas encore créé l'objet Menu
            if(!isset($menus[$menuId])){
                $menus[$menuId] = new Menu(
                    $menuId,
                    $row['menu_name'],
                    $row['description'],
                    $row['illustration_dish_id'],
                    $row['minimum_people'],
                    $row['price_per_person'],
                    $row['remaining_quantity'],
                );
                // initialise les plats
            $menus[$menuId]->setDishes([]);
            }
            // Ajouter le plat
            if ($row['dish_id'] !== null) {
                $dishId = $row['dish_id'];
                // récupérer les dishes déjà présents dans CE menu
                $currentDishes = $menus[$menuId]->getDishes();
                // Si le plat n'existe pas encore → on le crée
                if (!isset($currentDishes[$dishId])) {
                    $currentDishes[$dishId] = [
                        'id' => $dishId,
                        'name' => $row['dish_title'],
                        'description' => $row['dish_description'],
                        'picture' => $row['dish_picture'],
                        'categoryName' => $row['category_name'],
                        'diets' => [],
                        'allergens' => []
                    ];
                }
                // Ajouter diet s'il y en a
                if ($row['diet_id'] !== null) {
                    $currentDishes[$dishId]['diets'][$row['diet_id']] = [
                        'id' => $row['diet_id'],
                        'name' => $row['d_diet_name']
                    ];
                }
                // Ajouter allergen s'il y en a
                if ($row['allergen_id'] !== null) {
                    $currentDishes[$dishId]['allergens'][$row['allergen_id']] = [
                        'id' => $row['allergen_id'],
                        'name' => $row['d_allergen_name']
                    ];
                }
                // réinjecter dans le bon menu
                $menus[$menuId]->setDishes($currentDishes);
            }
            // Ajouter la condition si pas déjà présente
             if ($row['condition_id'] !== null) {
                $menus[$menuId]->addCondition([
                  'id' => $row['condition_id'],
                  'type' => $row['condition_type'],
                  'description' => $row['condition_description']
                  ]);
            }
            // Ajouter les données de l'illustration
            $menus[$menuId]->setIllustrationDish([
                'id' => $row['illustration_dish_id'],
                'picture' => $row['illustration_picture']
            ]);
        }
        // Nettoyage final (important pour le front)
        foreach ($menus as $menu) {
            $dishes = $menu->getDishes();

            foreach ($dishes as &$dish) {
                $dish['diets'] = array_values($dish['diets']);
                $dish['allergens'] = array_values($dish['allergens']);
            }

            $menu->setDishes(array_values($dishes));
        }

        return array_values($menus);
    }
    /**
     * Retourne un  tableau tous les menus.
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query
        ("SELECT 
            menu.id AS menu_id,
            menu.menu_name,
            menu.description,
            menu.illustration_dish_id,
            menu.minimum_people,
            menu.price_per_person,
            menu.remaining_quantity,
            dish.id AS dish_id,
            dish.dish_title,
            dish.description AS dish_description,
            dish.picture AS dish_picture,
            category_dish.category_name,
            diet.id AS diet_id,
            diet.diet_name AS d_diet_name,
            allergen.id AS allergen_id,
            allergen.allergen_name AS d_allergen_name,
            condition_menu.id AS condition_id,
            condition_menu.condition_type,
            condition_menu.description AS condition_description,
            illustration_dish.picture AS illustration_picture
        FROM menu 
        LEFT JOIN menu_dish ON menu.id = menu_dish.id_menu
        LEFT JOIN dish ON menu_dish.id_dish = dish.id
        LEFT JOIN category_dish ON dish.id_category_dish = category_dish.id
        LEFT JOIN menu_condition_menu ON menu_condition_menu.id_menu = menu.id
        LEFT JOIN condition_menu ON menu_condition_menu.id_condition_menu = condition_menu.id
        LEFT JOIN dish AS illustration_dish ON menu.illustration_dish_id = illustration_dish.id
        LEFT JOIN diet_dish ON dish.id = diet_dish.id_dish
        LEFT JOIN diet ON diet_dish.id_diet = diet.id
        LEFT JOIN allergen_dish ON dish.id = allergen_dish.id_dish
        LEFT JOIN allergen ON allergen_dish.id_allergen = allergen.id");

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->hydrate($rows);
    }
    /**
     * Retourne un menu par son ID.
     */
    public function findById(string $id): Menu
    {
        $stmt = $this->pdo->prepare
        ("SELECT 
            menu.id AS menu_id,
            menu.menu_name,
            menu.description,
            menu.illustration_dish_id,
            menu.minimum_people,
            menu.price_per_person,
            menu.remaining_quantity,
            dish.id AS dish_id,
            dish.dish_title,
            dish.description AS dish_description,
            dish.picture AS dish_picture,
            category_dish.category_name,
            diet.id AS diet_id,
            diet.diet_name AS d_diet_name,
            allergen.id AS allergen_id,
            allergen.allergen_name AS d_allergen_name,
            condition_menu.id AS condition_id,
            condition_menu.condition_type,
            condition_menu.description AS condition_description,
            illustration_dish.picture AS illustration_picture
        FROM menu 
        LEFT JOIN menu_dish ON menu.id = menu_dish.id_menu
        LEFT JOIN dish ON menu_dish.id_dish = dish.id
        LEFT JOIN category_dish ON dish.id_category_dish = category_dish.id
        LEFT JOIN menu_condition_menu ON menu_condition_menu.id_menu = menu.id
        LEFT JOIN dish AS illustration_dish ON menu.illustration_dish_id = illustration_dish.id
        LEFT JOIN condition_menu ON menu_condition_menu.id_condition_menu = condition_menu.id
        LEFT JOIN diet_dish ON dish.id = diet_dish.id_dish
        LEFT JOIN diet ON diet_dish.id_diet = diet.id
        LEFT JOIN allergen_dish ON dish.id = allergen_dish.id_dish
        LEFT JOIN allergen ON allergen_dish.id_allergen = allergen.id WHERE menu.id = ?");

        $stmt->execute([$id]);

        $rows= $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$rows) {
            // Si aucun résultat n'est trouvé lance une exception
            throw new RuntimeException('Menu not found');
        }

        return $this->hydrate($rows)[0];
    }
     /**
     * Insère un nouveau menu.
     */
    public function create(Menu $menu): void
    {
        // Génération UUID côté PHP
        $menuId = Uuid::uuid4()->toString();
        $menu->setId($menuId);

        try {
            // démarrage de la transaction
            $this->pdo->beginTransaction();

            // insertion du menu
            $stmt = $this->pdo->prepare("
                INSERT INTO menu (id, menu_name, description, illustration_dish_id, minimum_people, price_per_person, remaining_quantity)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            // exécute la requête avec les données du menu
            $stmt->execute([
                $menu->getId(),
                $menu->getMenuName(),
                $menu->getDescription(),
                $menu->getIllustrationDishId(),
                $menu->getMinimumPeople(),
                $menu->getPricePerPerson(),
                $menu->getRemainingQuantity()
            ]);

            // insertion dans table pivot menu_dish
            $stmtDish = $this->pdo->prepare("
                INSERT INTO menu_dish (id_menu, id_dish) VALUES (?, ?)
            ");
            foreach ($menu->getDishes() as $dishId) {
                $stmtDish->execute([$menuId, $dishId]);
            }
            // insertion dans table pivot menu_condition_menu
            $stmtCondition = $this->pdo->prepare("
                INSERT INTO menu_condition_menu (id_menu, id_condition_menu) VALUES (?, ?)
            ");
            foreach ($menu->getConditions() as $conditionId) {
                $stmtCondition->execute([$menuId, $conditionId]);
            }
                // commit
            $this->pdo->commit();

        } catch (\Exception $e) {
            // rollback en cas d’erreur
            $this->pdo->rollBack();
            throw new RuntimeException("Error while creating the menu : " . $e->getMessage());
        }
    }
    /**
     * Met à jour un menu existant.
     */
    public function update(Menu $menu): void
    {
        try {
            // démarrage de la transaction
            $this->pdo->beginTransaction();
            // modifie le menu
            $stmt = $this->pdo->prepare("
                UPDATE menu
                SET menu_name = ?, description = ?, illustration_dish_id = ?, minimum_people = ?, price_per_person = ?, remaining_quantity = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $menu->getMenuName(),
                $menu->getDescription(),
                $menu->getIllustrationDishId(),
                $menu->getMinimumPeople(),
                $menu->getPricePerPerson(),
                $menu->getRemainingQuantity(),
                $menu->getId()
            ]);

             // supprime anciennes relations
            $this->pdo->prepare("DELETE FROM menu_dish WHERE id_menu = ?")
                      ->execute([$menu->getId()]);

            $this->pdo->prepare("DELETE FROM menu_condition_menu WHERE id_menu = ?")
                      ->execute([$menu->getId()]);

            // réinsertion
            $stmtDish = $this->pdo->prepare("
                INSERT INTO menu_dish (id_menu, id_dish) VALUES (?, ?)
            ");

            foreach ($menu->getDishes() as $dishId) {
                $stmtDish->execute([$menu->getId(), $dishId]);
            }

               $stmtCondition = $this->pdo->prepare("
                INSERT INTO menu_condition_menu (id_menu, id_condition_menu) VALUES (?, ?)
            ");

            foreach ($menu->getConditions() as $conditionId) {
                $stmtCondition->execute([$menu->getId(), $conditionId]);
            }

            $this->pdo->commit();

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
    /**
     * Supprime un menu.
     */
    public function delete(string $id): void
    {
      try {

            $this->pdo->beginTransaction();

            // supprimer d'abord les pivots
            $this->pdo->prepare("DELETE FROM menu_dish WHERE id_menu = ?")
                      ->execute([$id]);

            $this->pdo->prepare("DELETE FROM menu_condition_menu WHERE id_menu = ?")
                      ->execute([$id]);

            // supprimer le menu
            $stmt = $this->pdo->prepare("DELETE FROM menu WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) {
                throw new RuntimeException('Menu not found');
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