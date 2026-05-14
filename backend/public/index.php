<?php
// Liste des domaines autorisés à faire des requêtes vers l'API
$allowedOrigins = [
    "http://localhost:8086",
    //à remplacer par l'url du site
    "https://monsite.com"
];
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

// gestion dynamique HTTPS
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
// Configuration du cookie de session PHP avec des options de sécurité
session_set_cookie_params([
    'httponly' => true,
    'secure' =>  $isHttps, //mettre true en production
    'samesite' => 'Lax'    // none en production si domaine séparé frontend/backend, sinon Lax
]);

session_start();

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
    require_once '../routes/cart.routes.php';
    require_once '../routes/delivery.routes.php';
    require_once '../routes/contact.routes.php';
    require_once '../routes/passwordReset.routes.php';

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