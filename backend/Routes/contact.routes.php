<?php

use App\Controller\ContactController;
use App\Middleware\AuthMiddleware;
use App\Helper\CsrfHelper;

$router->post('/contact', function() {
    (new ContactController())->send();
});