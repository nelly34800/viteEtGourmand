<?php

use App\Controller\ScheduleController;
use App\Middleware\AuthMiddleware;

$router->get('/schedule', function() {
    (new ScheduleController())->index();
});

$router->get('/schedule/{id}', function($id) {
    (new ScheduleController())->show($id);
});

$allowedRoles = ['admin', 'employe'];

$router->post('/schedule', function() use ($allowedRoles) {
    AuthMiddleware::requireRole($allowedRoles);
    (new ScheduleController())->store();
});

$router->put('/schedule/{id}', function($id) use ($allowedRoles) {
    AuthMiddleware::requireRole($allowedRoles);
    (new ScheduleController())->update($id);
});

$router->delete('/schedule/{id}', function($id) use ($allowedRoles) {
    AuthMiddleware::requireRole($allowedRoles);
    (new ScheduleController())->delete($id);
});
