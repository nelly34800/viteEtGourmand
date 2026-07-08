<?php

namespace App\Services;

class DeliveryService
{
    // coordonnées du traiteur
    private float $originLat = 44.837789;
    private float $originLng = -0.579180;
    // calculer les frais de livraisons 
    public function calculate(float $destLat, float $destLng): array
    {
      //récupération des coordonnées du traiteur et du client et appel de la fonction haversine pour calculer la distance entre les deux points
        $distance = $this->haversine(
            $this->originLat,
            $this->originLng,
            $destLat,
            $destLng
        );

        $price = 5 + ($distance * 0.59);

        return [
            'distance_km' => round($distance, 2),
            'delivery_charges' => round($price, 2)
        ];
    }
    // formule de Haversine: calcule distance entre 2 villes avec coordonnées (latitude / longitude).
    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}