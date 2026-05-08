<?php

namespace App\Services;

use App\Repository\MenuRepository;
use App\Repository\DrinkPackageRepository;
use App\Repository\PersonalPackageRepository;
use App\Repository\MaterialRepository;
use Database;
use RuntimeException;

class CartService
{
    private MenuRepository $menuRepository;
    private DrinkPackageRepository $drinkPackageRepository;
    private PersonalPackageRepository $personalPackageRepository;
    private MaterialRepository $materialRepository;

    public function __construct()
    {
        // Récupération de la connexion PDO et initialisation du repository
        $pdo = Database::getConnection();
        // Injection de la dépendance du repository
        $this->menuRepository = new MenuRepository($pdo);
        $this->drinkPackageRepository = new DrinkPackageRepository($pdo);
        $this->personalPackageRepository = new PersonalPackageRepository($pdo);
        $this->materialRepository = new MaterialRepository($pdo);
    }
    /**
   * Transforme le panier session en panier détaillé (avec infos BDD)
   */
    public function getDetailedCart(array $cart): array
    {
        $result = [];

        foreach ($cart as $cartItem) {
            // gestion des menus
            if ($cartItem['type'] === 'menu') {
                $menu = $this->menuRepository->findById($cartItem['id']);

                if ($menu) {
                    // récupération des données nécessaires au calcul
                    $quantity = $cartItem['quantity'];
                    $price = $menu->getPricePerPerson();
                    $minimum = $menu->getMinimumPeople();

                    [$lineTotal, $discount] = $this->calculateMenuDiscount($quantity, $minimum, $price);

                    $result[] = [
                        'type' => 'menu',
                        'id' => $menu->getId(),
                        'name' => $menu->getMenuName(),
                        'minimum_people' => $minimum,
                        'price_per_person' => $price,
                        'discount' => $discount,
                        'quantity' => $quantity,
                        'line_total' => $lineTotal
                    ];
                }
            }

            if ($cartItem['type'] === 'drink_package') {
                $drinkPackage = $this->drinkPackageRepository->findById($cartItem['id']);

                if ($drinkPackage) {
                    $result[] = [
                        'type' => 'drink_package',
                        'id' => $drinkPackage->getId(),
                        'name' => $drinkPackage->getDrinkPackageName(),
                        'minimum_people' => 1,
                        'price_per_person' => $drinkPackage->getPricePerPerson(),
                        'quantity' => $cartItem['quantity'],
                        'line_total' => $cartItem['quantity'] * $drinkPackage->getPricePerPerson()
                    ];
                }
            }

            if ($cartItem['type'] === 'personal_package') {
                $personalPackage = $this->personalPackageRepository->findById($cartItem['id']);

                if ($personalPackage) {
                    $result[] = [
                        'type' => 'personal_package',
                        'id' => $personalPackage->getId(),
                        'name' => $personalPackage->getEventType(),
                        'minimum_people' => 1,
                        'price_per_person' => $personalPackage->getPackagePrice(),
                        'quantity' => $cartItem['quantity'],
                        'line_total' => $cartItem['quantity'] * $personalPackage->getPackagePrice()
                    ];
                }
            }

            if ($cartItem['type'] === 'material') {
                $material = $this->materialRepository->findById($cartItem['id']);

                if ($material) {
                    $result[] = [
                        'type' => 'material',
                        'id' => $material->getId(),
                        'name' => $material->getMaterialName(),
                        'minimum_people' => 1,
                        'price_per_person' => $material->getPrice(),
                        'quantity' => $cartItem['quantity'],
                        'available_quantity' => $material->getQuantityAvailable(),
                        'line_total' => $cartItem['quantity'] * $material->getPrice()
                    ];
                }
            }
        }

        return $result;
    }
    /**
   * Ajoute un produit dans le panier
   * gère les règles spécifiques selon le type
   */
    public function add(array $cart, array $data): array
    {
        $type = $data['type'] ?? null;
        $id = $data['id'] ?? null;

        if (!$type || !$id) {
            throw new RuntimeException('Type ou référence manquant');
        }
        // calcule la quantité selon le type (menu, forfait ou matériel)
        $quantity = $this->resolveQuantity($cart, $type, $id, $data);

        foreach ($cart as &$item) {
            if ($item['type'] === $type && $item['id'] === $id) {
                if ($type === 'menu' || $type === 'material') {
                    $item['quantity'] += $quantity;
                } else {
                    $item['quantity'] = $quantity;
                }

                return $this->refreshPackagesQuantities($cart);
            }
        }

        $cart[] = [
            'type' => $type,
            'id' => $id,
            'quantity' => $quantity
        ];

        return $this->refreshPackagesQuantities($cart);
    }

    public function update(array $cart, string $id, array $data): array
    {
        $quantity = (int) ($data['quantity'] ?? 0);

        if ($quantity <= 0) {
            throw new RuntimeException('Quantité invalide');
        }

        foreach ($cart as &$item) {
            if ($item['id'] === $id) {
                if ($item['type'] === 'menu') {
                    $menu = $this->menuRepository->findById($id);

                    if (!$menu) {
                        throw new RuntimeException('Menu introuvable');
                    }

                    $item['quantity'] = max($quantity, (int) $menu->getMinimumPeople());
                }

                if ($item['type'] === 'material') {
                    $material = $this->materialRepository->findById($id);

                    if (!$material) {
                        throw new RuntimeException('Matériel introuvable');
                    }

                    if ($quantity > (int) $material->getQuantityAvailable()) {
                        throw new RuntimeException(
                            'Stock insuffisant. Quantité disponible : ' . $material->getQuantityAvailable()
                        );
                    }

                    $item['quantity'] = $quantity;
                }

                return $this->refreshPackagesQuantities($cart);
            }
        }

        throw new RuntimeException('Item non trouvé');
    }

