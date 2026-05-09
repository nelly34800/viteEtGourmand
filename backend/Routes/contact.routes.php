<?php

use App\Controller\ContactController;
use App\Middleware\AuthMiddleware;
use App\Helper\CsrfHelper;

$router->post('/contact', function() {
    CsrfHelper::validate();
    AuthMiddleware::requireAuth();
    (new ContactController())->send();
});