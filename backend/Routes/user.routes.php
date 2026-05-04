<?php

use App\Controller\UserController;
use App\Middleware\AuthMiddleware;
use App\Helper\CsrfHelper;

$router->get('/user/{id}', function($id) {
    AuthMiddleware::requireAuth();
    (new UserController())->show($id);
});

$router->post('/user', function() {
    (new UserController())->store();
});

$router->put('/user/{id}', function($id) {
    CsrfHelper::validate();
    AuthMiddleware::requireAuth();
    (new UserController())->updateInfo($id);
});

$router->delete('/user/{id}', function($id) {
    CsrfHelper::validate();
    AuthMiddleware::requireAuth();
    (new UserController())->delete($id);
});

$allowedRoles = ['admin'];

$router->get('/user', function() use ($allowedRoles) {
    AuthMiddleware::requireRole($allowedRoles);
    (new UserController())->index();
});

$router->post('/employee', function() use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new UserController())->createEmployee();
});