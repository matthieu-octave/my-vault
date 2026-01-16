<?php
namespace App\Utils;

class PasswordValidator {
public static function validate(string $password): bool {
        if (strlen($password) < 8) {
            return false;
        }
        // Vérifie la présence d'un chiffre (Regex)
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }
        
        return true;
    }
}