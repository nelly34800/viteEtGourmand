<?php

use App\Controller\DietController;
use App\Middleware\AuthMiddleware;

$router->get('/diet', function() {
    (new DietController())->index();
});

$router->get('/diet/{id}', function($id) {
    (new DietController())->show($id);
});

$allowedRoles = ['admin', 'employé'];

$router->post('/diet', function() use ($allowedRoles) {
    AuthMiddleware::requireRole($allowedRoles);
    (new DietController())->store();
});

$router->put('/diet/{id}', function($id) use ($allowedRoles) {
    AuthMiddleware::requireRole($allowedRoles);
    (new DietController())->update($id);
});

$router->delete('/diet/{id}', function($id) use ($allowedRoles) {
    AuthMiddleware::requireRole($allowedRoles);
    (new DietController())->delete($id);
});