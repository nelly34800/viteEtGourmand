<?php

namespace App\Repository;

use PDO;
use App\Entity\PersonalPackage;
use RuntimeException;
use Ramsey\Uuid\Uuid;

class PersonalPackageRepository
{    private PDO $pdo;

    /**
     * Injection de la dépendance PDO.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function mapRowToPersonalPackage(array $row): PersonalPackage
    {
        // Transforme une ligne sql de la table `personal_package` en objet PersonalPackage
        return new PersonalPackage(
            $row['id'],
            $row['event_type'],
            $row['staff_ratio'],
            $row['package_price'],
        );
    }
    /**
     * Retourne un tableau de tous les forfaits personnel.
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT id, event_type, staff_ratio, package_price FROM personal_package");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $personalPackages = [];

        foreach ($rows as $row) {
            $personalPackages[] = $this->mapRowToPersonalPackage($row);
        }

        return $personalPackages;
    }
    /**
     * Retourne un forfait personnel par son ID.
     */
    public function findById(string $id): PersonalPackage
    {
        $stmt = $this->pdo->prepare("SELECT id, event_type, staff_ratio, package_price FROM personal_package WHERE id = ?");
        $stmt->execute([$id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            // Si aucun résultat n'est trouvé lance une exception
            throw new RuntimeException('PersonalPackage not found');
        }

        return $this->mapRowToPersonalPackage($row);
    }
    /**
     * Insère un nouveau forfait de personnel.
     */
    public function create(PersonalPackage $personalPackage): void
    {
        // Génération UUID côté PHP
        $personalPackageId = Uuid::uuid4()->toString();
        $personalPackage->setId($personalPackageId);

        $stmt = $this->pdo->prepare("
            INSERT INTO personal_package (id, event_type, staff_ratio, package_price)
            VALUES (?, ?, ?, ?)
        ");
        // exécute la requête avec les données du forfait personnel
        $stmt->execute([
            $personalPackage->getId(),
            $personalPackage->getEventType(),
            $personalPackage->getStaffRatio(),
            $personalPackage->getPackagePrice()
        ]);
    }
    /**
     * Met à jour un forfait personnel existant.
     */
    public function update(PersonalPackage $personalPackage): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE personal_package
            SET event_type = ?, staff_ratio = ?, package_price = ?
            WHERE id = ?
        ");
        // exécute la requête avec les données du forfait personnel
        $stmt->execute([
            $personalPackage->getEventType(),
            $personalPackage->getStaffRatio(),
            $personalPackage->getPackagePrice(),
            $personalPackage->getId()
        ]);
        // Si le forfait personnel n'existe pas retourne une erreur
        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('PersonalPackage not found');
        }
    }
    /**
     * Supprime un forfait personnel.
     */
    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM personal_package WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('PersonalPackage not found');
        }
    }
    /**
     * Retourne un tableau de forfaits de personnel par leurs IDS.
     */
    public function findByIds(array $ids): array {
        // Vérifie si l'array est vide
        if (empty($ids)) return [];
        // crée autant de ? qu’il y a d’IDs pour sécuriser la requête SQL
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $stmt = $this->pdo->prepare(
            "SELECT id,
                event_type,
                staff_ratio,
                package_price
            FROM personal_package WHERE id IN ($placeholders)"
          );

        $stmt->execute($ids);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}