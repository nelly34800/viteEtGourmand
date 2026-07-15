<?php
// Liste des domaines autorisés à faire des requêtes vers l'API (variable dans le .env)
$allowedOrigins = [];

// Vérifie dans $_ENV OU via getenv() pour être sûr de capter Heroku
$originsRaw = $_ENV['ALLOWED_ORIGINS'] ?? getenv('ALLOWED_ORIGINS') ?? '';

if (!empty($originsRaw)) {
    $allowedOrigins = array_map(
        'trim',
        explode(',', $originsRaw)
    );
}
// Vérifie si la requête vient d’un domaine autorisé
if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
  // Autorise ce domaine à accéder à l'API
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
}
// Autorise les méthodes HTTP utilisées par l'API
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// Autorise certains headers côté frontend (important pour CSRF)
header("Access-Control-Allow-Headers: Content-Type, X-CSRF-Token");
// Autorise l'envoi des cookies (sessions PHP)
header("Access-Control-Allow-Credentials: true");
// Gère les requêtes préflight (OPTIONS) pour CORS
// Si c'est une requête OPTIONS, on répond avec 200 et on arrête le script
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
// Définit le type de réponse en JSON pour toute l’API
header('Content-Type: application/json');
// Autoload des classes (Composer)
require_once __DIR__ . '/../vendor/autoload.php';
// Connexion à la base de données
require_once '../config/database.php';
require_once '../config/mongodb.php';

// gestion dynamique HTTPS
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');


if (session_status() === PHP_SESSION_NONE) {
    // Configuration du cookie de session PHP avec des options de sécurité pour la production
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '', // Laissé vide pour qu'il s'adapte au domaine Heroku
        'secure' => true,     // Indispensable en HTTPS (Heroku)
        'httponly' => true,   // Protection contre le vol de cookies en JS
        'samesite' => 'None'  // Autorise la transmission si le front et le back sont sur deux URL Heroku différentes
    ]);
    session_start();
} 

use App\Helper\CsrfHelper;
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
    require_once '../Routes/schedule.routes.php';
    require_once '../Routes/user.routes.php';
    require_once '../Routes/auth.routes.php';
    require_once '../Routes/dish.routes.php';
    require_once '../Routes/categoryDish.routes.php';
    require_once '../Routes/allergen.routes.php';
    require_once '../Routes/diet.routes.php';
    require_once '../Routes/menu.routes.php';
    require_once '../Routes/condition.routes.php';
    require_once '../Routes/materialCategory.routes.php';
    require_once '../Routes/material.routes.php';
    require_once '../Routes/notice.routes.php';
    require_once '../Routes/drinkPackage.routes.php';
    require_once '../Routes/personalPackage.routes.php';
    require_once '../Routes/order.routes.php';
    require_once '../Routes/cart.routes.php';
    require_once '../Routes/delivery.routes.php';
    require_once '../Routes/contact.routes.php';
    require_once '../Routes/passwordReset.routes.php';
    require_once '../Routes/statistic.routes.php';

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
    echo json_encode(['error' =>  $e->getMessage() ]);
}