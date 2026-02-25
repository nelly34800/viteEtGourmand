<?php
namespace App\Repository;

use PDO;
use App\Entity\Schedule;
use RuntimeException;

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

    public function hydrate(array $data): Schedule
    {
        // Transforme le tableau de la bdd en une instance de Schedule (objet)
        return new Schedule(
            $data['schedule_name'],
            $data['first_day'],
            $data['last_day'],
            $data['opening_time'],
            $data['closing_time'],
            $data['id']
        );
    }
    /**
     * Retourne un  tableau tous les horaires.
     *
     * @return array
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM schedule");
        $results =  $stmt->fetchAll(PDO::FETCH_ASSOC);

        $schedules = [];

        foreach ($results as $row) {
            $schedules[] = $this->hydrate($row);
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

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            // Si aucun résultat n'est trouvé lance une exception
            throw new RuntimeException('Schedule not found');
        }

        return $this->hydrate($result);
    }

    /**
     * Insère un nouvel horaire.
     */
    public function create(Schedule $schedule): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO schedule (schedule_name, first_day, last_day, opening_time, closing_time)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
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
