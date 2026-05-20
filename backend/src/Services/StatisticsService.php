<?php

namespace App\Services;

use MongoDB\Database;
use MongoDB\BSON\UTCDateTime;

class StatisticsService
{
    private $collection;

    public function __construct(Database $mongoDb)
    {
        $this->collection = $mongoDb->selectCollection('orders_stats');
    }
  //  enregistre les statistiques des commandes dans MongoDB
    public function saveOrderStatistics(array $order): void
    {
        $this->collection->insertOne([
            'order_id' => $order['order_id'],
            'created_at' => new UTCDateTime(),
            'total_order' => (float) $order['total_order'],
            'items' => $order['items']
        ]);
    }
  //  récupère le nombre de commandes par menu
    public function getOrdersByMenu(): array
    {
        // prépare requête à mongodb
        $stats = [
            ['$unwind' => '$items'],
            [
                // additionne les quantités commandées pour chaque menu
                '$group' => [
                    '_id' => '$items.menu_name',
                    'nombre_commandes' => ['$sum' => '$items.quantity']
                ]
            ],
            // tri les résultats par nombre de commandes décroissant (+ -> -)
            ['$sort' => ['nombre_commandes' => -1]]
        ];

        return iterator_to_array($this->collection->aggregate($stats));
    }
    // récupère le chiffre d'affaires par menu (reçoit nom du menu et dates de début et fin)
    public function getRevenueByMenu(?string $menuName = null, ?string $startDate = null, ?string $endDate = null): array
    {
    // prépare requête à mongodb ($unwind déplie le tableau des menus)
      $pipeline = [
          ['$unwind' => '$items']
      ];
      //tableau vide pour stocker les filtres
      $match = [];
      // si un menu est sélectionné, filtre les résultats par nom de menu
      if ($menuName !== null) {
          $match['items.menu_name'] = $menuName;
      }

      if ($startDate !== null && $endDate !== null) {
          $match['created_at'] = [
              // convertit les dates en format UTCDateTime pour MongoDB
              '$gte' => new UTCDateTime(strtotime($startDate . ' 00:00:00') * 1000),
              '$lte' => new UTCDateTime(strtotime($endDate . ' 23:59:59') * 1000)
          ];
      }
      //si on a un filtre on l'ajoute pour que mongo applique les conditions
      if (!empty($match)) {
          $pipeline[] = ['$match' => $match];
      }
      // groupe les resultat par nom de menu et additionne les prix des menus
      $pipeline[] = [
          '$group' => [
              '_id' => '$items.menu_name', //_id = clée de regroupement ex: group by en sql
              'chiffre_affaires' => ['$sum' => '$items.total_price']
          ]
      ];
      // trie les résultat par chiffre d'affaires décroissant
      $pipeline[] = [
          '$sort' => ['chiffre_affaires' => -1]
      ];
      // execute la requête et retourne le tableau des resultats
      return iterator_to_array($this->collection->aggregate($pipeline));
  }
}