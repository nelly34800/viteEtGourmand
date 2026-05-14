<?php

use App\Controller\PasswordResetController;

$router->post('/passwordReset', function() {
    (new PasswordResetController())->store();
});

$router->post('/passwordReset/update', function() {
    (new PasswordResetController())->updatePassword();
});