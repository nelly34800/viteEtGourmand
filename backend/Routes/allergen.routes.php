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
      CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new AllergenController())->store();
});

$router->put('/allergen/{id}', function($id) use ($allowedRoles) {
      CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new AllergenController())->update($id);
});

$router->delete('/allergen/{id}', function($id) use ($allowedRoles) {
      CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new AllergenController())->delete($id);
});
