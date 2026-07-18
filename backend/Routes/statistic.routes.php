<?php

use App\Controller\StatisticsController;
use App\Middleware\AuthMiddleware;

$allowedRoles = ['admin'];

$router->get('/statistics/ordersByMenu', function() use ($allowedRoles) {
   // On commente temporairement l'auth et le contrôleur et renvoie text brut
    // AuthMiddleware::requireRole(['admin']);
    // (new StatisticsController())->ordersByMenu();
    
    echo json_encode(["message" => "La route fonctionne sur Heroku !"]);
    exit;
});

$router->get('/statistics/revenueByMenu', function() use ($allowedRoles) {
    AuthMiddleware::requireRole($allowedRoles);
    (new StatisticsController())->revenueByMenu();
});