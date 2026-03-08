<?php

use App\Controller\ScheduleController;
use App\Middleware\AuthMiddleware;
use App\Helper\CsrfHelper;

$router->get('/schedule', function() {
    (new ScheduleController())->index();
});

$router->get('/schedule/{id}', function($id) {
    (new ScheduleController())->show($id);
});

$allowedRoles = ['admin', 'employé'];

$router->post('/schedule', function() use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new ScheduleController())->store();
});

$router->put('/schedule/{id}', function($id) use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new ScheduleController())->update($id);
});

$router->delete('/schedule/{id}', function($id) use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new ScheduleController())->delete($id);
});
