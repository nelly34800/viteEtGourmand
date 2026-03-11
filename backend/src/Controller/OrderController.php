<?php

namespace App\Controller;
 
use App\Repository\OrderRepository;
use App\Entity\Order;
use App\Helper\RequestHelper;
use App\Helper\ResponseHelper;
use App\Helper\ValidatorHelper;
use Database;
use InvalidArgumentException;
use RuntimeException;
/**
 * Contrôleur responsable de la gestion des commandes.
 */
class OrderController
{
    private OrderRepository $repository;

    public function __construct()
    {
      // Récupération de la connexion PDO et initialisation du repository
        $pdo = Database::getConnection();
        // Injection de la dépendance du repository
        $this->repository = new OrderRepository($pdo);
    }
     /**
     * Liste toutes les commandes.
     */
    public function index(): void
    {
        // Appel du repository pour récupérer toutes les commandes et affichage au format JSON
        $orders = $this->repository->findAll();

        $response = array_map(function(Order $order) {
          // Transformation en array pour JSON (hydratation inverse)
            return [
                'id' => $order->getId(),
                'order_date' => $order->getOrderDate()->format('Y-m-d H:i:s'),
                'service_date' => $order->getServiceDate()->format('Y-m-d H:i:s'),
                'delivery_address' => $order->getDeliveryAddress(),
                'city' => $order->getCity(),
                'postal_code' => $order->getPostalCode(),
                'number_of_people' => $order->getNumberOfPeople(),
                'total_order_price' => $order->getTotalOrderPrice(),
                'status' => $order->getStatus(),
                'equipment_loan' => $order->getEquipmentLoan(),
                'equipment_return' => $order->getEquipmentReturn(),
                'id_user' => $order->getIdUser(),
                'menus' => $order->getMenus(),
                'materials' => $order->getMaterials(),
                'drink_packages' => $order->getDrinkPackages(),
                'personal_packages' => $order->getPersonalPackages()
            ];
        }, $orders);

        ResponseHelper::json($response);
    }
       /**
     * Affiche une commande par son ID.
     */
    public function show(string $id): void
    {

        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour récupérer la commande par son id et affichage au format JSON

        try {
            $order = $this->repository->findById($id);
            // Transformation en array pour JSON (hydratation inverse)
            $response =([
                'id' => $order->getId(),
                'order_date' => $order->getOrderDate()->format('Y-m-d H:i:s'),
                'service_date' => $order->getServiceDate()->format('Y-m-d H:i:s'),
                'delivery_address' => $order->getDeliveryAddress(),
                'city' => $order->getCity(),
                'postal_code' => $order->getPostalCode(),
                'number_of_people' => $order->getNumberOfPeople(),
                'total_order_price' => $order->getTotalOrderPrice(),
                'status' => $order->getStatus(),
                'equipment_loan' => $order->getEquipmentLoan(),
                'equipment_return' => $order->getEquipmentReturn(),
                'id_user' => $order->getIdUser(),
                'menus' => $order->getMenus(),
                'materials' => $order->getMaterials(),
                'drink_packages' => $order->getDrinkPackages(),
                'personal_packages' => $order->getPersonalPackages()
            ]);
            ResponseHelper::json($response);

        } catch (RuntimeException $e) {
        ResponseHelper::json(['error' => 'Not found'], 404);
        }
    }

    /**
     * Crée une nouvelle commande.
     */
public function store(): void
{

    $data = RequestHelper::getJson();
    // Validation des champs obligatoires
    if(!isset($data['order_date'], $data['service_date'], $data['delivery_address'], $data['city'], $data['postal_code'], $data['number_of_people'], $data['total_order_price'], $data['status'], $data['equipment_loan'], $data['equipment_return'], $data['id_user'])) {
        throw new InvalidArgumentException('Invalid input');
    }
    $menus = $data ['menus'] ?? [];
    $materials = $data['materials'] ?? [];
    $drinkPackages = $data['drink_packages'] ?? [];
    $personalPackages = $data['personal_packages'] ?? [];
    // Création de l'entité Order à partir des données reçues
    $order = new Order(
        id: '', // l'UUID sera généré côté repository
        orderDate: new \DateTimeImmutable($data['order_date']),
        serviceDate: new \DateTimeImmutable($data['service_date']),
        deliveryAddress: $data['delivery_address'],
        city: $data['city'],
        postalCode: $data['postal_code'],
        numberOfPeople: $data['number_of_people'],
        totalOrderPrice: $data['total_order_price'],
        status: $data['status'],
        equipmentLoan: $data['equipment_loan'],
        equipmentReturn: $data['equipment_return'],
        idUser: $data['id_user'],
        menus: $menus,
        materials: $materials,
        drinkPackages: $drinkPackages,
        personalPackages: $personalPackages
    );
        //  appel du repository pour l'enregistrer en base
        try {
            $this->repository->create($order);
            ResponseHelper::json(['message' => 'Order created'], 201);

        } catch (\Exception $e) {
            ResponseHelper::json(['error' => 'Error during Order creation', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Met à jour un commande.
     */
    public function update(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Lecture du JSON
        $data = RequestHelper::getJson();
        // Validation des champs obligatoires
        if(!isset($data['order_date'], $data['service_date'], $data['delivery_address'], $data['city'], $data['postal_code'], $data['number_of_people'], $data['total_order_price'], $data['status'], $data['equipment_loan'], $data['equipment_return'], $data['id_user'])) {
        throw new InvalidArgumentException('Invalid input');
        }
        $menus = $data ['menus'] ?? [];
        $materials = $data['materials'] ?? [];
        $drinkPackages = $data['drink_packages'] ?? [];
        $personalPackages = $data['personal_packages'] ?? [];
        // Création de l'entité commande à partir des données reçues 
        $order = new Order(
            $id,
            new \DateTimeImmutable($data['order_date']),
            new \DateTimeImmutable($data['service_date']),
            $data['delivery_address'],
            $data['city'],
            $data['postal_code'],
            $data['number_of_people'],
            $data['total_order_price'],
            $data['status'],
            $data['equipment_loan'],
            $data['equipment_return'],
            $data['id_user'],
            menus: $menus,
            materials: $materials,
            drinkPackages: $drinkPackages,
            personalPackages: $personalPackages
        );
        // appel du repository pour mettre à jour en base
        $this->repository->update($order);

        ResponseHelper::json(['message' => 'Updated']);
    }
      /**
     * Supprime un commande.
     */
    public function delete(string $id): void
    {
        //si l'id n'a pas le format UUID retourne une erreur
        ValidatorHelper::validateUuid($id);
        // Appel du repository pour supprimer l'utilisateur en base
        $this->repository->delete($id);

        ResponseHelper::json(['message' => 'Deleted']);
    }
}
