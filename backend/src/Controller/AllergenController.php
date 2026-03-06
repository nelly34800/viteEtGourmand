<?php

namespace App\Controller;

use App\Repository\AllergenRepository;
use App\Entity\Allergen;
use App\Helper\RequestHelper;
use App\Helper\ResponseHelper;
use App\Helper\ValidatorHelper;
use Database;
use InvalidArgumentException;
use RuntimeException;
/**
 * Contrôleur responsable de la gestion des allergènes.
 */
class AllergenController
{
    private AllergenRepository $repository;

    public function __construct()
    {
      // Récupération de la connexion PDO et initialisation du repository
        $pdo = Database::getConnection();
        // Injection de la dépendance du repository
        $this->repository = new AllergenRepository($pdo);
    }
     /**
     * Liste tous les allergènes.
     */
    public function index(): void
    {
        // Appel du repository pour récupérer tous les allergènes et affichage au format JSON
        $allergens = $this->repository->findAll();

        $response = array_map(function($allergen) {
          // Transformation en array pour JSON (hydratation inverse)
            return [
                'id' => $allergen->getId(),
                'allergen_name' => $allergen->getAllergenName(),
            ];
        }, $allergens);

        ResponseHelper::json($response);
    }
       /**
     * Affiche un allergène par ID.
     */
    public function show(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour récupérer l'allergène par son id et affichage au format JSON
        $allergen = $this->repository->findById($id);

        if (!$allergen) {
            ResponseHelper::json(['error' => 'Not found'], 404);
        }
        // Transformation en array pour JSON (hydratation inverse)
        $response =([
            'id' => $allergen->getId(),
            'allergen_name' => $allergen->getAllergenName()
        ]);
        ResponseHelper::json($response);
    }
    /**
     * Crée un nouveau allergène.
     */
    public function store(): void
    {
        // Lecture du JSON envoyé
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        if(!isset($data['allergen_name'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        // Création de l'entité Allergen à partir des données reçues
        $allergen = new Allergen(
            '',
            $data['allergen_name']
        );
        //  appel du repository pour l'enregistrer en base
        $this->repository->create($allergen);
        ResponseHelper::json(['message' => 'Allergen created'], 201);
    }
    /**
     * Met à jour un allergène.
     */
    public function update(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Lecture du JSON
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        if (!isset($data['allergen_name'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        // Création de l'entité Allergen à partir des données reçues 
        $allergen = new Allergen(
            $id,
            $data['allergen_name']
        );
        // appel du repository pour mettre à jour en base
        $this->repository->update($allergen);
        ResponseHelper::json(['message' => 'Updated']);
    }
      /**
     * Supprime un allergène.
     */
    public function delete(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour supprimer l'allergène en base
        $this->repository->delete($id);

        ResponseHelper::json(['message' => 'Deleted']);
    }
}
