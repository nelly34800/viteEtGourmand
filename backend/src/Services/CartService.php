<?php

namespace App\Services;

use App\Repository\MenuRepository;
use App\Repository\DrinkPackageRepository;
use App\Repository\PersonalPackageRepository;
use App\Repository\MaterialRepository;
use Database;

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
   * Transforme le panier brut reçu du LocalStorage en panier détaillé (avec validation, calculs et remises)
   */
    public function getDetailedCart(array $cart): array
    {
        // valide et nettoie le panier brut selon les règles métiers (dessous)
        $cart = $this->validateAndCleanCart($cart);
        // force le recalcul des quantités (boissons, personnel) sur le panier brut reçu
        $cart = $this->refreshPackagesQuantities($cart);
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
            // Gestion des forfaits de boissons
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
            // Gestion des forfaits de personnel
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
            // Gestion du matériel
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
     * Valide et nettoie le panier brut selon les règles métiers :
     * - pas de forfaits/matériels sans menu
     * - pas de quantité supérieure au stock disponible pour le matériel
     */
    private function validateAndCleanCart(array $cart): array
    {
        $hasMenu = $this->getTotalMenuPeople($cart) > 0;
        // si aucun menu n'est présent dans le panier reçu, on le vide de tout le reste
        if (!$hasMenu) {
            return [];
        }

        foreach ($cart as &$item) {
            if ($item['type'] === 'material') {
                $material = $this->materialRepository->findById($item['id']);
                if ($material) {
                    $maxAvailable = (int) $material->getQuantityAvailable();
                    // Si la quantité demandée dépasse le stock, on la bride au maximum disponible
                    if ($item['quantity'] > $maxAvailable) {
                        $item['quantity'] = $maxAvailable;
                    }
                }
            }
        }
        return $cart;
    }
    /**
     * Calcule la remise de 10% si le nombre de convives requis est dépassé de 5
     */
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
    /**
     * Calcul le nombre total de convives (somme des quantités de tous les menus présents)
     */
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
    /**
     * Calcule la quantité de personnel nécessaire selon le ratio configuré en BDD (arrondi au supérieur: ceil)
     */
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
     /**
     * Recalcule automatiquement les quantités des forfaits du panier brut reçu
     */
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
}