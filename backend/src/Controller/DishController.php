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
        $dishs = $this->repository->findAll();

        $response = array_map(function($dish) {
          // Transformation en array pour JSON (hydratation inverse)
            return [
                'id' => $dish->getId(),
                'dish_title' => $dish->getDishTitle(),
                'description' => $dish->getDescription(),
                'picture' => $dish->getPicture(),
                'id_category_dish' => $dish->getIdCategoryDish(),
            ];
        }, $dishs);

        ResponseHelper::json($response);
    }
       /**
     * Affiche un plat par ID.
     */
    public function show(string $id): void
    {

        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour récupérer l'utilisateur par son id et affichage au format JSON
        $dish = $this->repository->findById($id);

        if (!$dish) {
            ResponseHelper::json(['error' => 'Not found'], 404);
        }
        // Transformation en array pour JSON (hydratation inverse)
        $response =([
            'id' => $dish->getId(),
            'dish_title' => $dish->getDishTitle(),
            'description' => $dish->getDescription(),
            'picture' => $dish->getPicture(),
            'id_category_dish' => $dish->getIdCategoryDish()
        ]);
        ResponseHelper::json($response);
    }
    /**
     * Crée un nouveau plat.
     */
    public function store(): void
    {
        // Lecture du JSON envoyé
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        if(!isset($data['dish_title'], $data['description'], $data['picture'], $data['id_category_dish'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        // Création de l'entité Dish à partir des données reçues
        $dish = new Dish(
            $data['dish_title'],
            $data['description'],
            $data['picture'],
            $data['id_category_dish']
        );
        //  appel du repository pour l'enregistrer en base
        try {
            $this->repository->create($dish);
            ResponseHelper::json(['message' => 'Dish created'], 201);

        } catch (\Exception $e) {
            ResponseHelper::json(['error' => 'Erreur lors de la création du plat', 'details' => $e->getMessage()], 500);
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
        // Validation des champs obligatoires
        if (!isset($data['dish_title'], $data['description'], $data['picture'], $data['id_category_dish'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        // Création de l'entité plat à partir des données reçues 
        $dish = new Dish(
            $data['dish_title'],
            $data['description'],
            $data['picture'],
            $data['id_category_dish'],
            null, // idCategoryName
            $id   // id
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
