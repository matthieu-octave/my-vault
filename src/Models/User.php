<?php

namespace App\Models;

use PDO;
use App\Utils\Database;

class User {
    private $pdo;

    public function __construct() {
        // C'est ici que la magie opère :
        // On demande la connexion à notre classe Database.
        $this->pdo = Database::getConnection();
    }

    /**
     * Crée un nouvel utilisateur
     */
    public function create(string $email, string $passwordHash): bool {

        // 2. Génération du Token API (32 octets convertis en hexadécimal = 64 chars)
        $apiToken = bin2hex(random_bytes(32));
        $sql = "INSERT INTO users (email, password_hash, api_token) VALUES (:email, :hash, :token)";
        $stmt = $this->pdo->prepare($sql);
        
        try {
            return $stmt->execute([
                ':email' => $email,
                ':hash' => $passwordHash,
                ':token' => $apiToken
            ]);
        } catch (\PDOException $e) {
            // Code 23000 = Violation de contrainte d'intégrité (Email déjà pris)
            if ($e->getCode() == 23000) {
                return false; 
            }
            throw $e;
        }
    }

    /**
     * Trouve un utilisateur par son email
     */
    public function findByEmail(string $email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        
        // Retourne un tableau associatif ou false si non trouvé
        return $stmt->fetch();
    }

    /**
     * Trouve un utilisateur par son token API
     */
    public function findByToken(string $token)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE api_token = :token LIMIT 1");
        $stmt->execute(['token' => $token]);
        return $stmt->fetch();
    }
    
    /**
     * Met à jour le mot de passe hashé d'un utilisateur
     */
    public function updatePassword(int $userId, string $newHash): bool {
        $sql = "UPDATE users SET password_hash = :hash WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':hash' => $newHash,
            ':id'   => $userId
        ]);
    }

    /**
     * Trouve un utilisateur par son ID (Nécessaire pour vérifier l'ancien mot de passe)
     */
    public function findById(int $id) {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}