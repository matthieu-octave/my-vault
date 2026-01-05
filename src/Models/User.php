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
        $sql = "INSERT INTO users (email, password_hash) VALUES (:email, :hash)";
        $stmt = $this->pdo->prepare($sql);
        
        try {
            return $stmt->execute([
                ':email' => $email,
                ':hash' => $passwordHash
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
}