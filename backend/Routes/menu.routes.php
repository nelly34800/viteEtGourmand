<?php

use App\Controller\MenuController;
use App\Middleware\AuthMiddleware;
use App\Helper\CsrfHelper;

$router->get('/menu', function() {
    (new MenuController())->index();
});
$router->get('/menu/{id}', function($id) {
    (new MenuController())->show($id);
});

$allowedRoles = ['admin', 'employé'];

$router->post('/menu', function() use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new MenuController())->store();
});

$router->put('/menu/{id}', function($id) use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new MenuController())->update($id);
});

$router->delete('/menu/{id}', function($id) use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new MenuController())->delete($id);
});