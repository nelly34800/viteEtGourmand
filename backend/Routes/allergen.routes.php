<?php

use App\Controller\AllergenController;
use App\Middleware\AuthMiddleware;
use App\Helper\CsrfHelper;

$router->get('/allergen', function() {
    (new AllergenController())->index();
});

$router->get('/allergen/{id}', function($id) {
    (new AllergenController())->show($id);
});

$allowedRoles = ['admin', 'employé'];

$router->post('/allergen', function() use ($allowedRoles) {
    AuthMiddleware::requireRole($allowedRoles);
    CsrfHelper::validate();
    (new AllergenController())->store();
});

$router->put('/allergen/{id}', function($id) use ($allowedRoles) {
    AuthMiddleware::requireRole($allowedRoles);
    CsrfHelper::validate();
    (new AllergenController())->update($id);
});

$router->delete('/allergen/{id}', function($id) use ($allowedRoles) {
    AuthMiddleware::requireRole($allowedRoles);
    CsrfHelper::validate();
    (new AllergenController())->delete($id);
});
