<?php

// 1. CHARGEMENT DE L'AUTOLOADER & CONFIG
// ---------------------------------------------------------
// On charge l'autoloader de Composer (remplace tous les require de classes)
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// --- CHARGEMENT DES VARIABLES D'ENVIRONNEMENT ---
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
// safeLoad() ne plante pas si le fichier manque, il ne fait rien.
$dotenv->safeLoad();

// On charge les fichiers de configuration (qui ne sont pas des classes)
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/security.php';

// 2. IMPORT DES CLASSES
// ---------------------------------------------------------
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ApiController;

// 3. ANALYSE DE L'URL
// ---------------------------------------------------------
$requestUri = $_SERVER['REQUEST_URI'];
$uri = parse_url($requestUri, PHP_URL_PATH);

// 4. ROUTAGE (Version Match PHP 8)
// ---------------------------------------------------------
// On utilise match(true) pour conserver la logique "str_ends_with"
// C'est beaucoup plus propre et lisible qu'une chaîne de if/else

try {
    match (true) {
        // Routes API
        //--------------------------------------------------

        // 1. Login (Pour obtenir le token)
        str_ends_with($uri, '/api/login') => (new ApiController())->login(),

        // 2. Secrets (Liste ou Création)
        str_ends_with($uri, '/api/secrets') =>
        $_SERVER['REQUEST_METHOD'] === 'POST'
            ? (new ApiController())->store()
            : (new ApiController())->index(),

        // 3. Révélation (Un seul secret)
        str_ends_with($uri, '/api/secret') => (new ApiController())->show(),

        // Routes Web
        //--------------------------------------------------

        // Routes AUTH
        str_ends_with($uri, '/register') => (new AuthController())->register(),
        str_ends_with($uri, '/login')    => (new AuthController())->login(),

        // Routes DASHBOARD
        str_ends_with($uri, '/dashboard') => (new DashboardController())->index(),
        str_ends_with($uri, '/logout')    => (new DashboardController())->logout(),
        str_ends_with($uri, '/delete') => (new DashboardController())->delete(),
        str_ends_with($uri, '/edit')   => (new DashboardController())->edit(),

        // Route PROFILE
        str_ends_with($uri, '/profile') => (new AuthController())->profile(),

        

        // Route RACINE (Redirection)
        str_ends_with($uri, '/') || str_ends_with($uri, '/index.php') => header('Location: register') && exit,

        // Route PAR DÉFAUT (404)
        default => throw new Exception("Page non trouvée"),
    };
} catch (Exception $e) {
    // Gestion centralisée des erreurs 404
    http_response_code(404);
    echo "<h1>404 - Page non trouvée</h1>";
    echo "<p>Le chemin '{$uri}' n'existe pas.</p>";
}
