<?php

use App\Controller\MaterialCategoryController;

$router->get('/materialCategory', function() {
    (new MaterialCategoryController())->index();
});

$router->get('/materialCategory/{id}', function($id) {
    (new MaterialCategoryController())->show($id);
});