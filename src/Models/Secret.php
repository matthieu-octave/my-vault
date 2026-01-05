<?php

namespace App\Models;

use PDO;
use App\Utils\Database;

class Secret {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Récupère tous les secrets d'un utilisateur
     */
    public function getAllByUser(int $userId): array {
        $sql = "SELECT * FROM secrets WHERE user_id = :user_id ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Ajoute un nouveau secret
     */
    public function create(int $userId, string $title, string $login, string $encryptedPassword): bool {
        $sql = "INSERT INTO secrets (user_id, title, login, encrypted_password) 
                VALUES (:user_id, :title, :login, :enc_pass)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':title' => $title,
            ':login' => $login,
            ':enc_pass' => $encryptedPassword
        ]);
    }
}