<?php

namespace App\Controller;

use App\Repository\ScheduleRepository;
use App\Entity\Schedule;
use App\Helper\RequestHelper;
use App\Helper\ResponseHelper;
use App\Helper\ValidatorHelper;
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

        ResponseHelper::json($response);
    }

       /**
     * Affiche un horaire par ID.
     */
    public function show(string $id): void
    {

        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour récupérer l'horaire par son id et affichage au format JSON
        $schedule = $this->repository->findById($id);

        if (!$schedule) {
            ResponseHelper::json(['error' => 'Not found'], 404);
        }
        // Transformation en array pour JSON (hydratation inverse)
        $response =([
            'id' => $schedule->getId(),
            'schedule_name' => $schedule->getScheduleName(),
            'first_day' => $schedule->getFirstDay(),
            'last_day' => $schedule->getLastDay(),
            'opening_time' => $schedule->getOpeningTime(),
            'closing_time' => $schedule->getClosingTime()
        ]);
        ResponseHelper::json($response);
    }
    /**
     * Crée un nouvel horaire.
     */
    public function store(): void
    {
        // Lecture du JSON envoyé
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        if(!isset($data['schedule_name'], $data['first_day'], $data['last_day'], $data['opening_time'], $data['closing_time'])) {
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

        ResponseHelper::json(['message' => 'Schedule created'], 201);
    }
    /**
     * Met à jour un horaire.
     */
    public function update(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Lecture du JSON
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        if (!isset($data['schedule_name'], $data['first_day'], $data['last_day'], $data['opening_time'], $data['closing_time'])) {
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

        ResponseHelper::json(['message' => 'Updated']);
    }
     /**
     * Supprime un horaire.
     */
    public function delete(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour supprimer l'horaire en base
        $this->repository->delete($id);

        ResponseHelper::json(['message' => 'Deleted']);
    }
}
