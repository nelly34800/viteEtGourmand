<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\AuthRepository;
use App\Helper\RequestHelper;
use App\Helper\ResponseHelper;
use App\Helper\ValidatorHelper;
use App\Helper\CsrfHelper;
use Database;
use RuntimeException;

class AuthController
{
    private UserRepository $userRepository;
    private AuthRepository $authRepository;

    public function __construct()
    {
      // Récupération de la connexion PDO et initialisation du repository
        $pdo = Database::getConnection();
        // Injection de la dépendance du repository
        $this->userRepository = new UserRepository($pdo);
        $this->authRepository = new AuthRepository($pdo);
    }
    /**
     * connexion d'un utilisateur.
     */
    public function login(): void
    {
        // Lecture des données du JSON envoyé
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        try {
            //récupérer l'utilisateur par email
            $user = $this->userRepository->findByEmail($email);
            // Vérifier le mot de passe
            if (!$user || !$user->verifyPassword($password)) {
                throw new RuntimeException();
            }
            // protection session fixation
            session_regenerate_id(true);


            $_SESSION['user'] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'role' => $user->getRoleName()
            ];

            ResponseHelper::json(['message' => 'Login successful','csrf_token' => CsrfHelper::generate(),
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'role' => $user->getRoleName()
            ]]);

        } catch (RuntimeException $e) {
            ResponseHelper::json(['error' => 'Incorrect username or password'], 401);
        }
    }
    /**
     * Logout d'un utilisateur.
     */
    public function logout(): void
    {

        $_SESSION = [];  // vide toutes les variables de session
        session_destroy();  // supprime la session côté serveur

        ResponseHelper::json(['message' => 'Logout successful']);
    }
}