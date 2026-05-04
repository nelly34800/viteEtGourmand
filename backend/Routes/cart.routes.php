<?php

use App\Controller\CartController;
use App\Middleware\AuthMiddleware;
use App\Helper\CsrfHelper;

$allowedRoles = ['client'];

$router->get('/cart', function() use ($allowedRoles) {
    AuthMiddleware::requireRole($allowedRoles);
    (new CartController())->index();
});

$router->post('/cart', function() use ($allowedRoles) {
     CsrfHelper::validate();
     AuthMiddleware::requireRole($allowedRoles);
    (new CartController())->store();
});

$router->put('/cart/{id}', function($id) use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new CartController())->update($id);
});

$router->delete('/cart/{id}', function($id) use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new CartController())->delete($id);
});