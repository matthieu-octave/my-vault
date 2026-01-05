<?php

namespace App\Utils;

class Security {
    
    /**
     * Démarrage sécurisé de la session
     * Empêche le vol de cookie via XSS (HttpOnly) et force le mode Strict
     */
    public static function safeSessionStart() {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,          // Expire à la fermeture du navigateur
                'path' => '/',
                'domain' => '', 
                'secure' => false,        // Mettre à true si vous êtes en HTTPS
                'httponly' => true,       // CRUCIAL : JavaScript ne peut pas lire le cookie
                'samesite' => 'Strict'    // Bloque le CSRF externe
            ]);
            
            session_start();
        }
    }

    /**
     * Connexion de l'utilisateur (Anti-Fixation)
     */
    public static function login($userId) {
        self::safeSessionStart();
        
        // CRUCIAL : On change l'ID de session à chaque connexion
        // Si un pirate avait volé l'ID avant le login, il devient inutile ici.
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $userId;
    }

    /**
     * Vérifie si l'utilisateur est connecté
     */
    public static function isLogged() {
        self::safeSessionStart();
        return isset($_SESSION['user_id']);
    }

    /**
     * Déconnexion propre
     */
    public static function logout() {
        self::safeSessionStart();
        session_unset();
        session_destroy();
    }
}