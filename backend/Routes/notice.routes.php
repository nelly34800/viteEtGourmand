<?php

use App\Controller\NoticeController;
use App\Middleware\AuthMiddleware;
use App\Helper\CsrfHelper;

$router->get('/notice', function() {
    CsrfHelper::validate();
    AuthMiddleware::requireAuth();
    (new NoticeController())->index();
});

$router->get('/notice/{id}', function($id) {
    CsrfHelper::validate();
    AuthMiddleware::requireAuth();
    (new NoticeController())->show($id);
});

$router->post('/notice', function() use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireAuth();
    (new NoticeController())->store();
});

$router->put('/notice/{id}', function($id) use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireAuth();
    (new NoticeController())->update($id);
});

$router->delete('/notice/{id}', function($id) use ($allowedRoles) {
    CsrfHelper::validate();
    AuthMiddleware::requireAuth();
    (new NoticeController())->delete($id);
});