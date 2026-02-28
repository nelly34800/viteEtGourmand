<?php

use App\Controller\AuthController;
use App\Middleware\AuthMiddleware;

$router->post('/login', function() {
    (new AuthController())->login();
});

$router->post('/logout', function() {
    AuthMiddleware::requireAuth();
    (new AuthController())->logout();
});