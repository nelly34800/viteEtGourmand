<?php

namespace App\Controller;

use App\Services\MailService;
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

    const ROLE_CUSTOMER = 'client';
    const ROLE_EMPLOYEE = 'employé';

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

        $response = array_map(function(User $user) {
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
        if(!isset($data['last_name'], $data['first_name'], $data['email'], $data['password'], $data['postal_address'], $data['city'], $data['postal_code'], $data['phone'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        // autorisation : par défaut, un nouvel utilisateur est un client
        $id_role = $this->getRoleIdByName(self::ROLE_CUSTOMER);
        // Création de l'entité User à partir des données reçues
        $user = new User(
            '',
            $data['last_name'],
            $data['first_name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['postal_address'],
            $data['city'],
            $data['postal_code'],
            $data['phone'],
            $id_role
        );
        //  appel du repository pour l'enregistrer en base
        try {
            $this->repository->create($user);

            $mailService = new MailService();

            $mailService->sendWelcomeCustomerMail(
              $user->getEmail(),
              $user->getFirstName()
          );

            ResponseHelper::json(['message' => 'User created'], 201);
        } catch (\Exception $e) {
            ResponseHelper::json(['error' => 'Error creating user', 'details' => $e->getMessage()], 500);
        }
    }
    /**
     * Met à jour un utilisateur.
     */
    public function updateInfo(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // autorisation : un utilisateur ne peut modifier que son propre compte
        if ($_SESSION['user']['id'] !== $id) {
            ResponseHelper::json(['error' => 'Forbidden'], 403);
            return;
        }
        // Lecture du JSON
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        if (!isset($data['first_name'], $data['last_name'], $data['postal_address'], $data['city'], $data['postal_code'], $data['phone'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        if (isset($data['id_role'])) {
            throw new InvalidArgumentException('Role modification not allowed');
        }
        $existingUser = $this->repository->findById($id);
        // Création de l'entité utilisateur à partir des données reçues 
        $user = new User(
            $id,
            $data['last_name'],
            $data['first_name'],
            $existingUser->getEmail(),
            $existingUser->getPassword(),
            $data['postal_address'],
            $data['city'],
            $data['postal_code'],
            $data['phone'],
            $existingUser->getIdRole(),
            null, // roleName
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
        // récupérer le user à supprimer
        $userToDelete = $this->repository->findById($id);

        $currentUser = $_SESSION['user'];

        // règle d'autorisation
        $isOwner = $currentUser['id'] === $id;
        $isAdminDeletingEmployee = (
            $currentUser['role'] === 'admin' &&
            $userToDelete->getRoleName() === 'employé'
        );

    if (!$isOwner && !$isAdminDeletingEmployee) {
        ResponseHelper::json(['error' => 'Forbidden'], 403);
        return;
    }
        // Appel du repository pour supprimer l'utilisateur en base
        $this->repository->delete($id);

        ResponseHelper::json(['message' => 'Deleted']);
    }
    /**
    * Crée un nouvel employé.
    */
    public function createEmployee(): void
    {
        // Lecture du JSON envoyé
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        if(!isset($data['last_name'], $data['first_name'], $data['email'], $data['password'], $data['postal_address'], $data['city'], $data['postal_code'], $data['phone'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        // autorisation : seul un admin peut créer un employé
        $id_role = $this->getRoleIdByName(self::ROLE_EMPLOYEE);
        // Création de l'entité User à partir des données reçues
        $user = new User(
            '',
            $data['last_name'],
            $data['first_name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['postal_address'],
            $data['city'],
            $data['postal_code'],
            $data['phone'],
            $id_role
        );
        //  appel du repository pour l'enregistrer en base
        try {
            $this->repository->create($user);

             $mailService = new MailService();

            $mailService->sendWelcomeEmployeeMail(
              $user->getEmail(),
              $user->getFirstName()
            );

            ResponseHelper::json(['message' => 'Employee created'], 201);
        } catch (\Exception $e) {
            ResponseHelper::json(['error' => 'Error creating employee', 'details' => $e->getMessage()], 500);
        }
    }

    private function getRoleIdByName(string $roleName): string
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT id FROM role WHERE role_name = :name");
        $stmt->execute(['name' => $roleName]);

        $id = $stmt->fetchColumn();

        if (!$id) {
            throw new RuntimeException("Role not found");
        }

        return $id;
    }

     /**
     * Met à jour le mot de passe oublie de mot de passe
     */
    public function updatePassword(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // autorisation : un utilisateur ne peut modifier que son propre compte
        if (['Password_reset']['user_id'] !== ['user']['id']) {
            ResponseHelper::json(['error' => 'Forbidden'], 403);
            return;
        }
        // Lecture du JSON
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        if (!isset($data['password'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        $existingUser = $this->repository->findById($id);
        // Création de l'entité utilisateur à partir des données reçues 
        $user = new User(
            $id,
            $existingUser->getLastName(),
            $existingUser->getFirstName(),
            $existingUser->getEmail(),
            $data['password'],
            $existingUser->getCity(),
            $existingUser->getPostalCode(),
            $existingUser->getPhone(),
            $existingUser->getIdRole(),
            null, // roleName
        );
    }
       /**
     * Met à jour le mot de passe utilisateur connecté
     */
    public function changePassword(string $id): void
    {   
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);

        $userId = $_SESSION['user']['id'];
        // autorisation : un utilisateur ne peut modifier que son propre compte
        if ($userId !== $id) {
            ResponseHelper::json(['error' => 'Forbidden'], 403);
        }
        // Récupération des données du formulaire
        $data = RequestHelper::getJson();

        if (!isset($data['currentPassword'], $data['newPassword'])) {
            throw new InvalidArgumentException('Invalid input');
        }

        $user = $this->repository->findById($userId);

        if (!password_verify($data['currentPassword'], $user->getPassword())) {
            ResponseHelper::json(['error' => 'Ancien mot de passe incorrect'], 400);
        }

        $hashedPassword = password_hash($data['newPassword'], PASSWORD_DEFAULT);

        $this->repository->updatePassword($userId, $hashedPassword);

        ResponseHelper::json(['message' => 'Mot de passe modifié']);
    }
}