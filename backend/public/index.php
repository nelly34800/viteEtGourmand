<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../config/database.php';

session_set_cookie_params([
    'httponly' => true,
    'secure' => true,
    'samesite' => 'Strict'
]);

session_start();

use App\Helper\CsrfHelper;

CsrfHelper::generate();
 
use App\Router\Router;

try {
    /**
     * Récupération méthode et URI
     */
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    $router = new Router();
    /**
     * On charge les fichiers de routes
     */
    require_once '../routes/schedule.routes.php';
    require_once '../routes/user.routes.php';
    require_once '../routes/auth.routes.php';
    require_once '../routes/dish.routes.php';
    require_once '../routes/categoryDish.routes.php';
    require_once '../routes/allergen.routes.php';
    require_once '../routes/diet.routes.php';
    require_once '../routes/menu.routes.php';
    require_once '../routes/condition.routes.php';
    require_once '../routes/materialCategory.routes.php';
    require_once '../routes/material.routes.php';
    require_once '../routes/notice.routes.php';
    require_once '../routes/drinkPackage.routes.php';
    require_once '../routes/personalPackage.routes.php';
    require_once '../routes/order.routes.php';

    $router->dispatch($method, $uri);

} catch (PDOException $e) {  //problème de bdd

    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);

}  catch (InvalidArgumentException $e) {  // erreur de validation

    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);

} catch (RuntimeException $e) {  //ressource non trouvée

    http_response_code(404);
    echo json_encode(['error' => $e->getMessage()]);

} catch (Throwable $e) {  //toutes les autres erreurs

    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}