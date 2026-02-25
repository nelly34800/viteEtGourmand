<?php

namespace App\Controller;

use App\Repository\ScheduleRepository;
use App\Entity\Schedule;
use Database;
use InvalidArgumentException;
use RuntimeException;
/**
 * Contrôleur responsable de la gestion des horaires.
 */
class ScheduleController
{
    private ScheduleRepository $repository;

    public function __construct()
    {
      // Récupération de la connexion PDO et initialisation du repository
        $pdo = Database::getConnection();
        // Injection de la dépendance du repository
        $this->repository = new ScheduleRepository($pdo);
    }

     /**
     * Liste tous les horaires.
     */
    public function index(): void
    {
        // Appel du repository pour récupérer tous les horaires et affichage au format JSON
        $schedules = $this->repository->findAll();

        $response = array_map(function($schedule) {
          // Transformation en array pour JSON (hydratation inverse)
            return [
                'id' => $schedule->getId(),
                'schedule_name' => $schedule->getScheduleName(),
                'first_day' => $schedule->getFirstDay(),
                'last_day' => $schedule->getLastDay(),
                'opening_time' => $schedule->getOpeningTime(),
                'closing_time' => $schedule->getClosingTime()
            ];
        }, $schedules);

        echo json_encode($response);
    }

       /**
     * Affiche un horaire par ID.
     */
    public function show(string $id): void
    {

        //si l'id n'a pas le format UUID retourne une erreur
        if (!preg_match('/^[0-9a-fA-F-]{36}$/', $id)) {
            throw new InvalidArgumentException("Invalid ID format");
        }
        // Appel du repository pour récupérer l'horaire par son id et affichage au format JSON
        $schedule = $this->repository->findById($id);
        // Transformation en array pour JSON (hydratation inverse)
        echo json_encode([
            'id' => $schedule->getId(),
            'schedule_name' => $schedule->getScheduleName(),
            'first_day' => $schedule->getFirstDay(),
            'last_day' => $schedule->getLastDay(),
            'opening_time' => $schedule->getOpeningTime(),
            'closing_time' => $schedule->getClosingTime()
        ]);
    }
    /**
     * Crée un nouvel horaire.
     */
    public function store(): void
    {
        // Lecture du JSON envoyé
        $data = json_decode(file_get_contents("php://input"), true);
        // Validation des champs obligatoires
        if(!$data || !isset($data['schedule_name'], $data['first_day'], $data['last_day'], $data['opening_time'], $data['closing_time'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        // Création de l'entité Schedule à partir des données reçues
        $schedule = new Schedule(
            $data['schedule_name'],
            $data['first_day'],
            $data['last_day'],
            $data['opening_time'],
            $data['closing_time']
        );
        //  appel du repository pour l'enregistrer en base
        $this->repository->create($schedule);

        http_response_code(201);
        echo json_encode(['message' => 'Created']);
    }
    /**
     * Met à jour un horaire.
     */
    public function update(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        if (!preg_match('/^[0-9a-fA-F-]{36}$/', $id)) {
            throw new InvalidArgumentException("Invalid ID format");
        }
        // Lecture du JSON
        $data = json_decode(file_get_contents("php://input"), true);
        // Validation des champs obligatoires
        if (!$data || !isset($data['first_day'], $data['last_day'], $data['opening_time'], $data['closing_time'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        // Création de l'entité Schedule à partir des données reçues 
        $schedule = new Schedule(
            $data['schedule_name'],
            $data['first_day'],
            $data['last_day'],
            $data['opening_time'],
            $data['closing_time'],
            $id
        );
        // appel du repository pour mettre à jour en base
        $this->repository->update($schedule);

        echo json_encode(['message' => 'Updated']);
    }
     /**
     * Supprime un horaire.
     */
    public function delete(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        if (!preg_match('/^[0-9a-fA-F-]{36}$/', $id)) {
            throw new InvalidArgumentException("Invalid ID format");
        }
        // Appel du repository pour supprimer l'horaire en base
        $this->repository->delete($id);

        http_response_code(200);
        echo json_encode(['message' => 'Deleted']);
    }
}
