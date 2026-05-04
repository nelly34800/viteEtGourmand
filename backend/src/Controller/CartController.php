<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use App\Repository\DrinkPackageRepository;
use App\Repository\PersonalPackageRepository;
use App\Repository\MaterialRepository;
use App\Helper\RequestHelper;
use App\Helper\ResponseHelper;
use Database;
/**
 * Contrôleur responsable de la gestion du panier.
 */
class CartController
{

  private MenuRepository $menuRepository;
  private DrinkPackageRepository $drinkPackageRepository;
  private PersonalPackageRepository $personalPackageRepository;
  private MaterialRepository $materialRepository;

  public function __construct()
  {
    if (session_status() === PHP_SESSION_NONE) {
    session_start();
    }
    // Récupération de la connexion PDO et initialisation du repository
    $pdo = Database::getConnection();
    // Injection de la dépendance du repository
    $this->menuRepository = new MenuRepository($pdo);
    $this->drinkPackageRepository = new DrinkPackageRepository($pdo);
    $this->personalPackageRepository = new PersonalPackageRepository($pdo);
    $this->materialRepository = new MaterialRepository($pdo);
  }
  /**
  * Lit panier de l'utilisateur
  */
  public function index(): void
  {
    $cart = $_SESSION['cart'] ?? [];
    $result = [];

    foreach ($cart as $cartItem) {
      if ($cartItem['type'] === 'menu') {
        $menu = $this->menuRepository->findById($cartItem['id']);

        if ($menu) {
          $result[] = [
          'type' => 'menu',
          'id' => $menu->getId(),
          'name' => $menu->getMenuName(),
          'minimum_people' => $menu->getMinimumPeople(),
          'price_per_person' => $menu->getPricePerPerson(),
          'quantity' => $cartItem['quantity'],
          'line_total' => $cartItem['quantity'] * $menu->getPricePerPerson()
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
    ResponseHelper::json($result);
  }
  /**
   * Crée un nouveau panier
   */
  public function store(): void
  {
    try {
      $data = RequestHelper::getJson();

      if (!$data) {
          ResponseHelper::json(['error' => 'JSON invalide'], 400);
      }
      $type = $data['type'] ?? null;
      $id = $data['id'] ?? null;

      if (!$type || !$id) {
      ResponseHelper::json(['error' => 'Type ou référence manquant'], 400);
      }
      // récupère les infos du produit
      switch ($type) {
        case 'menu':
          $product = $this->menuRepository->findById($id);
          
          if (!$product) {
            ResponseHelper::json(['error' => 'Menu introuvable'], 404);
        }
        // calcul quantité minimum
        $quantity = max(
          (int) ($data['quantity'] ?? 1),
          (int) $product->getMinimumPeople()
        );
          break;
        case 'drink_package':
          $product = $this->drinkPackageRepository->findById($id);

          if (!$product) {
            ResponseHelper::json(['error' => 'Forfait boisson introuvable'], 404);
          }
          if ($this->getTotalMenuPeople() <= 0) {
            ResponseHelper::json(['error' => 'Ajoutez d’abord un menu avant un forfait personnel'], 400);
          }
          // Pour les forfaits boisson : quantité = nombre total de convives
          $quantity = $this->getTotalMenuPeople();
          break;

        case 'personal_package':
          $product = $this->personalPackageRepository->findById($id);
          
          if (!$product) {
            ResponseHelper::json(['error' => 'Forfait de personnel introuvable'], 404);
          }
          if ($this->getTotalMenuPeople() <= 0) {
            ResponseHelper::json(['error' => 'Ajoutez d’abord un menu avant un forfait personnel'], 400);
          }
          // Pour les forfaits de personnel : quantité = nombre total de convives/ratio
          $quantity = $this->getStaffQuantity($id);
          break;
        case 'material':
          $product = $this->materialRepository->findById($id);

          if (!$product) {
            ResponseHelper::json(['error' => 'Matériel introuvable'], 404);
          }

          if ($this->getTotalMenuPeople() <= 0) {
            ResponseHelper::json(['error' => 'Ajoutez d’abord un menu avant un matériel'], 400);
          }

          $quantity = (int) ($data['quantity'] ?? 1);

          if ($quantity <= 0) {
            ResponseHelper::json(['error' => 'Quantité invalide'], 400);
          }

          $alreadyInCart = $this->getItemQuantityInCart('material', $id);
          $totalRequested = $alreadyInCart + $quantity;

          if ($totalRequested > (int) $product->getQuantityAvailable()) {
            ResponseHelper::json([
              'error' => 'Stock insuffisant. Quantité disponible : ' . $product->getQuantityAvailable()
            ], 400);
          }

          break;

        default:
          ResponseHelper::json(['error' => 'Type invalide'], 400);
      }

      $cart = $_SESSION['cart'] ?? [];

      if (empty($cart)) {
        ResponseHelper::json(['error' => 'Panier vide'], 400);
      }

      foreach ($cart as &$item) {
        if ($item['type'] === $type && $item['id'] === $id) {
          if ($type === 'menu' || $type === 'material') {
            $item['quantity'] += $quantity;
          } else {
            $item['quantity'] = $quantity;
          }
          $cart = $this->refreshPackagesQuantities($cart);
          $_SESSION['cart'] = $cart;

        ResponseHelper::json(['success' => true, 'cart' => $_SESSION['cart']]);
        }
      }
      // nouvelle reférence
      $cart[] = [
        'type' => $type,
        'id' => $id,
        'quantity' => $quantity
      ];
      $cart = $this->refreshPackagesQuantities($cart);
      $_SESSION['cart'] = $cart;

      ResponseHelper::json(['success' => true,'cart' => $_SESSION['cart']]);

    } catch (\Throwable $e) { 
      ResponseHelper::json(['error' => $e->getMessage()], 400);
    }
  }

  private function getTotalMenuPeople(): int
  {
      $cart = $_SESSION['cart'] ?? [];
      $total = 0;

      foreach ($cart as $item) {
          if ($item['type'] === 'menu') {
              $total += (int) $item['quantity'];
          }
      }

      return $total;
  }
  // calcul ratio total pers/personnel (ceil: arrondis au dessus)
  private function getStaffQuantity(string $personalPackageId): int
{
    $totalPeople = $this->getTotalMenuPeople();

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
  // calcul du nombre de personnes pour les forfaits
  private function refreshPackagesQuantities(array $cart): array
  {
      $totalPeople = 0;

      foreach ($cart as $item) {
          if ($item['type'] === 'menu') {
              $totalPeople += (int) $item['quantity'];
          }
      }
      foreach ($cart as &$item) {
          if ($item['type'] === 'drink_package') {
              $item['quantity'] = $totalPeople;
          }
          if ($item['type'] === 'personal_package') {
              $item['quantity'] =  $this->getStaffQuantity($item['id']);
          }
      }
      return $cart;
  }
  // vérifier quantité materiel disponible
  private function getItemQuantityInCart(string $type, string $id): int
  {
    $cart = $_SESSION['cart'] ?? [];
    $quantity = 0;

    foreach ($cart as $item) {
      if ($item['type'] === $type && $item['id'] === $id) {
        $quantity += (int) $item['quantity'];
      }
    }
    return $quantity;
}

  public function update(string $id): void
  {
    $data = RequestHelper::getJson();
    $quantity = (int) ($data['quantity'] ?? 0);

    if ($quantity <= 0) {
        ResponseHelper::json(['error' => 'Quantité invalide'], 400);
    }

    $cart = $_SESSION['cart'] ?? [];

    foreach ($cart as &$item) {
      if ($item['id'] === $id) {
        // On ne modifie que les menus
        if ($item['type'] === 'menu') {
          $menu = $this->menuRepository->findById($id);

          if (!$menu) {
              ResponseHelper::json(['error' => 'Menu introuvable'], 404);
          }

          $item['quantity'] = max($quantity, (int) $menu->getMinimumPeople());
        }

        if ($item['type'] === 'material') {
            $material = $this->materialRepository->findById($id);

            if (!$material) {
                ResponseHelper::json(['error' => 'Matériel introuvable'], 404);
            }

            if ($quantity > (int) $material->getQuantityAvailable()) {
                ResponseHelper::json([
                    'error' => 'Stock insuffisant. Quantité disponible : ' . $material->getQuantityAvailable()
                ], 400);
            }

            $item['quantity'] = $quantity;
        }
            // Recalculer les forfaits après modification du panier
        $cart = $this->refreshPackagesQuantities($cart);
        $_SESSION['cart'] = $cart;

        ResponseHelper::json(['success' => true, 'cart' => $_SESSION['cart']]);
      }
    }
    ResponseHelper::json(['error' => 'Item non trouvé'], 404);
  }
  public function delete(string $id): void
  {
    $cart = $_SESSION['cart'] ?? [];

    $cart = array_filter($cart, function($item) use ($id) {
      return $item['id'] !== $id;
    });

    $cart = array_values($cart);

    $cart = $this->refreshPackagesQuantities($cart);

    $_SESSION['cart'] = $cart;

    ResponseHelper::json(['success' => true]);
  }
}