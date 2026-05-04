<?php

namespace App\Repository;

use PDO;
use App\Entity\Material;
use RuntimeException;
use Ramsey\Uuid\Uuid;
/**
 * Repository responsable de l'accès aux données
 * pour l'entité Material.
 */
class MaterialRepository
{
    private PDO $pdo;

    /**
     * Injection de la dépendance PDO.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function mapRowToMaterial(array $row): Material
    {
    // transforme une ligne SQL de la table `material` en objet Material
        return new Material(
            $row['id'],
            $row['material_name'],
            $row['quantity_available'],
            $row['price'],
            $row['id_material_category'],
            $row['material_category_name'] ?? null
        );
    }
    /**
     * Retourne un tableau tous le matériel
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query
            ("SELECT 
                material.id,
                material.material_name,
                material.quantity_available,
                material.price,
                material.id_material_category,
                material_category.material_category_name
            FROM material
            LEFT JOIN material_category ON material.id_material_category = material_category.id");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $materials = [];
        foreach ($rows as $row) {
            $materials[] = $this->mapRowToMaterial($row);
        }
        return $materials;
    }
    /**
     * Retourne un matériel par son ID.
     */
    public function findById(string $id): Material
    {
        $stmt = $this->pdo->prepare
            ("SELECT 
                material.id,
                material.material_name,
                material.quantity_available,
                material.price,
                material.id_material_category,
                material_category.material_category_name
            FROM material
            LEFT JOIN material_category ON material.id_material_category = material_category.id WHERE material.id = ?");
            $stmt->execute([$id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            // Si aucun résultat n'est trouvé lance une exception
            throw new RuntimeException('Material not found');
        }
        return $this->mapRowToMaterial($row);
    }
     /**
     * Insère un nouveau matériel.
     */
    public function create(Material $material): void
    {
        // Génération UUID côté PHP
        $materialId = Uuid::uuid4()->toString();
        $material->setId($materialId);

            // insertion du matériel
            $stmt = $this->pdo->prepare("
                INSERT INTO material (id, material_name, quantity_available, price, id_material_category)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $material->getId(),
                $material->getMaterialName(),
                $material->getQuantityAvailable(),
                $material->getPrice(),
                $material->getIdMaterialCategory()
            ]);
    }
    /**
     * Met à jour un matériel existant.
     */
    public function update(Material $material): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE material 
            SET material_name = ?, quantity_available = ?, price = ?, id_material_category = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $material->getMaterialName(),
            $material->getQuantityAvailable(),
            $material->getPrice(),
            $material->getIdMaterialCategory(),
            $material->getId()
        ]);

        // Si le matériel n'existe pas retourne une erreur
        if ($stmt->rowCount() === 0) {
            throw new RuntimeException("Material not found");
      }
    }
    /**
     * Supprime un matériel.
     */
    public function delete(string $id): void
    {
        // supprimer le matériel
        $stmt = $this->pdo->prepare("DELETE FROM material WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('Material not found');
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
                material_name,
                quantity_available,
                price
            FROM drink_package WHERE id IN ($placeholders)"
          );

        $stmt->execute($ids);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}