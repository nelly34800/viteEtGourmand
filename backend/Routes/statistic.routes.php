<?php

use App\Controller\StatisticsController;
use App\Middleware\AuthMiddleware;

$allowedRoles = ['admin'];

$router->get('/statistics/orders-by-menu', function() use ($allowedRoles) {
    AuthMiddleware::requireRole(['admin']);
    (new StatisticsController())->ordersByMenu();
});

$router->get('/statistics/revenue-by-menu', function() use ($allowedRoles) {
    AuthMiddleware::requireRole($allowedRoles);
    (new StatisticsController())->revenueByMenu();
});