<?php

namespace App\Repository;

use PDO;
use App\Entity\Notice;
use RuntimeException;
use Ramsey\Uuid\Uuid;
/**
 * Repository responsable de l'accès aux données
 * pour l'entité Notice.
 */
class NoticeRepository
{
    private PDO $pdo;
    /**
     * Injection de la dépendance PDO. 
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function mapRowToNotice(array $row): Notice
    {
        // transforme une ligne SQL de la table `notice` en objet Notice
        return new Notice(
            $row['id'],
            $row['note'],
            $row['description'],
            $row['signature'],
            $row['status'],
            new \DateTimeImmutable($row['date']),
            $row['id_order'],
        );
    }
    /**
     * Retourne un tableau de tous les avis.
     */
    public function findAllNotice(): array
    {
        $stmt = $this->pdo->query("SELECT id, note, description, signature, status, date, id_order FROM notice");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $notices = [];
        foreach ($rows as $row) {
            $notices[] = $this->mapRowToNotice($row);
        }
        return $notices;
    }
    /**
     * Retourne un avis par son ID.
     */
    public function findById(string $id): Notice
    {
        $stmt = $this->pdo->prepare("SELECT id, note, description, signature, status, date, id_order FROM notice WHERE notice.id = ?");
        $stmt->execute([$id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            // Si aucun résultat n'est trouvé lance une exception
            throw new RuntimeException('Notice not found');
        }
        return $this->mapRowToNotice($row);
    }
    /**
     * Insère un nouvel avis.
     */
    public function create(Notice $notice): void
    {
        // Génération UUID côté PHP
        $noticeId = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $notice->setId($noticeId);
        // insertion de l'avis
        $stmt = $this->pdo->prepare("
            INSERT INTO notice 
            (id, note, description, signature, status, date, id_order)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $notice->getId(),
            $notice->getNote(),
            $notice->getDescription(),
            $notice->getSignature(),
            $notice->getStatus(),
            $notice->getDate()->format('Y-m-d'),
            $notice->getIdOrder()
        ]);
    }
    /**
     * Met à jour un avis existant.
     */
    public function update(Notice $notice): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE notice
            SET note = ?, description = ?, signature = ?, status = ?, date = ?, id_order = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $notice->getNote(),
            $notice->getDescription(),
            $notice->getSignature(),
            $notice->getStatus(),
            $notice->getDate()->format('Y-m-d'),
            $notice->getIdOrder(),
            $notice->getId()

        ]);
        // Si l'avis n'existe pas retourne une erreur
        if ($stmt->rowCount() === 0) {
            throw new RuntimeException("notice not found");
      }
    }
    /**
     * Supprime un avis.
     */
    public function delete(string $id): void
    {
      //supprime l'avis
        $stmt = $this->pdo->prepare("DELETE FROM notice WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('notice not found');
        }
    }
    /**
     * Retourne un tableau de tous les avis validés.
     */
    public function findAllNoticeValidate(): array
    {
        $stmt = $this->pdo->query("
        SELECT id, note, description, signature, status, date, id_order 
        FROM notice 
        WHERE status = 'validé'");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $notices = [];
        foreach ($rows as $row) {
            $notices[] = $this->mapRowToNotice($row);
        }
        return $notices;
    }
}