<?php

namespace App\Controller;

use App\Services\CartService;
use App\Services\GeocodingService;
use App\Services\DeliveryService;
use App\Services\MailService;
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
        // Récupération de l'utilisateur connecté
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            ResponseHelper::json(['error' => 'Utilisateur non connecté'], 401);
        }
        // Appel du repository pour récupérer toutes les commandes et affichage au format JSON
        if (in_array($user['role'], ['admin', 'employé'])) {
            $orders = $this->repository->findAll();
        // Appel du repository pour récupérer toutes les commandes d'un client
        } else {
            $orders = $this->repository->findAll($user['id']);
        }

        $response = array_map(function(Order $order) {
          // Transformation en array pour JSON (hydratation inverse)
            return [
                'id' => $order->getId(),
                'order_date' => $order->getOrderDate()->format('Y-m-d H:i:s'),
                'service_date' => $order->getServiceDate()->format('Y-m-d H:i:s'),
                'delivery_address' => $order->getDeliveryAddress(),
                'city' => $order->getCity(),
                'postal_code' => $order->getPostalCode(),
                'latitude' => $order->getLatitude(),
                'longitude' => $order->getLongitude(),
                'distance_km'=> $order->getDistanceKm(),
                'number_of_people' => $order->getNumberOfPeople(),
                'delivery_charges' => $order->getDeliveryCharges(),
                'total_amount' => $order->getTotalAmount(),
                'status' => $order->getStatus(),
                'status_changed_at' => $order->getStatusChangedAt()?->format('Y-m-d H:i:s'),
                'equipment_loan' => $order->getEquipmentLoan(),
                'equipment_return' => $order->getEquipmentReturn(),
                'cancellation_reason' => $order->getCancellationReason(),
                'contact_mode' => $order->getContactMode(),
                'id_user' => $order->getIdUser(),
                'user_last_name' => $order->getUserLastName(),
                'user_first_name' => $order->getUserFirstName(),
                'user_email' => $order->getUserEmail(),
                'user_phone' => $order->getUserPhone(),
                'has_notice' => $order->hasNotice(),
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
                'latitude' => $order->getLatitude(),
                'longitude' => $order->getLongitude(),
                'distance_km'=> $order->getDistanceKm(),
                'number_of_people' => $order->getNumberOfPeople(),
                'delivery_charges' => $order->getDeliveryCharges(),
                'total_amount' => $order->getTotalAmount(),
                'status' => $order->getStatus(),
                'status_changed_at' => $order->getStatusChangedAt()?->format('Y-m-d H:i:s'),
                'equipment_loan' => $order->getEquipmentLoan(),
                'equipment_return' => $order->getEquipmentReturn(),
                'cancellation_reason' => $order->getCancellationReason(),
                'contact_mode' => $order->getContactMode(),
                'id_user' => $order->getIdUser(),
                'user_last_name' => $order->getUserLastName(),
                'user_first_name' => $order->getUserFirstName(),
                'user_email' => $order->getUserEmail(),
                'user_phone' => $order->getUserPhone(),
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
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $data = RequestHelper::getJson();
            // Vérification des infos de session
            $cart = $_SESSION['cart'] ?? [];
            $delivery = $_SESSION['delivery'] ?? null;
            $user = $_SESSION['user'] ?? null;

            if (empty($cart)) {
                ResponseHelper::json(['error' => 'Panier vide'], 400);
            }

            if (!$delivery) {
                ResponseHelper::json(['error' => 'Frais de livraison non calculés'], 400);
            }

            if (!$user || empty($user['id'])) {
                ResponseHelper::json(['error' => 'Utilisateur non connecté'], 401);
            }

            if (empty($data['service_date'])) {
                ResponseHelper::json(['error' => 'Date de service manquante'], 400);
            }
            // récupère le panier détaillé dans CartService
            $cartService = new CartService();
            $detailedCart = $cartService->getDetailedCart($cart);
            // prépare les tableaux de produits
            $menus = [];
            $materials = [];
            $drinkPackages = [];
            $personalPackages = [];
            // initialise les totaux
            $totalAmount = 0;
            $numberOfPeople = 0;
            $equipmentLoan = false;

            foreach ($detailedCart as $item) {
                // ajoute le total ligne au total général
                $totalAmount += (float) $item['line_total'];

                if ($item['type'] === 'menu') {
                    // ajoute personnes de chaque menu au nombre total de convives
                    $numberOfPeople += (int) $item['quantity'];

                    $menus[] = [
                        'id' => $item['id'],
                        'number' => $item['quantity'],
                        'price' => $item['price_per_person'],
                        'discount' => $item['discount'] ?? 0,
                        'subtotal' => $item['line_total']
                    ];
                }

                if ($item['type'] === 'material') {
                  //  si il y a du matériel dans la commande mais location materiel à vrai
                    $equipmentLoan = true;

                    $materials[] = [
                        'id' => $item['id'],
                        'number' => $item['quantity'],
                        'price' => $item['price_per_person'],
                        'subtotal' => $item['line_total']
                    ];
                }

                if ($item['type'] === 'drink_package') {
                    $drinkPackages[] = [
                        'id' => $item['id'],
                        'number' => $item['quantity'],
                        'price' => $item['price_per_person'],
                        'subtotal' => $item['line_total']
                    ];
                }

                if ($item['type'] === 'personal_package') {
                    $personalPackages[] = [
                        'id' => $item['id'],
                        'number' => $item['quantity'],
                        'price' => $item['price_per_person'],
                        'subtotal' => $item['line_total']
                    ];
                }
            }
            // ajout frais de livraison
            $deliveryCharges = (float) $delivery['delivery_charges'];
            // total du panier = total + frais de livraison
            $totalAmount = $totalAmount + $deliveryCharges;
            // crée l'objet order
            $order = new Order(
                id: '',
                orderDate: new \DateTimeImmutable(),
                serviceDate: new \DateTimeImmutable($data['service_date']),
                deliveryAddress: $delivery['address'],
                city: $delivery['city'],
                postalCode: $delivery['postal_code'],
                latitude: (float) $delivery['latitude'],
                longitude: (float) $delivery['longitude'],
                distanceKm: (float) $delivery['distance_km'],
                numberOfPeople: $numberOfPeople,
                deliveryCharges: $deliveryCharges,
                totalAmount: $totalAmount,
                status: 'en attente',
                equipmentLoan: $equipmentLoan,
                idUser: $user['id'],
                statusChangedAt: null,
                equipmentReturn: false,
                cancellationReason: null,
                contactMode: null,
                userLastName: null,
                userFirstName: null,
                userEmail: null,
                userPhone: null,
                menus: $menus,
                materials: $materials,
                drinkPackages: $drinkPackages,
                personalPackages: $personalPackages
            );
            //enregistre en BDD
            $orderId = $this->repository->create($order);
            $orderNumber = 'CMD-' . substr($orderId, 0, 8);
            // vide le panier 
            unset($_SESSION['cart'], $_SESSION['delivery']);
            //  email client
            $mailService = new MailService();

            $mailService->sendOrderCreatedMail(
                $user['email'],
                $orderNumber
            );

            ResponseHelper::json(['success' => true,'message' => 'Commande créée'], 201);

        } catch (\Throwable $e) {
            ResponseHelper::json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Met à jour un commande.
     */
    public function update(string $id): void
    {
      
        try {
            //si l'id n'a pas le format UUID retourne une erreur
            ValidatorHelper::validateUuid($id);
            $user = $_SESSION['user'] ?? null;

            if (!$user || $user['role'] !== 'client') {
                ResponseHelper::json(['error' => 'Accès interdit'], 403);
            }

            $orderInfo = $this->repository->findOwnerAndStatus($id);

            if (!$orderInfo) {
                ResponseHelper::json(['error' => 'Commande introuvable'], 404);
            }

            if ($orderInfo['id_user'] !== $user['id']) {
                ResponseHelper::json(['error' => 'Vous ne pouvez modifier que vos commandes'], 403);
            }

            if ($orderInfo['status'] !== 'en attente') {
                ResponseHelper::json(['error' => 'Cette commande ne peut plus être modifiée'], 400);
            }
            // Lecture du JSON
            $data = RequestHelper::getJson();

            $serviceDate = $data['service_date'] ?? null;
            $address = trim($data['delivery_address'] ?? '');
            $city = trim($data['city'] ?? '');
            $postalCode = trim($data['postal_code'] ?? '');

            if (!$serviceDate || !$address || !$city || !$postalCode) {
                ResponseHelper::json(['error' => 'Date et adresse complètes requises'], 400);
            }

            $geocoder = new GeocodingService();
            $coords = $geocoder->geocode($address, $postalCode, $city);

            $deliveryService = new DeliveryService();
            $delivery = $deliveryService->calculate($coords['lat'], $coords['lng']);

            $oldDeliveryCharges = (float) $this->repository->findById($id)->getDeliveryCharges();
            $currentTotal = (float) $orderInfo['total_amount'];

            // on retire les anciens frais de livraison puis on ajoute les nouveaux
            $totalWithoutDelivery = $currentTotal - $oldDeliveryCharges;
            $newTotalAmount = $totalWithoutDelivery + (float) $delivery['delivery_charges'];
            // appel du repository pour mettre à jour en base
            $this->repository->update(
                $id,
                (new \DateTimeImmutable($serviceDate))->format('Y-m-d H:i:s'),
                $address,
                $city,
                $postalCode,
                (float) $coords['lat'],
                (float) $coords['lng'],
                (float) $delivery['distance_km'],
                (float) $delivery['delivery_charges'],
                $newTotalAmount
            );

            ResponseHelper::json([
                'success' => true,
                'message' => 'Commande modifiée'
            ]);

        } catch (\Throwable $e) {
            ResponseHelper::json(['error' => $e->getMessage()], 400);
        }
    }
      /**
     * Supprime un commande.
     */
    public function delete(string $id): void
    {
        try {
            //si l'id n'a pas le format UUID retourne une erreur
            ValidatorHelper::validateUuid($id);
            $user = $_SESSION['user'] ?? null;

            if (!$user) {
                ResponseHelper::json(['error' => 'Utilisateur non connecté'], 401);
            }

            $this->repository->delete($id, $user['id']);

            ResponseHelper::json(['message' => 'Deleted']);

        } catch (\Throwable $e) {
            ResponseHelper::json(['error' => $e->getMessage()], 400);
        }
    }
    // modifi statut de la commande
    public function updateStatus(string $id): void
    {
        try {
            $data = RequestHelper::getJson();

            $status = $data['status'] ?? null;
            $reason = $data['cancellation_reason'] ?? null;
            $contactMode = $data['contact_mode'] ?? null;

            if (!$status) {
                ResponseHelper::json(['error' => 'Statut manquant'], 400);
            }

            $allowedStatuses = [
                'en attente',
                'accepté',
                'en préparation',
                'en cours de livraison',
                'livrée',
                'attente retour matériel',
                'terminée',
                'annulée'
            ];

            if (!in_array($status, $allowedStatuses, true)) {
                ResponseHelper::json(['error' => 'Statut invalide'], 400);
            }

            if ($status === 'annulée' && (!$reason || !$contactMode)) {
                ResponseHelper::json([
                    'error' => 'Le motif et le mode de contact sont obligatoires pour une annulation'
                ], 400);
            }
            // récupère l'id de la commande
            $this->repository->updateStatus($id, $status, $reason, $contactMode);
            $orderId = $id;

            if ($status === 'terminée' || $status === 'attente retour matériel') {
                $customerEmail = $this->repository->findCustomerEmailByOrderId($orderId);
                $orderNumber = 'CMD-' . substr($orderId, 0, 8);

                $mailService = new MailService();

                if ($status === 'terminée') {
                  $mailService->sendOrderCompletedMail($customerEmail, $orderNumber);
                }

                if ($status === 'attente retour matériel') {
                    $mailService->sendReturnMaterialMail($customerEmail, $orderNumber);
                }
            }
            ResponseHelper::json(['success' => true]);

        } catch (\Throwable $e) {
            ResponseHelper::json(['error' => $e->getMessage()], 400);
        }
    }
}