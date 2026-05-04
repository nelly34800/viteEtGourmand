<?php

namespace App\Repository;

use PDO;
use App\Entity\DrinkPackage;

use RuntimeException;
use Ramsey\Uuid\Uuid;

class DrinkPackageRepository
{    private PDO $pdo;

    /**
     * Injection de la dépendance PDO.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function mapRowToDrinkPackage(array $row): DrinkPackage
    {
        // Transforme une ligne sql de la table `drink_package` en objet DrinkPackage
        return new DrinkPackage(
            $row['id'],
            $row['drink_package_name'],
            $row['price_per_person'],
        );
    }
    /**
     * Retourne un tableau de tous les forfaits boissons.
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT id, drink_package_name, price_per_person FROM drink_package");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $drinkPackages = [];

        foreach ($rows as $row) {
            $drinkPackages[] = $this->mapRowToDrinkPackage($row);
        }

        return $drinkPackages;
    }
    /**
     * Retourne un forfaits boissons par son ID.
     */
    public function findById(string $id): DrinkPackage
    {
        $stmt = $this->pdo->prepare("SELECT id, drink_package_name, price_per_person FROM drink_package WHERE id = ?");
        $stmt->execute([$id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            // Si aucun résultat n'est trouvé lance une exception
            throw new RuntimeException('DrinkPackage not found');
        }

        return $this->mapRowToDrinkPackage($row);
    }
    /**
     * Insère un nouveau forfait de boisson.
     */
    public function create(DrinkPackage $drinkPackage): void
    {
        // Génération UUID côté PHP
        $drinkPackageId = Uuid::uuid4()->toString();
        $drinkPackage->setId($drinkPackageId);

        $stmt = $this->pdo->prepare("
            INSERT INTO drink_package (id, drink_package_name, price_per_person)
            VALUES (?, ?, ?)
        ");
        // exécute la requête avec les données du forfaits boissons
        $stmt->execute([
            $drinkPackage->getId(),
            $drinkPackage->getDrinkPackageName(),
            $drinkPackage->getPricePerPerson()
        ]);
    }
    /**
     * Met à jour un forfait boissons existant.
     */
    public function update(DrinkPackage $drinkPackage): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE drink_package
            SET drink_package_name = ?, price_per_person = ?
            WHERE id = ?
        ");
        // exécute la requête avec les données du forfaits boissons
        $stmt->execute([
            $drinkPackage->getDrinkPackageName(),
            $drinkPackage->getPricePerPerson(),
            $drinkPackage->getId()
        ]);
        // Si le forfait boissons n'existe pas retourne une erreur
        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('DrinkPackage not found');
        }
    }
    /**
     * Supprime un forfait boissons.
     */
    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM drink_package WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('DrinkPackage not found');
        }
    }
    /**
     * Retourne un tableau de forfaits boissons par leurs IDS.
     */
    public function findByIds(array $ids): array {
        // Vérifie si l'array est vide
        if (empty($ids)) return [];
        // crée autant de ? qu’il y a d’IDs pour sécuriser la requête SQL
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $stmt = $this->pdo->prepare(
            "SELECT id,
                drink_package_name,
                minimum_people,
                price_per_person
            FROM drink_package WHERE id IN ($placeholders)"
          );

        $stmt->execute($ids);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}