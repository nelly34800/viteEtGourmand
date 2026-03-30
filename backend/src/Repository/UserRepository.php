<?php

namespace App\Repository;

use PDO;
use App\Entity\User;
use RuntimeException;
use Ramsey\Uuid\Uuid;
/**
 * Repository responsable de l'accès aux données
 * pour l'entité User.
 */
class UserRepository
{
    private PDO $pdo;
    /**
     * Injection de la dépendance PDO. 
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function mapRowToUser(array $row): User
    {
        // transforme une ligne SQL de la table `user` en objet User
        return new User(
            $row['user_id'],
            $row['last_name'],
            $row['first_name'],
            $row['email'],
            $row['password'],
            $row['postal_address'],
            $row['city'],
            $row['postal_code'],
            $row['phone'],
            $row['role_id'],
            $row['role_name'] ?? null
        );
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM `user` WHERE email = ?");
        $stmt->execute([$email]);

        return (bool) $stmt->fetch();
    }
    /**
     * Retourne un tableau de tous les utilisateur qui ont le rôle employe.
     */
    public function findAllEmploye(): array
    {
        $stmt = $this->pdo->query
        ("SELECT
            `user`.id AS user_id,
            `user`.last_name,
            `user`.first_name,
            `user`.email,
            `user`.password,
            `user`.postal_address,
            `user`.city,
            `user`.postal_code,
            `user`.phone,
            role.id AS role_id,
            role.role_name 
         FROM user 
         JOIN `role` ON user.id_role = role.id 
         WHERE role.role_name = 'employé'");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $users = [];
        foreach ($rows as $row) {
            $users[] = $this->mapRowToUser($row);
        }
        return $users;
    }
    /**
     * Retourne un utilisateur par son ID.
     */
    public function findById(string $id): User
    {
        $stmt = $this->pdo->prepare
        ("SELECT
            `user`.id AS user_id,
            `user`.last_name,
            `user`.first_name,
            `user`.email,
            `user`.password,
            `user`.postal_address,
            `user`.city,
            `user`.postal_code,
            `user`.phone,
            role.id AS role_id,
            role.role_name 
        FROM user 
        JOIN role ON user.id_role = role.id 
        WHERE user.id = ?");
        $stmt->execute([$id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            // Si aucun résultat n'est trouvé lance une exception
            throw new RuntimeException('User not found');
        }
        return $this->mapRowToUser($row);
    }
    /**
     * Insère un nouvel utilisateur.
     */
    public function create(User $user): void
    {
        if ($this->emailExists($user->getEmail())) {
            throw new RuntimeException("Email already used");
        }
        // Génération UUID côté PHP
        $userId = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $user->setId($userId);
        // insertion de l'utilisateur
        $stmt = $this->pdo->prepare("
            INSERT INTO `user` 
            (id, last_name, first_name, email, password, postal_address, city, postal_code, phone, id_role)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $user->getId(),
            $user->getLastName(),
            $user->getFirstName(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getPostalAddress(),
            $user->getCity(),
            $user->getPostalCode(),
            $user->getPhone(),
            $user->getIdRole()
        ]);
    }
    /**
     * Met à jour un user existant.
     */
    public function update(User $user): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE `user`
            SET last_name = ?, first_name = ?, email = ?, password = ?, postal_address = ?, city = ?, postal_code = ?, phone = ?, id_role = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $user->getLastName(),
            $user->getFirstName(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getPostalAddress(),
            $user->getCity(),
            $user->getPostalCode(),
            $user->getPhone(),
            $user->getIdRole(),
            $user->getId()

        ]);
        // Si l'utilisateur n'existe pas retourne une erreur
        if ($stmt->rowCount() === 0) {
            throw new RuntimeException("User not found");
      }
    }
    /**
     * Supprime un utilisateur.
     */
    public function delete(string $id): void
    {
      //supprime l'utilisateur
        $stmt = $this->pdo->prepare("DELETE FROM `user` WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('User not found');
        }
    }
     /**
     * Retourne un utilisateur par son email.
     */
    public function findByEmail(string $email): User
    {
        $stmt = $this->pdo->prepare
        ("SELECT 
            `user`.id AS user_id,
            `user`.last_name,
            `user`.first_name,
            `user`.email,
            `user`.password,
            `user`.postal_address,
            `user`.city,
            `user`.postal_code,
            `user`.phone,
            role.id AS role_id,
            role.role_name 
        FROM `user` 
        JOIN role ON user.id_role = role.id 
        WHERE email = ?");
        $stmt->execute([$email]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            // Si aucun résultat n'est trouvé lance une exception
            throw new RuntimeException('User not found');
        }
        return $this->mapRowToUser($result);
    }
}