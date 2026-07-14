<?php

use App\Controller\CartController;
use App\Middleware\AuthMiddleware;

$allowedRoles = ['client'];

$router->post('/cart/details', function() use ($allowedRoles) {
     AuthMiddleware::requireRole($allowedRoles);
    (new CartController())->getDetails();
});