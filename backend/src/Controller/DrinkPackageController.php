<?php

namespace App\Controller;

use App\Repository\DrinkPackageRepository;
use App\Entity\DrinkPackage;
use App\Helper\RequestHelper;
use App\Helper\ResponseHelper;
use App\Helper\ValidatorHelper;
use Database;
use InvalidArgumentException;
use RuntimeException;
/**
 * Contrôleur responsable de la gestion des forfaits boissons.
 */
class DrinkPackageController
{
    private DrinkPackageRepository $repository;

    public function __construct()
    {
      // Récupération de la connexion PDO et initialisation du repository
        $pdo = Database::getConnection();
        // Injection de la dépendance du repository
        $this->repository = new DrinkPackageRepository($pdo);
    }
     /**
     * Liste tous les forfaits boissonss.
     */
    public function index(): void
    {
        // Appel du repository pour récupérer tous les forfaits boissons et affichage au format JSON
        $drinkPackages = $this->repository->findAll();

        $response = array_map(function($drinkPackage) {
          // Transformation en array pour JSON (hydratation inverse)
            return [
                'id' => $drinkPackage->getId(),
                'drink_package_name' => $drinkPackage->getDrinkPackageName(),
                'price_per_person' => $drinkPackage->getPricePerPerson(),
            ];
        }, $drinkPackages);

        ResponseHelper::json($response);
    }
       /**
     * Affiche un forfait boissons par ID.
     */
    public function show(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour récupérer l'forfait boissons par son id et affichage au format JSON
        $drinkPackage = $this->repository->findById($id);

        if (!$drinkPackage) {
            ResponseHelper::json(['error' => 'Not found'], 404);
        }
        // Transformation en array pour JSON (hydratation inverse)
        $response =([
            'id' => $drinkPackage->getId(),
            'drink_package_name' => $drinkPackage->getDrinkPackageName(),
            'price_per_person' => $drinkPackage->getPricePerPerson(),
        ]);
        ResponseHelper::json($response);
    }
    /**
     * Crée un nouveau forfait boissons.
     */
    public function store(): void
    {
        // Lecture du JSON envoyé
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        if(!isset($data['drink_package_name'], $data['price_per_person'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        // Création de l'entité DrinkPackage à partir des données reçues
        $drinkPackage = new DrinkPackage(
            '',
            $data['drink_package_name'],
            $data['price_per_person']
        );
        //  appel du repository pour l'enregistrer en base
        $this->repository->create($drinkPackage);
        ResponseHelper::json(['message' => 'DrinkPackage created'], 201);
    }
    /**
     * Met à jour un forfait boissons.
     */
    public function update(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Lecture du JSON
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        if (!isset($data['drink_package_name'], $data['price_per_person'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        // Création de l'entité DrinkPackage à partir des données reçues 
        $drinkPackage = new DrinkPackage(
            $id,
            $data['drink_package_name'],
            $data['price_per_person']
        );
        // appel du repository pour mettre à jour en base
        $this->repository->update($drinkPackage);
        ResponseHelper::json(['message' => 'Updated']);
    }
      /**
     * Supprime un forfait boissons.
     */
    public function delete(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour supprimer l'forfait boissons en base
        $this->repository->delete($id);

        ResponseHelper::json(['message' => 'Deleted']);
    }
}