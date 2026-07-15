<?php

use App\Controller\AuthController;
use App\Middleware\AuthMiddleware;
use App\Helper\CsrfHelper;

$router->post('/login', function() {
    (new AuthController())->login();
});

$router->post('/logout', function() {
    AuthMiddleware::requireAuth();
    (new AuthController())->logout();
});

$router->get('/checkSession', function() {
    (new AuthController())->checkSession();
});