<?php

namespace App\Controller;

use App\Services\GeocodingService;
use App\Services\DeliveryService;
use App\Helper\RequestHelper;
use App\Helper\ResponseHelper;
/**
 * Contrôleur responsable des frais de livraison.
 */
class DeliveryController
{
    public function __construct()
    {
    }

    public function calculate(): void
    {
      try {
            $data = RequestHelper::getJson();
            // récupère les infos en session
            $address = $data['address'] ?? '';
            $city = $data['city'] ?? '';
            $postalCode = $data['postalCode'] ?? '';

            if (!$address || !$postalCode || !$city) {
                ResponseHelper::json(['error' => 'Adresse complète requise'], 400);
            }
            // géocodage de l'adresse
            $geocoder = new GeocodingService();
            $coords = $geocoder->geocode($address, $postalCode, $city);
            // Calcul de la distance et des frais
            $service = new DeliveryService();
            $result = $service->calculate(
                (float) $coords['lat'],
                (float) $coords['lng']
            );

            ResponseHelper::json([
                'success' => true,
                'address_found' => $coords['display_name'],
                'latitude' => $coords['lat'],
                'longitude' => $coords['lng'],
                'distance_km' => $result['distance_km'],
                'delivery_charges' => $result['delivery_charges']
            ]);

        } catch (\Throwable $e) {
            ResponseHelper::json(['error' => $e->getMessage()], 400);
        }
    }
}