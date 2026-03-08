<?php

namespace App\Controller;

use App\Repository\MaterialRepository;
use App\Entity\Material;
use App\Helper\RequestHelper;
use App\Helper\ResponseHelper;
use App\Helper\ValidatorHelper;
use Database;
use InvalidArgumentException;
use RuntimeException;
/**
 * Contrôleur responsable de la gestion du matériel.
 */
class MaterialController
{
    private MaterialRepository $repository;

    public function __construct()
    {
      // Récupération de la connexion PDO et initialisation du repository
        $pdo = Database::getConnection();
        // Injection de la dépendance du repository
        $this->repository = new MaterialRepository($pdo);
    }
     /**
     * Liste tous le matériel.
     */
    public function index(): void
    {
        // Appel du repository pour récupérer tous le matériel et affichage au format JSON
        $materials = $this->repository->findAll();

        $response = array_map(function(Material $material) {
          // Transformation en array pour JSON (hydratation inverse)
            return [
                'id' => $material->getId(),
                'material_name' => $material->getMaterialName(),
                'quantity_available' => $material->getQuantityAvailable(),
                'price' => $material->getPrice(),
                'id_material_category' => $material->getIdMaterialCategory()
            ];
        }, $materials);

        ResponseHelper::json($response);
    }
       /**
     * Affiche un matériel par ID.
     */
    public function show(string $id): void
    {

        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour récupérer le matériel par son id et affichage au format JSON
        $material = $this->repository->findById($id);

        if (!$material) {
            ResponseHelper::json(['error' => 'Not found'], 404);
        }
            // Transformation en array pour JSON (hydratation inverse)
            $response =([
                'id' => $material->getId(),
                'material_name' => $material->getMaterialName(),
                'quantity_available' => $material->getQuantityAvailable(),
                'price' => $material->getPrice(),
                'id_material_category' => $material->getIdMaterialCategory()
            ]);
            ResponseHelper::json($response);
    }
    /**
     * Crée un nouveau matériel.
     */
public function store(): void
{

    $data = RequestHelper::getJson();
    // Validation des champs obligatoires
    if(!isset($data['material_name'], $data['quantity_available'], $data['price'], $data['id_material_category'])) {
        throw new InvalidArgumentException('Invalid input');
    }
    // Création de l'entité material à partir des données reçues
    $material = new Material(
        '', // l'UUID sera généré côté repository
        $data['material_name'],
        $data['quantity_available'],
        $data['price'],
        $data['id_material_category'],
    );
        //  appel du repository pour l'enregistrer en base
        try {
            $this->repository->create($material);
            ResponseHelper::json(['message' => 'material created'], 201);

        } catch (\Exception $e) {
            ResponseHelper::json(['error' => 'Error during material creation', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Met à jour un matériel.
     */
    public function update(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Lecture du JSON
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        if(!isset($data['material_name'], $data['quantity_available'], $data['price'], $data['id_material_category'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        // Création de l'entité matériel à partir des données reçues 
        $material = new Material(
            $id,
            $data['material_name'],
            $data['quantity_available'],
            $data['price'],
            $data['id_material_category'],
            null, //material_category_name

        );
        // appel du repository pour mettre à jour en base
        $this->repository->update($material);

        ResponseHelper::json(['message' => 'Updated']);
    }
      /**
     * Supprime un matériel.
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
