<?php

namespace App\Controller;

use App\Services\CartService;
use App\Helper\RequestHelper;
use App\Helper\ResponseHelper;
/**
 * Contrôleur responsable de la gestion du panier (Architecture Stateless / LocalStorage).
 */
class CartController
{
    private CartService $cartService;

    public function __construct()
    {
      $this->cartService = new CartService();
    }
    /**
    * Lit panier de l'utilisateur (reçoit le panier brut du LocalStorage et renvoie la version calculée)
    */
    public function getDetails(): void
    {
        try {
            $data = RequestHelper::getJson();
            // récupère le tableau brut envoyé par le JS, ou un tableau vide
            $rawCart = $data['cart'] ?? [];

            // calcul (remises, ratios, etc. dans cartService)
            $detailedCart = $this->cartService->getDetailedCart($rawCart);

            // Calcul du total général
            $totalGeneral = array_sum(array_column($detailedCart, 'line_total'));

            ResponseHelper::json([
                'detailed_cart' => $detailedCart,
                'total_general' => $totalGeneral
            ]);

        } catch (\Throwable $e) {
            ResponseHelper::json(['error' => $e->getMessage()], 400);
        }
    }
}