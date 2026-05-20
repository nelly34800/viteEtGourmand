<?php

namespace App\Controller;

use mongodb;
use App\Services\StatisticsService;
use App\Helper\ResponseHelper;

class StatisticsController
{
    private StatisticsService $statisticsService;

    public function __construct()
    {
        global $mongoDb;
        // injecte la dépendance du service de statistiques en lui passant la connexion à MongoDB
        $this->statisticsService = new StatisticsService($mongoDb);
    }
    // nombre de commandes par menu
    public function ordersByMenu(): void
    {
        $data = $this->statisticsService->getOrdersByMenu();

        ResponseHelper::json($data);
    }

    // chiffre d'affaires par menu
    public function revenueByMenu(): void
    {
        $menuName = isset($_GET['menu_name']) && $_GET['menu_name'] !== ''
            ? $_GET['menu_name']
            : null;

        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;

        $data = $this->statisticsService->getRevenueByMenu(
            $menuName,
            $startDate,
            $endDate
        );
        ResponseHelper::json($data);
    }
}