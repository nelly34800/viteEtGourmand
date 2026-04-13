<?php

namespace App\Controller;

use App\Repository\NoticeRepository;
use App\Entity\Notice;
use App\Helper\RequestHelper;
use App\Helper\ResponseHelper;
use App\Helper\ValidatorHelper;
use Database;
use InvalidArgumentException;
use RuntimeException;
/**
 * Contrôleur responsable de la gestion des avis.
 */
class NoticeController
{
    private NoticeRepository $repository;

    const CREATION_STATUS ='en attente';

    public function __construct()
    {
      // Récupération de la connexion PDO et initialisation du repository
        $pdo = Database::getConnection();
        // Injection de la dépendance du repository
        $this->repository = new NoticeRepository($pdo);
    }
     /**
     * Liste tous les avis.
     */
    public function index(): void
    {
        // Appel du repository pour récupérer tous les avis et affichage au format JSON
        $notices = $this->repository->findAllNotice();

        $response = array_map(function(Notice $notice) {
          // Transformation en array pour JSON (hydratation inverse)
            return [
                'id' => $notice->getId(),
                'note' => $notice->getNote(),
                'description' => $notice->getDescription(),
                'signature' => $notice->getSignature(),
                'status' => $notice->getStatus(),
                'date' => $notice->getDate()->format('Y-m-d'),
                'id_order' => $notice->getIdOrder(),
            ];
        }, $notices);

        ResponseHelper::json($response);
    }
       /**
     * Affiche un avis par ID.
     */
    public function show(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour récupérer l'avis par son id et affichage au format JSON
        $notice = $this->repository->findById($id);

        if (!$notice) {
            ResponseHelper::json(['error' => 'Not found'], 404);
        }
        // Transformation en array pour JSON (hydratation inverse)
        $response =([
            'id' => $notice->getId(),
            'note' => $notice->getNote(),
            'description' => $notice->getDescription(),
            'signature' => $notice->getSignature(),
            'status' => $notice->getStatus(),
            'date' => $notice->getDate()->format('Y-m-d'),
            'id_order' => $notice->getIdOrder(),
        ]);
        ResponseHelper::json($response);
    }
    /**
     * Crée un nouvel avis.
     */
    public function store(): void
    {
        // Lecture du JSON envoyé
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        if(!isset($data['note'], $data['description'], $data['signature'], $data['date'], $data['id_order'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        $status = self::CREATION_STATUS;
        // Création de l'entité notice à partir des données reçues
        $notice = new Notice(
            '',
            $data['note'],
            $data['description'],
            $data['signature'],
            $status,
            new \DateTimeImmutable($data['date']),
            $data['id_order']
        );
        //  appel du repository pour l'enregistrer en base
        try {
            $this->repository->create($notice);
            ResponseHelper::json(['message' => 'notice created'], 201);
        } catch (\Exception $e) {
            ResponseHelper::json(['error' => 'Error creating notice', 'details' => $e->getMessage()], 500);
        }
    }
    /**
     * Met à jour un avis.
     */
    public function update(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Lecture du JSON
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        if (!isset($data['note'], $data['description'], $data['signature'], $data['status'], $data['date'], $data['id_order'])) {
            throw new InvalidArgumentException('Invalid input');
        }
        // Création de l'entité avis à partir des données reçues 
        $notice = new Notice(
            $id,
            $data['note'],
            $data['description'],
            $data['signature'],
            $data['status'],
            new \DateTimeImmutable($data['date']),
            $data['id_order']
        );
        // appel du repository pour mettre à jour en base
        $this->repository->update($notice);

        ResponseHelper::json(['message' => 'Updated']);
    }
      /**
     * Supprime un avis.
     */
    public function delete(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour supprimer l'avis en base
        $this->repository->delete($id);

        ResponseHelper::json(['message' => 'Deleted']);
    }
     /**
     * Liste tous les avis validés.
     */
    public function indexValidate(): void
    {
        // Appel du repository pour récupérer tous les avis validés et affichage au format JSON
        $notices = $this->repository->findAllNoticeValidate();

        $response = array_map(function(Notice $notice) {
          // Transformation en array pour JSON (hydratation inverse)
            return [
                'id' => $notice->getId(),
                'note' => $notice->getNote(),
                'description' => $notice->getDescription(),
                'signature' => $notice->getSignature(),
                'status' => $notice->getStatus(),
                'date' => $notice->getDate()->format('Y-m-d'),
                'id_order' => $notice->getIdOrder(),
            ];
        }, $notices);

        ResponseHelper::json($response);
    }
}

