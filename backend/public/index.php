<?php
header('Content-Type: application/json');

require_once '../config/database.php';

session_start();
 
use App\Router\Router;

try {
    //fonction d'autoload pour charger les classes automatiquement
    spl_autoload_register(function ($class) {

    $prefix = "App\\";
    $baseDir = __DIR__ . '/../src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
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