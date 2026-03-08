<?php

use App\Controller\MaterialController;
use App\Middleware\AuthMiddleware;
use App\Helper\CsrfHelper;

$router->get('/material', function() {
    (new MaterialController())->index();
});

$router->get('/material/{id}', function($id) {
    (new MaterialController())->show($id);
});

$allowedRoles = ['admin', 'employé'];

$router->post('/material', function() use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new MaterialController())->store();
});

$router->put('/material/{id}', function($id) use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new MaterialController())->update($id);
});

$router->delete('/material/{id}', function($id) use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new MaterialController())->delete($id);
});