<?php

namespace App\Controllers;


use App\Utils\Security;
use App\Utils\Crypto; 
use App\Models\Secret;

class DashboardController {

    public function index() {
        // 1. Vérifier si on est connecté (Sinon -> Login)
        if (!Security::isLogged()) {
            header('Location: login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $secretModel = new Secret();
        $error = null;
        $success = null;

        // 2. Traitement du formulaire d'AJOUT de secret
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $login = $_POST['login'];
            $rawPassword = $_POST['password'];

            // C'est ICI que la magie opère : CHIFFREMENT 🔒
            $key = hex2bin(APP_KEY_HEX);

            if ($title && $rawPassword) {
                // On chiffre le mot de passe avant de l'envoyer au Modèle
                $encryptedPayload = Crypto::encrypt($rawPassword, $key);
                
                if ($secretModel->create($userId, $title, $login, $encryptedPayload)) {
                    $success = "Secret ajouté et chiffré avec succès !";
                } else {
                    $error = "Erreur lors de l'enregistrement.";
                }
            }
        }

        // 3. Récupération des secrets pour l'affichage
        $secrets = $secretModel->getAllByUser($userId);

        // 4. Chargement de la vue
        require_once __DIR__ . '/../../templates/dashboard.php';
    }

    public function logout() {
        Security::logout();
        header('Location: login');
        exit;
    }
}