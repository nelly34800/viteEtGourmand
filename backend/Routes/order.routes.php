<?php

use App\Controller\OrderController;
use App\Middleware\AuthMiddleware;
use App\Helper\CsrfHelper;

$router->get('/order', function() {
    CsrfHelper::validate();
    AuthMiddleware::requireAuth();
    (new OrderController())->index();
});

$router->get('/order/{id}', function($id) {
    CsrfHelper::validate();
    AuthMiddleware::requireAuth();
    (new OrderController())->show($id);
});

$router->post('/order', function() use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireAuth();
    (new OrderController())->store();
});

$router->put('/order/{id}', function($id) use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireAuth();
    (new OrderController())->update($id);
});

$router->delete('/order/{id}', function($id) use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireAuth();
    (new OrderController())->delete($id);
});