<?php

use App\Controller\DeliveryController;
use App\Middleware\AuthMiddleware;

$allowedRoles = ['client'];

$router->post('/delivery_charges', function() use ($allowedRoles) {
    AuthMiddleware::requireRole($allowedRoles);
    (new DeliveryController())->calculate();
});