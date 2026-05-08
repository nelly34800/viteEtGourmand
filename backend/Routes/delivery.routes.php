<?php

use App\Controller\DeliveryController;
use App\Middleware\AuthMiddleware;
use App\Helper\CsrfHelper;

$allowedRoles = ['client'];

$router->post('/delivery_charges', function() use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new DeliveryController())->calculate();
});