<?php

use App\Controller\DrinkPackageController;
use App\Middleware\AuthMiddleware;
use App\Helper\CsrfHelper;

$router->get('/drinkPackage', function() {
    (new DrinkPackageController())->index();
});

$router->get('/drinkPackage/{id}', function($id) {
    (new DrinkPackageController())->show($id);
});

$allowedRoles = ['admin', 'employé'];

$router->post('/drinkPackage', function() use ($allowedRoles) {
      CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new DrinkPackageController())->store();
});

$router->put('/drinkPackage/{id}', function($id) use ($allowedRoles) {
      CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new DrinkPackageController())->update($id);
});

$router->delete('/drinkPackage/{id}', function($id) use ($allowedRoles) {
      CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new DrinkPackageController())->delete($id);
});