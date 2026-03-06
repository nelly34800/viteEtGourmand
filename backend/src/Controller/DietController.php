<?php

namespace App\Controller;

use App\Repository\DietRepository;
use App\Entity\Diet;
use App\Helper\RequestHelper;
use App\Helper\ResponseHelper;
use App\Helper\ValidatorHelper;
use Database;
use InvalidArgumentException;
use RuntimeException;
/**
 * Contrôleur responsable de la gestion des régimes alimentaires.
 */
class DietController
{
    private DietRepository $repository;

    public function __construct()
    {
      // Récupération de la connexion PDO et initialisation du repository
        $pdo = Database::getConnection();
        // Injection de la dépendance du repository
        $this->repository = new DietRepository($pdo);
    }
     /**
     * Liste tous les régimes alimentaires.
     */
    public function index(): void
    {
        // Appel du repository pour récupérer tous les régimes alimentaires et affichage au format JSON
        $diets = $this->repository->findAll();

        $response = array_map(function($diet) {
          // Transformation en array pour JSON (hydratation inverse)
            return [
                'id' => $diet->getId(),
                'diet_name' => $diet->getDietName(),
            ];
        }, $diets);

        ResponseHelper::json($response);
    }
       /**
     * Affiche un régime alimentaire par ID.
     */
    public function show(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour récupérer le régime alimentaire par son id et affichage au format JSON
        $diet = $this->repository->findById($id);

        if (!$diet) {
            ResponseHelper::json(['error' => 'Not found'], 404);
        }
        // Transformation en array pour JSON (hydratation inverse)
        $response =([
            'id' => $diet->getId(),
            'diet_name' => $diet->getDietName()
        ]);
        ResponseHelper::json($response);
    }
    /**
     * Crée un nouveau régime alimentaire.
     */
    public function store(): void
    {
        // Lecture du JSON envoyé
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        if(!isset($data['diet_name'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        // Création de l'entité Diet à partir des données reçues
        $diet = new Diet(
            '',
            $data['diet_name']
        );
        //  appel du repository pour l'enregistrer en base
        $this->repository->create($diet);
        ResponseHelper::json(['message' => 'Diet created'], 201);
    }
    /**
     * Met à jour un régime alimentaire.
     */
    public function update(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Lecture du JSON
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        if (!isset($data['diet_name'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        // Création de l'entité Diet à partir des données reçues 
        $diet = new Diet(
            $id,
            $data['diet_name']
   );
        // appel du repository pour mettre à jour en base
        $this->repository->update($diet);
        ResponseHelper::json(['message' => 'Updated']);
    }
      /**
     * Supprime un régime alimentaire.
     */
    public function delete(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour supprimer le régime alimentaire en base
        $this->repository->delete($id);

        ResponseHelper::json(['message' => 'Deleted']);
    }
}
