<?php

namespace App\Repository;

use PDO;
use App\Entity\Order;
use RuntimeException;
use Ramsey\Uuid\Uuid;
/**
 * Repository responsable de l'accès aux données
 * pour l'entité Order.
 */
class OrderRepository
{
    private PDO $pdo;

    /**
     * Injection de la dépendance PDO.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function hydrate(array $rows): array
    {
        // Transforme le tableau de la bdd en une instance de Order (objet)
        $orders = [];

        foreach ($rows as $row) {
            $orderId = $row['order_id'];

            // Si on n'a pas encore créé l'objet Order
            if (!isset($orders[$orderId])) {
                $orders[$orderId] = new Order(
                    $orderId,
                    new \DateTimeImmutable($row['order_date']),
                    new \DateTimeImmutable($row['service_date']),
                    $row['delivery_address'],
                    $row['city'],
                    $row['postal_code'],
                    $row['number_of_people'],
                    $row['total_order_price'],
                    $row['status'],
                    $row['equipment_loan'],
                    $row['equipment_return'],
                    $row['id_user'],
                );
            }
            // Ajouter le menu si pas déjà présent
             if ($row['menu_id'] !== null) {
                $orders[$orderId]->addMenu([
                  'id' => $row['menu_id'],
                  'name' => $row['menu_name'],
                  'number' => $row['number_people'],
                  'price' => $row['price_person'],
                  'subtotal' => $row['subtotal']
                  ]);
            }
            // Ajouter le matériel si pas déjà présent
            if ($row['material_id'] !== null) {
            $orders[$orderId]->addMaterial([
                  'id' => $row['material_id'],
                  'name' => $row['material_name'],
                  'number' => $row['quantity'],
                  'price' => $row['unit_price'],
                  'subtotal' => $row['subtotal']
                  ]);
            }
             // Ajouter le forfait boisson si pas déjà présent
            if ($row['drink_package_id'] !== null) {
            $orders[$orderId]->addDrinkPackage([
                  'id' => $row['drink_package_id'],
                  'name' => $row['drink_package_name'],
                  'number' => $row['number_people'],
                  'price' => $row['price_person'],
                  'subtotal' => $row['subtotal']
                  ]);
            }
             // Ajouter le forfait personnel si pas déjà présent
            if ($row['personal_package_id'] !== null) {
            $orders[$orderId]->addPersonalPackage([
                  'id' => $row['personal_package_id'],
                  'name' => $row['personal_package_event_type'],
                  'number' => $row['number_people'],
                  'price' => $row['price_package'],
                  'subtotal' => $row['subtotal']
                  ]);
            }
        }
    // À la fin, $orders contient tous les objets Order avec leurs tableaux menu, material,drinkPackage et personalPackage
    return array_values($orders);
    }
    /**
     * Retourne un  tableau tous les commandes
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query
            ("SELECT 
                orders.id AS order_id,
                orders.order_date,
                orders.service_date,
                orders.delivery_address,
                orders.city,
                orders.postal_code,
                orders.number_of_people,
                orders.total_order_price,
                orders.status,
                orders.equipment_loan,
                orders.equipment_return,
                orders.id_user,
                order_menu.number_people AS menu_number_people,
                order_menu.price_person,
                order_menu.subtotal AS menu_subtotal,
                menu.id AS menu_id,
                menu.menu_name,
                order_material.quantity,
                order_material.unit_price,
                order_material.subtotal AS material_subtotal,
                material.id AS material_id,
                material.material_name,
                order_drink_package.number_people AS drink_number_people,
                order_drink_package.price_person,
                order_drink_package.subtotal AS drink_subtotal,
                drink_package.id AS drink_package_id,
                drink_package.drink_package_name,
                order_personal_package.number_people AS personal_number_people,
                order_personal_package.price_package,
                order_personal_package.subtotal AS personal_subtotal,
                personal_package.id AS personal_package_id,
                personal_package.event_type AS personal_package_event_type
            FROM orders
            LEFT JOIN user ON orders.id_user = user.id
            LEFT JOIN order_menu ON orders.id = order_menu.id_order
            LEFT JOIN menu ON order_menu.id_menu = menu.id
            LEFT JOIN order_material ON orders.id = order_material.id_order
            LEFT JOIN material ON order_material.id_material = material.id
            LEFT JOIN order_drink_package ON orders.id = order_drink_package.id_order
            LEFT JOIN drink_package ON order_drink_package.id_drink_package = drink_package.id
            LEFT JOIN order_personal_package ON orders.id = order_personal_package.id_order
            LEFT JOIN personal_package ON order_personal_package.id_personal_package = personal_package.id");

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->hydrate($rows);
    }
    /**
     * Retourne un commande par son ID.
     */
    public function findById(string $id): Order
    {
        $stmt = $this->pdo->prepare
            ("SELECT 
                orders.id AS order_id,
                orders.order_date,
                orders.service_date,
                orders.delivery_address,
                orders.city,
                orders.postal_code,
                orders.number_of_people,
                orders.total_order_price,
                orders.status,
                orders.equipment_loan,
                orders.equipment_return,
                orders.id_user,
                order_menu.number_people,
                order_menu.price_person,
                order_menu.subtotal,
                menu.id AS menu_id,
                menu.menu_name,
                order_material.quantity,
                order_material.unit_price,
                order_material.subtotal,
                material.id AS material_id,
                material.material_name,
                order_drink_package.number_people,
                order_drink_package.price_person,
                order_drink_package.subtotal,
                drink_package.id AS drink_package_id,
                drink_package.drink_package_name,
                order_personal_package.number_people,
                order_personal_package.price_package,
                order_personal_package.subtotal,
                personal_package.id AS personal_package_id,
                personal_package.event_type AS personal_package_event_type
            FROM orders
            LEFT JOIN user ON orders.id_user = user.id
            LEFT JOIN order_menu ON orders.id = order_menu.id_order
            LEFT JOIN menu ON order_menu.id_menu = menu.id
            LEFT JOIN order_material ON orders.id = order_material.id_order
            LEFT JOIN material ON order_material.id_material = material.id
            LEFT JOIN order_drink_package ON orders.id = order_drink_package.id_order
            LEFT JOIN drink_package ON order_drink_package.id_drink_package = drink_package.id
            LEFT JOIN order_personal_package ON orders.id = order_personal_package.id_order
            LEFT JOIN personal_package ON order_personal_package.id_personal_package = personal_package.id WHERE orders.id = ?");

        $stmt->execute([$id]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$rows) {
          // Si aucun résultat n'est trouvé lance une exception
            throw new RuntimeException('Order not found');
        }

        return $this->hydrate($rows)[0];
        }
     /**
     * Insère un nouveau commande.
     */
    public function create(Order $order): void
    {
        // Génération UUID côté PHP
        $orderId = Uuid::uuid4()->toString();
        $order->setId($orderId);

        try {
            // démarrage de la transaction
            $this->pdo->beginTransaction();

            // insertion du commande
            $stmt = $this->pdo->prepare("
                INSERT INTO orders (id, order_date, service_date, delivery_address, city, postal_code, number_of_people, total_order_price, status, equipment_loan, equipment_return, id_user)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $order->getId(),
                $order->getOrderDate()->format('Y-m-d H:i:s'),
                $order->getServiceDate()->format('Y-m-d H:i:s'),
                $order->getDeliveryAddress(),
                $order->getCity(),
                $order->getPostalCode(),
                $order->getNumberOfPeople(),
                $order->getTotalOrderPrice(),
                $order->getStatus(),
                $order->getEquipmentLoan(),
                $order->getEquipmentReturn(),
                $order->getIdUser(),
            ]);

            // insertion dans table pivot order_menu
            $stmtMenu = $this->pdo->prepare("
                INSERT INTO order_menu (id_order, id_menu, number_people, price_person, subtotal) VALUES (?, ?, ?, ?, ?)
            ");
            foreach ($order->getMenus() as $menu) {
                $stmtMenu->execute([
                    $order->getId(),
                    $menu['id'],
                    $menu['number'],
                    $menu['price'],
                    $menu['subtotal']
                ]);
            }
              // insertion dans table pivot order_material
            $stmtMaterial = $this->pdo->prepare("
                INSERT INTO order_material (id_order, id_material, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)
            ");
            foreach ($order->getMaterials() as $material) {
                $stmtMaterial->execute([
                    $order->getId(),
                    $material['id'],
                    $material['number'],
                    $material['price'],
                    $material['subtotal']
                ]);
            }
                // insertion dans table pivot order_drink_package
            $stmtDrinkPackage = $this->pdo->prepare("
                INSERT INTO order_drink_package (id_order, id_drink_package, number_people, price_person, subtotal) VALUES (?, ?, ?, ?, ?)
            ");
            foreach ($order->getDrinkPackages() as $drinkPackage) {
                $stmtDrinkPackage->execute([
                    $order->getId(),
                    $drinkPackage['id'],
                    $drinkPackage['number'],
                    $drinkPackage['price'],
                    $drinkPackage['subtotal']
                ]);
            }
                // insertion dans table pivot order_personal_package
            $stmtPersonalPackage = $this->pdo->prepare("
                INSERT INTO order_personal_package (id_order, id_personal_package, number_people, price_package, subtotal) VALUES (?, ?, ?, ?, ?)
            ");
            foreach ($order->getPersonalPackages() as $personalPackage) {
                $stmtPersonalPackage->execute([
                    $order->getId(),
                    $personalPackage['id'],
                    $personalPackage['number'],
                    $personalPackage['price'],
                    $personalPackage['subtotal']
                ]);
            }

            // commit
            $this->pdo->commit();

        } catch (\Exception $e) {
            // rollback en cas d’erreur
            $this->pdo->rollBack();
            throw new RuntimeException("Error while creating the Order : " . $e->getMessage());
        }
    }
    /**
     * Met à jour un commande existant.
     */
    public function update(Order $order): void
    {
        try {
            // démarrage de la transaction
            $this->pdo->beginTransaction();
            // modifie le commande
            $stmt = $this->pdo->prepare("
                UPDATE orders 
                SET order_date = ?, service_date = ?, delivery_address = ?, city = ?, postal_code = ?, number_of_people = ?, total_order_price = ?, status = ?, equipment_loan = ?, equipment_return = ?, id_user = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $order->getOrderDate()->format('Y-m-d H:i:s'),
                $order->getServiceDate()->format('Y-m-d H:i:s'),
                $order->getDeliveryAddress(),
                $order->getCity(),
                $order->getPostalCode(),
                $order->getNumberOfPeople(),
                $order->getTotalOrderPrice(),
                $order->getStatus(),
                $order->getEquipmentLoan(),
                $order->getEquipmentReturn(),
                $order->getIdUser(),
                $order->getId()
            ]);

            // supprime anciennes relations
            $this->pdo->prepare("DELETE FROM order_menu WHERE id_order = ?")
                      ->execute([$order->getId()]);

            $this->pdo->prepare("DELETE FROM order_material WHERE id_order = ?")
                      ->execute([$order->getId()]);

            $this->pdo->prepare("DELETE FROM order_drink_package WHERE id_order = ?")
                      ->execute([$order->getId()]);

            $this->pdo->prepare("DELETE FROM order_personal_package WHERE id_order = ?")
                      ->execute([$order->getId()]);

            // réinsertion
            $stmtMenu = $this->pdo->prepare("
                INSERT INTO order_menu (id_order, id_menu, number_people, price_person, subtotal) VALUES (?, ?, ?, ?, ?)
            ");
            foreach ($order->getMenus() as $menu) {
                $stmtMenu->execute([
                    $order->getId(),
                    $menu['id'],
                    $menu['number'],
                    $menu['price'],
                    $menu['subtotal']
                ]);
            }
              // insertion dans table pivot order_material
            $stmtMaterial = $this->pdo->prepare("
                INSERT INTO order_material (id_order, id_material, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)
            ");
            foreach ($order->getMaterials() as $material) {
               $stmtMaterial->execute([
                    $order->getId(),
                    $material['id'],
                    $material['number'],
                    $material['price'],
                    $material['subtotal']
                ]);
            }
                // insertion dans table pivot order_drink_package
            $stmtDrinkPackage = $this->pdo->prepare("
                INSERT INTO order_drink_package (id_order, id_drink_package, number_people, price_person, subtotal) VALUES (?, ?, ?, ?, ?)
            ");
            foreach ($order->getDrinkPackages() as $drinkPackage) {
                $stmtDrinkPackage->execute([
                    $order->getId(),
                    $drinkPackage['id'],
                    $drinkPackage['number'],
                    $drinkPackage['price'],
                    $drinkPackage['subtotal']
                ]);
            }
                // insertion dans table pivot order_personal_package
            $stmtPersonalPackage = $this->pdo->prepare("
                INSERT INTO order_personal_package (id_order, id_personal_package, number_people, price_package, subtotal) VALUES (?, ?, ?, ?, ?)
            ");
            foreach ($order->getPersonalPackages() as $personalPackage) {
                $stmtPersonalPackage->execute([
                    $order->getId(),
                    $personalPackage['id'],
                    $personalPackage['number'],
                    $personalPackage['price'],
                    $personalPackage['subtotal']
                ]);
            }

            $this->pdo->commit();

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
    /**
     * Supprime un commande.
     */
    public function delete(string $id): void
    {
        try {

            $this->pdo->beginTransaction();

            // supprimer d'abord les pivots
            $this->pdo->prepare("DELETE FROM order_menu WHERE id_order = ?")
                      ->execute([$id]);

            $this->pdo->prepare("DELETE FROM order_material WHERE id_order = ?")
                      ->execute([$id]);

            $this->pdo->prepare("DELETE FROM order_drink_package WHERE id_order = ?")
                      ->execute([$id]);

            $this->pdo->prepare("DELETE FROM order_personal_package WHERE id_order = ?")
                      ->execute([$id]);

            // supprimer le commande
            $stmt = $this->pdo->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) {
                throw new RuntimeException('Order not found');
            }

            $this->pdo->commit();

        } catch (\Throwable $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}