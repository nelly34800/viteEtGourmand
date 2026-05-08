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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
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

            $geocoder = new GeocodingService();
            $coords = $geocoder->geocode($address, $postalCode, $city);

            $service = new DeliveryService();
            $result = $service->calculate(
                (float) $coords['lat'],
                (float) $coords['lng']
            );

            // stocke en session
            $_SESSION['delivery'] = [
                'address' => $address,
                'city' => $city,
                'postal_code' => $postalCode,
                'latitude' => $coords['lat'],
                'longitude' => $coords['lng'],
                'distance_km' => $result['distance_km'],
                'delivery_charges' => $result['delivery_charges']
            ];

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