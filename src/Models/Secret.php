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

    /**
     * Récupère un secret spécifique (Vérifie qu'il appartient bien à l'utilisateur !)
     */
    public function getOne(int $id, int $userId) {
        $sql = "SELECT * FROM secrets WHERE id = :id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id, ':user_id' => $userId]);
        return $stmt->fetch();
    }

    /**
     * Met à jour un secret
     */
    public function update(int $id, int $userId, string $title, string $login, string $encryptedPassword): bool {
        $sql = "UPDATE secrets 
                SET title = :title, login = :login, encrypted_password = :enc_pass 
                WHERE id = :id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':title'    => $title,
            ':login'    => $login,
            ':enc_pass' => $encryptedPassword,
            ':id'       => $id,
            ':user_id'  => $userId
        ]);
    }

    /**
     * Supprime un secret
     */
    public function delete(int $id, int $userId): bool {
        $sql = "DELETE FROM secrets WHERE id = :id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id, ':user_id' => $userId]);
    }
}