<?php

// 1. CHARGEMENT DE L'AUTOLOADER & CONFIG
// ---------------------------------------------------------
// On charge l'autoloader de Composer (remplace tous les require de classes)
require_once __DIR__ . '/../vendor/autoload.php';

// On charge les fichiers de configuration (qui ne sont pas des classes)
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/security.php';

// 2. IMPORT DES CLASSES
// ---------------------------------------------------------
use App\Controllers\AuthController;
use App\Controllers\DashboardController;

// 3. ANALYSE DE L'URL
// ---------------------------------------------------------
$requestUri = $_SERVER['REQUEST_URI'];
$uri = parse_url($requestUri, PHP_URL_PATH);

// 4. ROUTAGE (Version Match PHP 8)
// ---------------------------------------------------------
// On utilise match(true) pour conserver ta logique "str_ends_with"
// C'est beaucoup plus propre et lisible qu'une chaîne de if/else

try {
    match (true) {
        // Routes AUTH
        str_ends_with($uri, '/register') => (new AuthController())->register(),
        str_ends_with($uri, '/login')    => (new AuthController())->login(),
        
        // Routes DASHBOARD
        str_ends_with($uri, '/dashboard') => (new DashboardController())->index(),
        str_ends_with($uri, '/logout')    => (new DashboardController())->logout(),
        
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