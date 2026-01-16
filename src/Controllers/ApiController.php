<?php
namespace App\Controllers;

use App\Models\Secret;
use App\Models\User;
use App\Utils\Crypto;


class ApiController {

    /**
     * Méthode privée pour vérifier l'authentification
     * Renvoie l'utilisateur si OK, sinon coupe le script avec une erreur 401
     */
    private function authenticate() {
        // 1. Récupérer le Header "Authorization"
        // Astuce : Dans certains serveurs, c'est dans $_SERVER['HTTP_AUTHORIZATION']
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        // 2. Vérifier format "Bearer <token>"
        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(['error' => 'Token manquant (Format attendu: Bearer <votre_token>)']);
            exit;
        }

        $token = $matches[1]; // Le code hexadécimal extrait

        // 3. Vérifier en BDD
        $userModel = new User();
        $user = $userModel->findByToken($token);

        if (!$user) {
            http_response_code(403); // Forbidden
            echo json_encode(['error' => 'Token invalide ou expiré']);
            exit;
        }

        return $user; // On retourne l'utilisateur trouvé
    }
    
    /**
     * POST /api/login
     * Échange Email + Password -> Token
     */
    public function login() {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(400); echo json_encode(['error' => 'Champs manquants']); exit;
        }

        $userModel = new User();
        $user = $userModel->findByEmail($data['email']);

        if (!$user || !password_verify($data['password'], $user['password_hash'])) {
            http_response_code(401); echo json_encode(['error' => 'Identifiants incorrects']); exit;
        }

        // Succès : On renvoie le token stocké en base
        echo json_encode([
            'status' => 'success',
            'token' => $user['api_token']
        ]);
        exit;
    }

    // GET /api/secrets
    public function index() {
        // --- SÉCURITÉ ---
        $user = $this->authenticate(); // Si ça passe, on récupère $user. Sinon ça exit.
        // ----------------

        $secretModel = new Secret();
        // FIN DE LA TRICHE : On utilise le vrai ID de l'utilisateur authentifié
        $secrets = $secretModel->getAllByUser($user['id']); 

        $data = array_map(function($s) {
            return ['id' => $s['id'], 'titre' => $s['title'], 'login' => $s['login']];
        }, $secrets);

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    // POST /api/secrets
    public function store() {
        // --- SÉCURITÉ ---
        $user = $this->authenticate();
        // ----------------

        $jsonBrut = file_get_contents('php://input');
        $data = json_decode($jsonBrut, true);

        if (empty($data['title']) || empty($data['password'])) {
            http_response_code(400); 
            echo json_encode(['error' => 'Données incomplètes']);
            exit;
        }

        // 4. CHIFFREMENT : On ne stocke jamais en clair !
        try {
            // On convertit la clé hexadécimale (.env) en binaire brut pour OpenSSL
            $key = hex2bin(APP_KEY_HEX);
            
            // On appelle votre classe Crypto statique
            // Cela retourne le JSON complet (ciphertext + iv + tag)
            $encryptedPackage = Crypto::encrypt($data['password'], $key);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur de chiffrement serveur']);
            exit;
        }

        // 5. INSERTION : Appel au Modèle
        $secretModel = new Secret();
        
        // On suppose que votre modèle a une méthode create()
        // Si elle n'existe pas, voir le code ci-dessous
        $success = $secretModel->create(
            (int)$user['id'],   // 1. L'ID de l'utilisateur (issu du token)
            $data['title'],     // 2. Le Titre
            $data['login'],     // 3. Le Login
            $encryptedPackage   // 4. Le paquet chiffré JSON
        );

        if ($success) {
            http_response_code(201); // 201 Created
            header('Content-Type: application/json');
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Secret sauvegardé avec succès',
                'data' => [
                    'title' => $data['title'],
                    'login' => $data['login']
                ]
            ]);
        } else {
            // Si la requête SQL a échoué
            http_response_code(500);
            echo json_encode(['error' => 'Erreur lors de l\'enregistrement en base de données']);
        }
        exit;
    }

    /**
     * GET /api/secret?id=XX
     * Révèle un secret (Déchiffrement)
     */
    public function show() {
        $user = $this->authenticate(); // Sécurité

        $id = $_GET['id'] ?? 0;
        $secretModel = new Secret();
        $secret = $secretModel->getOne((int)$id);

        // Vérification stricte : le secret appartient-il à cet user ?
        if (!$secret || $secret['user_id'] !== $user['id']) {
            http_response_code(403); echo json_encode(['error' => 'Accès interdit']); exit;
        }

        // Déchiffrement
        try {
            $key = hex2bin(APP_KEY_HEX);
            $clearPassword = Crypto::decrypt($secret['encrypted_password'], $key);
        } catch (\Exception $e) {
            http_response_code(500); echo json_encode(['error' => 'Erreur déchiffrement']); exit;
        }
        header('Content-Type: application/json');
        echo json_encode([
            'id' => $secret['id'],
            'title' => $secret['title'],
            'password_revealed' => $clearPassword // Le Graal
        ]);
        exit;
    }
}