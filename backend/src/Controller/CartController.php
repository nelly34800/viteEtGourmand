<?php

namespace App\Controller;

use App\Services\CartService;
use App\Helper\RequestHelper;
use App\Helper\ResponseHelper;
/**
 * Contrôleur responsable de la gestion du panier.
 */
class CartController
{
    private CartService $cartService;

    public function __construct()
    {
      if (session_status() === PHP_SESSION_NONE) {
      session_start();
      }
      $this->cartService = new CartService();
    }
    /**
    * Lit panier de l'utilisateur
    */
    public function index(): void
    {
        $cart = $_SESSION['cart'] ?? [];
        $result = $this->cartService->getDetailedCart($cart);

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

            $cart = $_SESSION['cart'] ?? [];

            $_SESSION['cart'] = $this->cartService->add($cart, $data);

            ResponseHelper::json([
                'success' => true,
                'cart' => $_SESSION['cart']
            ]);

        } catch (\Throwable $e) {
            ResponseHelper::json(['error' => $e->getMessage()], 400);
        }
    }
    /**
     * Modifier le panier
     */
    public function update(string $id): void
    {
        try {
            $data = RequestHelper::getJson();
            $cart = $_SESSION['cart'] ?? [];

            $_SESSION['cart'] = $this->cartService->update($cart, $id, $data);

            ResponseHelper::json([
                'success' => true,
                'cart' => $_SESSION['cart']
            ]);

        } catch (\Throwable $e) {
            ResponseHelper::json(['error' => $e->getMessage()], 400);
        }
    }
    /**
     * Supprimer le panier
     */
    public function delete(string $id): void
    {
        try {
            $cart = $_SESSION['cart'] ?? [];

            $_SESSION['cart'] = $this->cartService->delete($cart, $id);

            ResponseHelper::json(['success' => true]);

        } catch (\Throwable $e) {
            ResponseHelper::json(['error' => $e->getMessage()], 400);
        }
    }
}