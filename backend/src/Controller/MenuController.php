<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use App\Entity\Menu;
use App\Helper\RequestHelper;
use App\Helper\ResponseHelper;
use App\Helper\ValidatorHelper;
use Database;
use InvalidArgumentException;
use RuntimeException;
/**
 * Contrôleur responsable de la gestion des menus.
 */
class MenuController
{
    private MenuRepository $repository;

    public function __construct()
    {
        // Récupération de la connexion PDO et initialisation du repository
        $pdo = Database::getConnection();
        // Injection de la dépendance du repository
        $this->repository = new MenuRepository($pdo);
    }
     /**
     * Liste tous les menus.
     */
    public function index(): void
    {
        // Appel du repository pour récupérer tous les menus et affichage au format JSON
        $menus = $this->repository->findAll();

        $response = array_map(function(Menu $menu) {
          // Transformation en array pour JSON (hydratation inverse)
            return [
                'id' => $menu->getId(),
                'menu_name' => $menu->getMenuName(),
                'description' => $menu->getDescription(),
                'minimum_people' => $menu->getMinimumPeople(),
                'price_per_person' => $menu->getPricePerPerson(),
                'remaining_quantity' => $menu->getRemainingQuantity(),
                'dishes' => $menu->getDishes(),
                'conditions' => $menu->getConditions(),
                'illustration' => $menu->getIllustrationDish(),
            ];
        }, $menus);

        ResponseHelper::json($response);
    }
       /**
     * Affiche un menu par ID.
     */
    public function show(string $id): void
    {

        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour récupérer le menu par son id et affichage au format JSON

        try {
            $menu = $this->repository->findById($id);
            // Transformation en array pour JSON (hydratation inverse)
        $response =([
            'id' => $menu->getId(),
            'menu_name' => $menu->getMenuName(),
            'description' => $menu->getDescription(),
            'minimum_people' => $menu->getMinimumPeople(),
            'price_per_person' => $menu->getPricePerPerson(),
            'remaining_quantity' => $menu->getRemainingQuantity(),
            'dishes' => $menu->getDishes(),
            'conditions' => $menu->getConditions(),
            'illustration' => $menu->getIllustrationDish(),
        ]);
        ResponseHelper::json($response);

        } catch (RuntimeException $e) {
        ResponseHelper::json(['error' => 'Not found'], 404);
        }
    }
    /**
     * Crée un nouveau menu.
     */
    public function store(): void
    {
        // Lecture du JSON envoyé
        $data = RequestHelper::getJson();
        ValidatorHelper::validateUuidArray($data['dish_id'] ?? []);
        ValidatorHelper::validateUuidArray($data['condition_id'] ?? []);

        // Validation des champs obligatoires
        if(!isset($data['menu_name'], $data['description'], $data['minimum_people'], $data['price_per_person'], $data['remaining_quantity'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        // Validation de l'illustration (obligatoire)
        if (empty($data['illustration_dish_id'])) {
            throw new InvalidArgumentException("Image obligatoire");
        }
        $dishIds = $data['dish_id'] ?? [];
        $conditionIds = $data['condition_id'] ?? [];
        // Création de l'entité Menu à partir des données reçues
        $menu = new Menu(
            id: '', // l'UUID sera généré côté repository
            menuName: $data['menu_name'],
            description: $data['description'],
            illustrationDishId: $data['illustration_dish_id'],
            minimumPeople: $data['minimum_people'],
            pricePerPerson: $data['price_per_person'],
            remainingQuantity: $data['remaining_quantity'],
            dishes: $dishIds,
            conditions:$conditionIds
        );
 
        //  appel du repository pour l'enregistrer en base
        try {
            $this->repository->create($menu);
            ResponseHelper::json(['message' => 'Menu created'], 201);

        } catch (\Exception $e) {
            ResponseHelper::json(['error' => 'Error during menu creation', 'details' => $e->getMessage()], 500);
        }
    }
    /**
     * Met à jour un menu.
     */
    public function update(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Lecture du JSON
        $data = RequestHelper::getJson();
        ValidatorHelper::validateUuidArray($data['dish_id'] ?? []);
        ValidatorHelper::validateUuidArray($data['condition_id'] ?? []);
        // Validation des champs obligatoires
        if (!isset($data['menu_name'], $data['description'], $data['minimum_people'], $data['price_per_person'], $data['remaining_quantity'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        // Validation de l'illustration (obligatoire)
        if (empty($data['illustration_dish_id'])) {
            throw new InvalidArgumentException("Image obligatoire");
        }
         $dishIds = $data['dish_id'] ?? [];
         $conditionIds = $data['condition_id'] ?? [];
        // Création de l'entité menu à partir des données reçues 
        $menu = new Menu(
            $id,
            $data['menu_name'],
            $data['description'],
            $data['illustration_dish_id'],
            $data['minimum_people'],
            $data['price_per_person'],
            $data['remaining_quantity'],
            $dishIds,
            $conditionIds
        );
        // appel du repository pour mettre à jour en base
        $this->repository->update($menu);

        ResponseHelper::json(['message' => 'Updated']);
    }
      /**
     * Supprime un menu.
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