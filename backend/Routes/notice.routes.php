<?php

use App\Controller\NoticeController;
use App\Middleware\AuthMiddleware;
use App\Helper\CsrfHelper;

$router->get('/notice', function() {
    AuthMiddleware::requireAuth();
    (new NoticeController())->index();
});

$router->get('/noticeValidate', function() {
    (new NoticeController())->indexValidate();
});

$router->get('/notice/{id}', function($id) {
    AuthMiddleware::requireAuth();
    (new NoticeController())->show($id);
});

$allowedRoles = ['client'];
$allowedRoles2 = ['admin', 'employé'];

$router->get('/noticeUnvalidated', function() use ($allowedRoles2) {
    AuthMiddleware::requireRole($allowedRoles2);
    (new NoticeController())->indexUnvalidated();
});

$router->post('/notice', function() use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles);
    (new NoticeController())->store();
});

$router->put('/notice/{id}', function($id) use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole();
    (new NoticeController())->update($id);
});

$router->delete('/notice/{id}', function($id) use ($allowedRoles2) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles2);
    (new NoticeController())->delete($id);
});

$router->put('/notice/{id}/updateStatus', function($id) use ($allowedRoles2) {
    CsrfHelper::validate();
    AuthMiddleware::requireRole($allowedRoles2);
    (new NoticeController())->updateStatus($id);
});