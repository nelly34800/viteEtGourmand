<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use App\Helper\RequestHelper;
use App\Helper\ResponseHelper;
use App\Helper\ValidatorHelper;
use Database;
use InvalidArgumentException;
use RuntimeException;
/**
 * Contrôleur responsable de la gestion des utilisateurs.
 */
class UserController
{
    private UserRepository $repository;

    public function __construct()
    {
      // Récupération de la connexion PDO et initialisation du repository
        $pdo = Database::getConnection();
        // Injection de la dépendance du repository
        $this->repository = new UserRepository($pdo);
    }
     /**
     * Liste tous les employés.
     */
    public function index(): void
    {
        // Appel du repository pour récupérer tous les employés et affichage au format JSON
        $users = $this->repository->findAllEmploye();

        $response = array_map(function($user) {
          // Transformation en array pour JSON (hydratation inverse)
            return [
                'id' => $user->getId(),
                'last_name' => $user->getLastName(),
                'first_name' => $user->getFirstName(),
                'email' => $user->getEmail(),
                'postal_address' => $user->getPostalAddress(),
                'city' => $user->getCity(),
                'postal_code' => $user->getPostalCode(),
                'phone' => $user->getPhone(),
                'id_role' => $user->getIdRole(),
            ];
        }, $users);

        ResponseHelper::json($response);
    }
       /**
     * Affiche un utilisateur par ID.
     */
    public function show(string $id): void
    {

        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour récupérer l'utilisateur par son id et affichage au format JSON
        $user = $this->repository->findById($id);

        if (!$user) {
            ResponseHelper::json(['error' => 'Not found'], 404);
        }
        // Transformation en array pour JSON (hydratation inverse)
        $response =([
            'id' => $user->getId(),
            'last_name' => $user->getLastName(),
            'first_name' => $user->getFirstName(),
            'email' => $user->getEmail(),
            'postal_address' => $user->getPostalAddress(),
            'city' => $user->getCity(),
            'postal_code' => $user->getPostalCode(),
            'phone' => $user->getPhone(),
            'id_role' => $user->getIdRole()
        ]);
        ResponseHelper::json($response);
    }
    /**
     * Crée un nouvel utilisateur.
     */
    public function store(): void
    {
        // Lecture du JSON envoyé
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        if(!isset($data['last_name'], $data['first_name'], $data['email'], $data['password'], $data['postal_address'], $data['city'], $data['postal_code'], $data['phone'], $data['id_role'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        // Création de l'entité User à partir des données reçues
        $user = new User(
            $data['last_name'],
            $data['first_name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['postal_address'],
            $data['city'],
            $data['postal_code'],
            $data['phone'],
            $data['id_role']
        );
        //  appel du repository pour l'enregistrer en base
        try {
            $this->repository->create($user);
            ResponseHelper::json(['message' => 'User created'], 201);
        } catch (\Exception $e) {
            ResponseHelper::json(['error' => 'Erreur lors de la création de l\'utilisateur', 'details' => $e->getMessage()], 500);
        }
    }
    /**
     * Met à jour un utilisateur.
     */
    public function update(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Lecture du JSON
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        if (!isset($data['first_name'], $data['last_name'], $data['email'], $data['password'], $data['postal_address'], $data['city'], $data['postal_code'], $data['phone'], $data['id_role'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        // Création de l'entité utilisateur à partir des données reçues 
        $user = new User(
            $data['last_name'],
            $data['first_name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['postal_address'],
            $data['city'],
            $data['postal_code'],
            $data['phone'],
            $data['id_role'],
            null, // roleName
            $id   // id
        );
        // appel du repository pour mettre à jour en base
        $this->repository->update($user);

        ResponseHelper::json(['message' => 'Updated']);
    }
      /**
     * Supprime un utilisateur.
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