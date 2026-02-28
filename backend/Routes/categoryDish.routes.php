<?php

use App\Controller\CategoryDishController;

$router->get('/categoryDish', function() {
    (new CategoryDishController())->index();
});

$router->get('/categoryDish/{id}', function($id) {
    (new CategoryDishController())->show($id);
});