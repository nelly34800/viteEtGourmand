<?php

use App\Controller\CartController;
use App\Middleware\AuthMiddleware;
use App\Helper\CsrfHelper;

$allowedRoles = ['client'];


$router->post('/cart/details', function() use ($allowedRoles) {
     CsrfHelper::validate();
     AuthMiddleware::requireRole($allowedRoles);
    (new CartController())->getDetails();
});

$router->post('/cart', function() use ($allowedRoles) {
     CsrfHelper::validate();
     AuthMiddleware::requireRole($allowedRoles);
    (new CartController())->store();
});
