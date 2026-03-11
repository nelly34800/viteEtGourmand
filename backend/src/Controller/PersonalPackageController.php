<?php

namespace App\Controller;

use App\Repository\PersonalPackageRepository;
use App\Entity\PersonalPackage;
use App\Helper\RequestHelper;
use App\Helper\ResponseHelper;
use App\Helper\ValidatorHelper;
use Database;
use InvalidArgumentException;
use RuntimeException;
/**
 * Contrôleur responsable de la gestion des forfaits personnel.
 */
class PersonalPackageController
{
    private PersonalPackageRepository $repository;

    public function __construct()
    {
      // Récupération de la connexion PDO et initialisation du repository
        $pdo = Database::getConnection();
        // Injection de la dépendance du repository
        $this->repository = new PersonalPackageRepository($pdo);
    }
     /**
     * Liste tous les forfaits personnel.
     */
    public function index(): void
    {
        // Appel du repository pour récupérer tous les forfaits personnel et affichage au format JSON
        $personalPackages = $this->repository->findAll();

        $response = array_map(function($personalPackage) {
          // Transformation en array pour JSON (hydratation inverse)
            return [
                'id' => $personalPackage->getId(),
                'event_type' => $personalPackage->getEventType(),
                'staff_ratio' => $personalPackage->getStaffRatio(),
                'package_price' => $personalPackage->getPackagePrice(),
            ];
        }, $personalPackages);

        ResponseHelper::json($response);
    }
       /**
     * Affiche un forfait personnel par ID.
     */
    public function show(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour récupérer le forfait personnel par son id et affichage au format JSON
        $personalPackage = $this->repository->findById($id);

        if (!$personalPackage) {
            ResponseHelper::json(['error' => 'Not found'], 404);
        }
        // Transformation en array pour JSON (hydratation inverse)
        $response =([
            'id' => $personalPackage->getId(),
            'event_type' => $personalPackage->getEventType(),
            'staff_ratio' => $personalPackage->getStaffRatio(),
            'package_price' => $personalPackage->getPackagePrice(),
        ]);
        ResponseHelper::json($response);
    }
    /**
     * Crée un nouveau forfait personnel.
     */
    public function store(): void
    {
        // Lecture du JSON envoyé
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        if(!isset($data['event_type'], $data['staff_ratio'],  $data['package_price'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        // Création de l'entité PersonalPackage à partir des données reçues
        $personalPackage = new PersonalPackage(
            '',
            $data['event_type'],
            $data['staff_ratio'],
            $data['package_price']
        );
        //  appel du repository pour l'enregistrer en base
        $this->repository->create($personalPackage);
        ResponseHelper::json(['message' => 'PersonalPackage created'], 201);
    }
    /**
     * Met à jour un forfait personnel.
     */
    public function update(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Lecture du JSON
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        if (!isset($data['event_type'], $data['staff_ratio'],  $data['package_price'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        // Création de l'entité PersonalPackage à partir des données reçues 
        $personalPackage = new PersonalPackage(
            $id,
            $data['event_type'],
            $data['staff_ratio'],
            $data['package_price']
        );
        // appel du repository pour mettre à jour en base
        $this->repository->update($personalPackage);
        ResponseHelper::json(['message' => 'Updated']);
    }
      /**
     * Supprime un forfait personnel.
     */
    public function delete(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour supprimer le forfait personnel en base
        $this->repository->delete($id);

        ResponseHelper::json(['message' => 'Deleted']);
    }
}