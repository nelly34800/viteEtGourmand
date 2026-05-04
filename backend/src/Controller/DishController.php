<?php

namespace App\Controller;
 
use App\Repository\DishRepository;
use App\Entity\Dish;
use App\Helper\RequestHelper;
use App\Helper\ResponseHelper;
use App\Helper\ValidatorHelper;
use Database;
use InvalidArgumentException;
use RuntimeException;
/**
 * Contrôleur responsable de la gestion des plats.
 */
class DishController
{
    private DishRepository $repository;

    public function __construct()
    {
      // Récupération de la connexion PDO et initialisation du repository
        $pdo = Database::getConnection();
        // Injection de la dépendance du repository
        $this->repository = new DishRepository($pdo);
    }
     /**
     * Liste tous les plats.
     */
    public function index(): void
    {
        // Appel du repository pour récupérer tous les plats et affichage au format JSON
        $dishes = $this->repository->findAll();

        $response = array_map(function(Dish $dish) {
          // Transformation en array pour JSON (hydratation inverse)
            return [
                'id' => $dish->getId(),
                'dish_title' => $dish->getDishTitle(),
                'description' => $dish->getDescription(),
                'picture' => $dish->getPicture(),
                'id_category_dish' => $dish->getIdCategoryDish(),
                'category_name' => $dish->getCategoryName(),
                'diets' => $dish->getDiets(),
                'allergens' => $dish->getAllergens(),
            ];
        }, $dishes);

        ResponseHelper::json($response);
    }
       /**
     * Affiche un plat par ID.
     */
    public function show(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour récupérer le plat par son id et affichage au format JSON

        try {
            $dish = $this->repository->findById($id);
            // Transformation en array pour JSON (hydratation inverse)
            $response =([
                'id' => $dish->getId(),
                'dish_title' => $dish->getDishTitle(),
                'description' => $dish->getDescription(),
                'picture' => $dish->getPicture(),
                'id_category_dish' => $dish->getIdCategoryDish(),
                'category_name' => $dish->getCategoryName(),
                'diets' => $dish->getDiets(),
                'allergens' => $dish->getAllergens()
            ]);
            ResponseHelper::json($response);

        } catch (RuntimeException $e) {
        ResponseHelper::json(['error' => 'Not found'], 404);
        }
    }

    /**
     * Crée un nouveau plat.
     */
public function store(): void
{
    $data = RequestHelper::getJson();
    ValidatorHelper::validateUuid($data['id_category_dish']);
    ValidatorHelper::validateUuidArray($data['allergen_id'] ?? []);
    ValidatorHelper::validateUuidArray($data['diet_id'] ?? []);
    ValidatorHelper::validatePicture($data['picture']);
    // Validation des champs obligatoires
    if(!isset($data['dish_title'], $data['description'], $data['picture'], $data['id_category_dish'])) {
        throw new InvalidArgumentException('Invalid input');
    }
    $dietIds = $data['diet_id'] ?? [];
    $allergenIds = $data['allergen_id'] ?? [];
    // Création de l'entité Dish à partir des données reçues
    $dish = new Dish(
        id: '', // l'UUID sera généré côté repository
        dishTitle: $data['dish_title'],
        description: $data['description'],
        picture: $data['picture'],
        idCategoryDish: $data['id_category_dish'],
        categoryName: null, 
        diets: $dietIds,
        allergens: $allergenIds
    );
        //  appel du repository pour l'enregistrer en base
        try {
            $this->repository->create($dish);
            ResponseHelper::json(['message' => 'Dish created'], 201);

        } catch (\Exception $e) {
            ResponseHelper::json(['error' => 'Error during dish creation', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Met à jour un plat.
     */
    public function update(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Lecture du JSON
        $data = RequestHelper::getJson();
        ValidatorHelper::validateUuid($data['id_category_dish']);
        ValidatorHelper::validateUuidArray($data['allergen_id'] ?? []);
        ValidatorHelper::validateUuidArray($data['diet_id'] ?? []);
        ValidatorHelper::validatePicture($data['picture']);
        // Validation des champs obligatoires
        if (!isset($data['dish_title'], $data['description'], $data['picture'], $data['id_category_dish'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        $dietIds = $data['diet_id'] ?? [];
        $allergenIds = $data['allergen_id'] ?? [];
        // Création de l'entité plat à partir des données reçues 
        $dish = new Dish(
            $id,
            $data['dish_title'],
            $data['description'],
            $data['picture'],
            $data['id_category_dish'],
            null, //category_name
            $dietIds,
            $allergenIds
        );
        // appel du repository pour mettre à jour en base
        $this->repository->update($dish);

        ResponseHelper::json(['message' => 'Updated']);
    }
      /**
     * Supprime un plat.
     */
    public function delete(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour supprimer l'utilisateur en base
        $this->repository->delete($id);

        ResponseHelper::json(['message' => 'Deleted']);
    }
}