    public function delete(array $cart, string $id): array
    {
        $cart = array_filter($cart, function ($item) use ($id) {
            return $item['id'] !== $id;
        });

        $cart = array_values($cart);

        return $this->refreshPackagesQuantities($cart);
    }
    /**
    * Détermine quantité à ajouter selon le type de produit
    * 
    * - menu → minimum de personnes respecté
    * - drink_package → basé sur total convives
    * - personal_package → basé sur ratio personnel
    * - material → limité par stock disponible
    */
    private function resolveQuantity(array $cart, string $type, string $id, array $data): int
    {
        // récupère les infos du produit
        switch ($type) {
            case 'menu':
                $product = $this->menuRepository->findById($id);

                if (!$product) {
                    throw new RuntimeException('Menu introuvable');
                }
                // calcul quantité minimum
                return max(
                    (int) ($data['quantity'] ?? 1),
                    (int) $product->getMinimumPeople()
                );

            case 'drink_package':
                $product = $this->drinkPackageRepository->findById($id);

                if (!$product) {
                    throw new RuntimeException('Forfait boisson introuvable');
                }

                if ($this->getTotalMenuPeople($cart) <= 0) {
                    throw new RuntimeException('Ajoutez d’abord un menu avant un forfait boisson');
                }

                return $this->getTotalMenuPeople($cart);

            case 'personal_package':
                $product = $this->personalPackageRepository->findById($id);

                if (!$product) {
                    throw new RuntimeException('Forfait de personnel introuvable');
                }

                if ($this->getTotalMenuPeople($cart) <= 0) {
                    throw new RuntimeException('Ajoutez d’abord un menu avant un forfait personnel');
                }

                return $this->getStaffQuantity($cart, $id);

            case 'material':
                $product = $this->materialRepository->findById($id);

                if (!$product) {
                    throw new RuntimeException('Matériel introuvable');
                }

                if ($this->getTotalMenuPeople($cart) <= 0) {
                    throw new RuntimeException('Ajoutez d’abord un menu avant un matériel');
                }

                $quantity = (int) ($data['quantity'] ?? 1);

                if ($quantity <= 0) {
                    throw new RuntimeException('Quantité invalide');
                }

                $alreadyInCart = $this->getItemQuantityInCart($cart, 'material', $id);
                $totalRequested = $alreadyInCart + $quantity;

                if ($totalRequested > (int) $product->getQuantityAvailable()) {
                    throw new RuntimeException(
                        'Stock insuffisant. Quantité disponible : ' . $product->getQuantityAvailable()
                    );
                }

                return $quantity;

            default:
                throw new RuntimeException('Type invalide');
        }
    }
    // calcul la remise s'il y en à une
    private function calculateMenuDiscount(int $quantity, int $minimum, float $price): array
    {
        $lineTotal = $quantity * $price;
        $discount = 0;

        if ($quantity >= ($minimum + 5)) {
            $discount = $lineTotal * 0.10;
            $lineTotal -= $discount;
        }

        return [$lineTotal, $discount];
    }
    // Calcul nombre total de convives (somme des menus) pour les forfaits
    private function getTotalMenuPeople(array $cart): int
    {
        $total = 0;

        foreach ($cart as $item) {
            if ($item['type'] === 'menu') {
                $total += (int) $item['quantity'];
            }
        }

        return $total;
    }
    // calcul ratio total pers/personnel (ceil: arrondis au dessus)
    private function getStaffQuantity(array $cart, string $personalPackageId): int
    {
        $totalPeople = $this->getTotalMenuPeople($cart);

        $personalPackage = $this->personalPackageRepository->findById($personalPackageId);

        if (!$personalPackage) {
            return 0;
        }

        $ratio = (int) $personalPackage->getStaffRatio();

        if ($ratio <= 0) {
            return 0;
        }

        return (int) ceil($totalPeople / $ratio);
    }
    // recalcul du nombre de personnes pour les forfaits après modification du panier
    private function refreshPackagesQuantities(array $cart): array
    {
        $totalPeople = $this->getTotalMenuPeople($cart);

        foreach ($cart as &$item) {
            if ($item['type'] === 'drink_package') {
                $item['quantity'] = $totalPeople;
            }

            if ($item['type'] === 'personal_package') {
                $item['quantity'] = $this->getStaffQuantity($cart, $item['id']);
            }
        }

        return $cart;
    }
    // vérifier quantité materiel disponible
    private function getItemQuantityInCart(array $cart, string $type, string $id): int
    {
        $quantity = 0;

        foreach ($cart as $item) {
            if ($item['type'] === $type && $item['id'] === $id) {
                $quantity += (int) $item['quantity'];
            }
        }

        return $quantity;
    }
}