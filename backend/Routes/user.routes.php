<?php

use App\Controller\UserController;
use App\Middleware\AuthMiddleware;

$router->get('/user/{id}', function($id) {
    (new UserController())->show($id);
});


$router->post('/user', function() {
    (new UserController())->store();
});

$router->put('/user/{id}', function($id) {
    (new UserController())->update($id);
});

$router->delete('/user/{id}', function($id) {
    (new UserController())->delete($id);
});

$allowedRoles = ['admin'];

$router->get('/user', function() use ($allowedRoles) {
    AuthMiddleware::requireRole($allowedRoles);
    (new UserController())->index();
});