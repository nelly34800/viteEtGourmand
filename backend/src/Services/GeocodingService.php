<?php

namespace App\Services;

use RuntimeException;

class GeocodingService
{
    // appel API openstreetmap pour récupérer coordonées
    public function geocode(string $address, string $postalCode, string $city): array
    {
        $query = trim($address . ', ' . $postalCode . ' ' . $city . ', France');

        $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
            'q' => $query,
            'format' => 'json',
            'limit' => 1,
            'addressdetails' => 1
        ]);

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: ViteEtGourmand/1.0 djasouboutique@gmail.com',
                    'Accept: application/json'
                ]
            ]
        ]);

        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            throw new RuntimeException("Erreur lors du géocodage de l'adresse");
        }

        $data = json_decode($response, true);

        if (empty($data[0])) {
            throw new RuntimeException("Adresse introuvable");
        }

        return [
            'lat' => (float) $data[0]['lat'],
            'lng' => (float) $data[0]['lon'],
            'display_name' => $data[0]['display_name']
        ];
    }
}