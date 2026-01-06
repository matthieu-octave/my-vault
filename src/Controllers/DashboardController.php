<?php

namespace App\Controllers;


use App\Utils\Security;
use App\Utils\Crypto;
use App\Models\Secret;

class DashboardController
{

    public function index()
    {
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

            // --- DÉBUT VERIF CSRF ---
            $token = $_POST['csrf_token'] ?? '';
            if ($token !== Security::getCsrfToken()) {
                // Arrêt immédiat du script avec un message d'erreur (ou redirection)
                die("Erreur de sécurité : Jeton CSRF invalide !");
            }
            // --- FIN VERIF CSRF ---

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

    public function logout()
    {
        Security::logout();
        header('Location: login');
        exit;
    }

    public function delete() {
        if (!Security::isLogged()) header('Location: login') && exit;

        // On récupère l'ID depuis l'URL (ex: /delete?id=4)
        $id = $_GET['id'] ?? null;
        $userId = $_SESSION['user_id'];

        if ($id) {
            $secretModel = new Secret();
            // On tente la suppression (la méthode delete vérifie déjà le user_id)
            $secretModel->delete((int)$id, $userId);
        }

        // Retour au dashboard
        header('Location: dashboard');
        exit;
    }

    public function edit() {
        if (!Security::isLogged()) header('Location: login') && exit;

        $id = $_GET['id'] ?? null;
        $userId = $_SESSION['user_id'];
        $secretModel = new Secret();
        $key = hex2bin(APP_KEY_HEX);

        // 1. Récupérer le secret pour pré-remplir le formulaire
        $secret = $secretModel->getOne((int)$id, $userId);

        if (!$secret) {
            // Si le secret n'existe pas ou n'est pas à moi -> Oust !
            header('Location: dashboard');
            exit;
        }

        // 2. Traitement du formulaire de modification
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérif CSRF (Bonus précédent)
            if (($_POST['csrf_token'] ?? '') !== Security::getCsrfToken()) die("Erreur CSRF");

            $title = $_POST['title'];
            $login = $_POST['login'];
            $rawPassword = $_POST['password'];

            if ($title && $rawPassword) {
                $encryptedPayload = Crypto::encrypt($rawPassword, $key);
                $secretModel->update($id, $userId, $title, $login, $encryptedPayload);
                
                header('Location: dashboard');
                exit;
            }
        }

        // 3. Pour l'affichage, on a besoin du mot de passe en clair pour le mettre dans le champ
        $decryptedPassword = Crypto::decrypt($secret['encrypted_password'], $key);

        // On charge une vue spécifique pour l'édition
        require_once __DIR__ . '/../../templates/edit_secret.php';
    }
}
