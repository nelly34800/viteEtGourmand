<?php

namespace App\Controller;

use App\Services\MailService;
use App\Repository\PasswordResetRepository;
use App\Entity\PasswordReset;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Helper\RequestHelper;
use App\Helper\ResponseHelper;
use App\Helper\ValidatorHelper;
use Database;
use InvalidArgumentException;
use RuntimeException;

class PasswordResetController
{
    private PasswordResetRepository $passwordResetRepository;
    private UserRepository $userRepository;

    public function __construct()
    {
      // Récupération de la connexion PDO et initialisation du repository
        $pdo = Database::getConnection();
        // Injection de la dépendance du repository
        $this->passwordResetRepository = new PasswordResetRepository($pdo);
        $this->userRepository = new UserRepository($pdo);
    }
    /**
     * Crée un nouveau token temporaire.
     */
    public function store(): void
    {
        // Lecture du JSON envoyé
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        if(!isset($data['email'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        // Récupération de l'utilisateur
        $user = $this->userRepository->findByEmail($data['email']);
         if (!$user) {
          // message volontairement vague pour éviter de tester mail jusqu'a en trouver un qui fonctionne
            ResponseHelper::json(['message' => 'Si un compte existe, un email a été envoyé'], 200);
            return;
        }
        // généré un token 
        $token = bin2hex(random_bytes(32));
        // création d'un objet PasswordReset
        $passwordReset = new PasswordReset(
            \Ramsey\Uuid\Uuid::uuid4()->toString(),
            $token,
            date('Y-m-d H:i:s', strtotime('+1 hour')),
            $user->getId()
        );
        // appel du repository pour l'enregistrer en base
        $this->passwordResetRepository->create($passwordReset);

        // création d'un objet mailService
        $mailService = new MailService();
        // appel de la fonction pour envoyer mail de réinitialisation de mot de passe
        $mailService->sendResetPasswordMail(
              $user->getEmail(),
              $user->getFirstName(),
              $token
        );

        ResponseHelper::json(['message' => 'Token created'], 201);
    }
    // méthode pour récupérer le mot de passe modifié
    public function updatePassword(): void
    {
        $data = RequestHelper::getJson();

        if (!isset($data['token'], $data['password'])) {
            throw new InvalidArgumentException('Invalid input');
        }

        // vérifier token
        $reset = $this->passwordResetRepository
            ->findValidToken($data['token']);

        if (!$reset) {
            ResponseHelper::json(['error' => 'Invalid or expired token'], 400);
        }

        // hash password
        $hashedPassword = password_hash(
          $data['password'],PASSWORD_DEFAULT
        );

        // update user password
        $this->userRepository->updatePassword(
            $reset['id_user'],$hashedPassword
        );

        // supprimer token
        $this->passwordResetRepository
            ->deleteToken($data['token']);

        ResponseHelper::json(['message' => 'Password updated']);
    }
}