<?php

use App\Controller\DishController;
use App\Middleware\AuthMiddleware;
use App\Helper\CsrfHelper;

$router->get('/dish', function() {
    (new DishController())->index();
});

$router->get('/dish/{id}', function($id) {
    (new DishController())->show($id);
});

$allowedRoles = ['admin', 'employé'];

$router->post('/dish', function() use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new DishController())->store();
});

$router->put('/dish/{id}', function($id) use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new DishController())->update($id);
});

$router->delete('/dish/{id}', function($id) use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new DishController())->delete($id);
});