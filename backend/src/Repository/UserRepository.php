<?php

namespace App\Repository;

use PDO;
use App\Entity\User;
use RuntimeException;
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

    private function hydrate(array $data): User
    {
        // Transforme le tableau de la bdd en une instance de User (objet)
        return new User(
            $data['last_name'],
            $data['first_name'],
            $data['email'],
            $data['password'],
            $data['postal_address'],
            $data['city'],
            $data['postal_code'],
            $data['phone'],
            $data['id_role'],
            $data['role_name'],
            $data['id']
        );
    }
    /**
     * Retourne un  tableau tous les utilisateur qui ont le rôle employe.
     *
     * @return array
     */
    public function findAllEmploye(): array
    {
        $stmt = $this->pdo->query("SELECT user.*, role.role_name FROM user JOIN role ON user.id_role = role.id WHERE role.role_name = 'employé'");
         $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $users = [];

        foreach ($results as $row) {
            $users[] = $this->hydrate($row);
        }

        return $users;
    }
    /**
     * Retourne un utilisateur par son ID.
     */
    public function findById(string $id): User
    {
        $stmt = $this->pdo->prepare("SELECT user.*, role.role_name FROM user JOIN role ON user.id_role = role.id WHERE user.id = ?");
        $stmt->execute([$id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            // Si aucun résultat n'est trouvé lance une exception
            throw new RuntimeException('User not found');
        }

        return $this->hydrate($result);
    }
    /**
     * Insère un nouvel utilisateur.
     */
    public function create(User $user): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO user (last_name, first_name, email, password, postal_address, city, postal_code, phone, id_role)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        // exécute la requête avec les données de l'utilisateur
        $stmt->execute([
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
        // récupère l'id généré par la base de données pour le nouvel utilisateur
        $id = $this->pdo->query("SELECT id FROM user WHERE email = " . $this->pdo->quote($user->getEmail()))->fetchColumn();
        $user->setId($id);
        }
    /**
     * Met à jour un user existant.
     */
    public function update(User $user): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE user
            SET last_name = ?, first_name = ?, email = ?, password = ?, postal_address = ?, city = ?, postal_code = ?, phone = ?
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
            $user->getId()
        ]);
        // Si l'utilisateur n'existe pas retourne une erreur
        if ($stmt->rowCount() === 0) {
            ResponseHelper::json(['debug' => 'rowcount 0'], 200);
        }
    }
    /**
     * Supprime un utilisateur.
     */
    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM user WHERE id = ?");
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
        $stmt = $this->pdo->prepare("SELECT *, role.role_name FROM user JOIN role ON user.id_role = role.id WHERE email = ?");
        $stmt->execute([$email]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            // Si aucun résultat n'est trouvé lance une exception
            throw new RuntimeException('User not found');
        }

        return $this->hydrate($result);
    }
}
