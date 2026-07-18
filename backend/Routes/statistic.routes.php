<?php

use App\Controller\StatisticsController;
use App\Middleware\AuthMiddleware;

$allowedRoles = ['admin'];

$router->get('/statistics/ordersbymenu', function() use ($allowedRoles) {
    AuthMiddleware::requireRole(['admin']);
    (new StatisticsController())->ordersByMenu();
});

$router->get('/statistics/revenuebymenu', function() use ($allowedRoles) {
    AuthMiddleware::requireRole($allowedRoles);
    (new StatisticsController())->revenueByMenu();
});