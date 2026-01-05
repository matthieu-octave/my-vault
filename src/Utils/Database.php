<?php

namespace App\Utils;

use PDO;
use PDOException;

class Database {
    // Configuration de la BDD
    private const HOST = HOST;
    private const DB_NAME = DB_NAME;
    private const USER = USER;
    private const PASS = PASS;

    // Variable statique pour stocker l'instance unique (Singleton)
    private static $pdoInstance = null;

    /**
     * Retourne l'instance unique de PDO.
     * Si elle n'existe pas encore, on la crée. Sinon, on renvoie celle qui existe.
     */
    public static function getConnection(): PDO {
        if (self::$pdoInstance === null) {
            try {
                $dsn = "mysql:host=" . self::HOST . ";dbname=" . self::DB_NAME . ";charset=utf8mb4";
                
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];

                self::$pdoInstance = new PDO($dsn, self::USER, self::PASS, $options);
            
            } catch (PDOException $e) {
                // En production, on log l'erreur au lieu de l'afficher
                die("Erreur de connexion BDD : " . $e->getMessage());
            }
        }
        
        return self::$pdoInstance;
    }
}