<?php

use App\Controller\ConditionController;
use App\Middleware\AuthMiddleware;
use App\Helper\CsrfHelper;

$router->get('/condition', function() {
    (new ConditionController())->index();
});
$router->get('/condition/{id}', function($id) {
    (new ConditionController())->show($id);
});

$allowedRoles = ['admin', 'employé'];

$router->post('/condition', function() use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new ConditionController())->store();
});

$router->put('/condition/{id}', function($id) use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new ConditionController())->update($id);
});

$router->delete('/condition/{id}', function($id) use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new ConditionController())->delete($id);
});