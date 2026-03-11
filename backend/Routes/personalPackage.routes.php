<?php

use App\Controller\PersonalPackageController;
use App\Middleware\AuthMiddleware;
use App\Helper\CsrfHelper;

$router->get('/personalPackage', function() {
    (new PersonalPackageController())->index();
});

$router->get('/personalPackage/{id}', function($id) {
    (new PersonalPackageController())->show($id);
});

$allowedRoles = ['admin', 'employé'];

$router->post('/personalPackage', function() use ($allowedRoles) {
      CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new PersonalPackageController())->store();
});

$router->put('/personalPackage/{id}', function($id) use ($allowedRoles) {
      CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new PersonalPackageController())->update($id);
});

$router->delete('/personalPackage/{id}', function($id) use ($allowedRoles) {
      CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new PersonalPackageController())->delete($id);
});