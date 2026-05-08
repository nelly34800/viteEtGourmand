<?php

use App\Controller\OrderController;
use App\Middleware\AuthMiddleware;
use App\Helper\CsrfHelper;

$router->get('/order', function() {
    AuthMiddleware::requireAuth();
    (new OrderController())->index();
});

$router->get('/order/{id}', function($id)  {
    AuthMiddleware::requireAuth();
    (new OrderController())->show($id);
});

$allowedRoles = ['client'];

$router->post('/order', function() use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new OrderController())->store();
});

$router->put('/order/{id}', function($id) {
    CsrfHelper::validate();
    AuthMiddleware::requireAuth();
    (new OrderController())->update($id);
});

$router->delete('/order/{id}', function($id) {
    CsrfHelper::validate();
    AuthMiddleware::requireAuth();
    (new OrderController())->delete($id);
});

$allowedRoles2 = ['admin', 'employé'];

$router->put('/order/{id}/updateStatus', function($id) use ($allowedRoles2) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles2);
    (new OrderController())->updateStatus($id);
});