<?php

namespace App\Controller;

use App\Repository\CategoryDishRepository;
use App\Helper\ResponseHelper;
use App\Helper\ValidatorHelper;
use Database;
/**
 * Contrôleur responsable de la gestion des catégories de plats.
 */
class CategoryDishController
{
    private CategoryDishRepository $repository;

    public function __construct()
    {
      // Récupération de la connexion PDO et initialisation du repository
        $pdo = Database::getConnection();
        // Injection de la dépendance du repository
        $this->repository = new CategoryDishRepository($pdo);
    }
     /**
     * Liste tous les employés.
     */
    public function index(): void
    {
        // Appel du repository pour récupérer toutes les catégories de plats et affichage au format JSON
        $categoriesDish = $this->repository->findAll();
        ResponseHelper::json($categoriesDish);
    }
       /**
     * Affiche une catégorie de plat par ID.
     */
    public function show(string $id): void
    {

        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour récupérer la catégorie de plat par son id et affichage au format JSON
        $categoryDish = $this->repository->findById($id);

        if (!$categoryDish) {
            ResponseHelper::json(['error' => 'Not found'], 404);
            return;
        }
        ResponseHelper::json($categoryDish);
    }
}