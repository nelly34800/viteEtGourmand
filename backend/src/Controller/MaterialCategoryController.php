<?php

namespace App\Controller;

use App\Repository\MaterialCategoryRepository;
use App\Helper\ResponseHelper;
use App\Helper\ValidatorHelper;
use Database;
/**
 * Contrôleur responsable de la gestion des catégories de matériel.
 */
class MaterialCategoryController
{
    private MaterialCategoryRepository $repository;

    public function __construct()
    {
      // Récupération de la connexion PDO et initialisation du repository
        $pdo = Database::getConnection();
        // Injection de la dépendance du repository
        $this->repository = new MaterialCategoryRepository($pdo);
    }
     /**
     * Liste tous les catégories de matériel.
     */
    public function index(): void
    {
        // Appel du repository pour récupérer toutes les catégories de matériel et affichage au format JSON
        $materialCategories = $this->repository->findAll();
        ResponseHelper::json($materialCategories);
    }
       /**
     * Affiche une catégorie de matériel par ID.
     */
    public function show(string $id): void
    {

        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour récupérer la catégorie de matériel par son id et affichage au format JSON
        $materialCategory = $this->repository->findById($id);

        if (!$materialCategory) {
            ResponseHelper::json(['error' => 'Not found'], 404);
            return;
        }
        ResponseHelper::json($materialCategory);
    }
}