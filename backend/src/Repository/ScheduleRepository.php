<?php

namespace App\Repository;

use PDO;
use App\Entity\Schedule;
use RuntimeException;
use Ramsey\Uuid\Uuid;

/**
 * Repository responsable de l'accès aux données
 * pour l'entité Schedule.
 */
class ScheduleRepository
{
    private PDO $pdo;

    /**
     * Injection de la dépendance PDO.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function mapRowToSchedule(array $row): Schedule
    {
        // Transforme une ligne SQL de la table`schedule` en objet Schedule
        return new Schedule(
          $row['id'],
            $row['schedule_name'],
            $row['first_day'],
            $row['last_day'],
            $row['opening_time'],
            $row['closing_time']
        );
    }
    /**
     * Retourne un tableau tous les horaires.
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM schedule");
        $rows =  $stmt->fetchAll(PDO::FETCH_ASSOC);

        $schedules = [];

        foreach ($rows as $row) {
            $schedules[] = $this->mapRowToSchedule($row);
        }
        return $schedules;
    }
    /**
     * Retourne un horaire par son ID.
     */
    public function findById(string $id): Schedule
    {
        $stmt = $this->pdo->prepare("SELECT * FROM schedule WHERE id = ?");
        $stmt->execute([$id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            // Si aucun résultat n'est trouvé lance une exception
            throw new RuntimeException('Schedule not found');
        }

        return $this->mapRowToSchedule($row);
    }
    /**
     * Insère un nouvel horaire.
     */
    public function create(Schedule $schedule): void
    {
        // Génération UUID côté PHP
        $scheduleId = Uuid::uuid4()->toString();
        $schedule->setId($scheduleId);

        $stmt = $this->pdo->prepare("
            INSERT INTO schedule (id, schedule_name, first_day, last_day, opening_time, closing_time)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $schedule->getId(),
            $schedule->getScheduleName(),
            $schedule->getFirstDay(),
            $schedule->getLastDay(),
            $schedule->getOpeningTime(),
            $schedule->getClosingTime()
        ]);
    }
    /**
     * Met à jour un horaire existant.
     */
    public function update(Schedule $schedule): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE schedule
            SET schedule_name = ?, first_day = ?, last_day = ?, opening_time = ?, closing_time = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $schedule->getScheduleName(),
            $schedule->getFirstDay(),
            $schedule->getLastDay(),
            $schedule->getOpeningTime(),
            $schedule->getClosingTime(),
            $schedule->getId()
        ]);
        // Si l'horaire n'existe pas retourne une erreur
        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('Schedule not found');
        }
    }

    /**
     * Supprime un horaire.
     */
    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM schedule WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('Schedule not found');
        }
    }
}
