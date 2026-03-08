<?php

namespace App\Controller;

use App\Repository\ConditionRepository;
use App\Entity\Condition;
use App\Helper\RequestHelper;
use App\Helper\ResponseHelper;
use App\Helper\ValidatorHelper;
use Database;
use InvalidArgumentException;
use RuntimeException;
/**
 * Contrôleur responsable de la gestion des conditions du menu.
 */
class ConditionController
{
    private ConditionRepository $repository;

    public function __construct()
    {
      // Récupération de la connexion PDO et initialisation du repository
        $pdo = Database::getConnection();
        // Injection de la dépendance du repository
        $this->repository = new ConditionRepository($pdo);
    }
     /**
     * Liste toutes les conditions du menu.
     */
    public function index(): void
    {
      // Appel du repository pour récupérer toutes les conditions du menu et affichage au format JSON
        $conditions = $this->repository->findAll();

        $response = array_map(function($condition) {
          // Transformation en array pour JSON (hydratation inverse)
            return [
                'id' => $condition->getId(),
                'condition_type' => $condition->getConditionType(),
                'description' => $condition->getDescription()
            ];
        }, $conditions);

        ResponseHelper::json($response);
    }
       /**
     * Affiche une condition par ID.
     */
    public function show(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour récupérer la condition par son id et affichage au format JSON
        $condition = $this->repository->findById($id);

        if (!$condition) {
            ResponseHelper::json(['error' => 'Not found'], 404);
        }
        // Transformation en array pour JSON (hydratation inverse)
        $response =([
            'id' => $condition->getId(),
            'condition_type' => $condition->getConditionType(),
            'description' => $condition->getDescription()
        ]);
        ResponseHelper::json($response);
    }
    /**
     * Crée une nouvelle condition.
     */
    public function store(): void
    {
        // Lecture du JSON envoyé
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        if(!isset($data['condition_type'], $data['description'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        // Création de l'entité Condition à partir des données reçues
        $condition = new Condition(
            '',
            $data['condition_type'],
            $data['description']
        );
        //  appel du repository pour l'enregistrer en base
        $this->repository->create($condition);
        ResponseHelper::json(['message' => 'Condition created'], 201);
    }
    /**
     * Met à jour une condition.
     */
    public function update(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Lecture du JSON
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        if (!isset($data['condition_type'], $data['description'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        // Création de l'entité Allergen à partir des données reçues 
        $condition = new Condition(
            $id,
            $data['condition_type'],
            $data['description']
        );
        // appel du repository pour mettre à jour en base
        $this->repository->update($condition);
        ResponseHelper::json(['message' => 'Updated']);
    }
      /**
     * Supprime une condition.
     */
    public function delete(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour supprimer la condition en base
        $this->repository->delete($id);

        ResponseHelper::json(['message' => 'Deleted']);
    }
}
