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
    /**
     * Reçoit le panier actuel + le nouvel item, et renvoie le nouveau panier brut combiné
     * lorsque le client clique sur "Ajouter au panier" depuis les pages des options (matériel, forfaits)
     */
    public function store(): void
    {
        try {
            $data = RequestHelper::getJson();

            if (!$data) {
                ResponseHelper::json(['error' => 'JSON invalide'], 400);
            }
            // Le JS envoie { cart: [...], type: '...', id: '...', quantity: ... }
            $currentCart = $data['cart'] ?? [];

            // cartService applique ses règles d'ajout (ex: vérification des stocks)
            $updatedCart = $this->cartService->add($currentCart, $data);

            ResponseHelper::json([
                'success' => true,
                'cart' => $updatedCart // Le frontend écrase son localStorage
            ]);

        } catch (\Throwable $e) {
            ResponseHelper::json(['error' => $e->getMessage()], 400);
        }
    }
}