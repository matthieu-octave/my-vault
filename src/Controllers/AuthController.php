<?php

namespace App\Controllers;

use App\Models\User;
use App\Utils\Security;
use App\Utils\PasswordValidator;

class AuthController
{

    public function register()
    {
        $error = null;
        $success = null;

        // Si le formulaire est soumis (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // --- DÉBUT VERIF CSRF ---
            $token = $_POST['csrf_token'] ?? '';
            if ($token !== Security::getCsrfToken()) {
                // Arrêt immédiat du script avec un message d'erreur (ou redirection)
                die("Erreur de sécurité : Jeton CSRF invalide !");
            }
            // --- FIN VERIF CSRF ---
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (!$email || empty($password)) {
                $error = "Veuillez remplir tous les champs.";
            } 
            // 2. On utilise notre validateur TDD pour la force du mot de passe
            elseif (!PasswordValidator::validate($password)) {
                $error = "Le mot de passe est trop faible (min 8 caractères et 1 chiffre).";
            }
            else {
                $userModel = new User();

                // 3. Le reste de la logique reste identique (Vérif email + Hash + Création)
                if ($userModel->findByEmail($email)) {
                    $error = "Cet email est déjà utilisé.";
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    if ($userModel->create($email, $hash)) {
                        $success = "Compte créé avec succès !";
                    } else {
                        $error = "Erreur lors de la création du compte.";
                    }
                }
            }
        }

        // Chargement de la Vue (en passant les variables $error et $success)
        require_once __DIR__ . '/../../templates/register.php';
    }

    public function login()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // --- DÉBUT VERIF CSRF ---
            $token = $_POST['csrf_token'] ?? '';
            if ($token !== Security::getCsrfToken()) {
                // Arrêt immédiat du script avec un message d'erreur (ou redirection)
                die("Erreur de sécurité : Jeton CSRF invalide !");
            }
            // --- FIN VERIF CSRF ---
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if ($email && $password) {
                $userModel = new \App\Models\User();

                // 1. On cherche l'utilisateur en BDD
                $user = $userModel->findByEmail($email);

                // 2. Vérification du mot de passe (Hash vs Clair)
                if ($user && password_verify($password, $user['password_hash'])) {

                    // 3. SUCCÈS : On démarre la session sécurisée
                    // On utilise notre classe Security !
                    \App\Utils\Security::login($user['id']);

                    // 4. Redirection vers le tableau de bord (Dashboard)
                    // (On créera cette page juste après)
                    header('Location: dashboard');
                    exit;
                } else {
                    // Sécurité : Message vague pour ne pas dire si c'est l'email ou le mdp qui est faux
                    $error = "Identifiants incorrects.";
                }
            } else {
                $error = "Veuillez remplir tous les champs.";
            }
        }

        require_once __DIR__ . '/../../templates/login.php';
    }


    public function profile()
    {
        // 1. Vérifier si connecté
        if (!Security::isLogged()) {
            header('Location: login');
            exit;
        }

        $error = null;
        $success = null;
        $userModel = new User();
        $userId = $_SESSION['user_id'];

        // 2. Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // --- DÉBUT VERIF CSRF ---
            $token = $_POST['csrf_token'] ?? '';
            if ($token !== Security::getCsrfToken()) {
                // Arrêt immédiat du script avec un message d'erreur (ou redirection)
                die("Erreur de sécurité : Jeton CSRF invalide !");
            }
            // --- FIN VERIF CSRF ---
            $oldPwd = $_POST['old_password'] ?? '';
            $newPwd = $_POST['new_password'] ?? '';
            $confPwd = $_POST['confirm_password'] ?? '';

            if ($oldPwd && $newPwd && $confPwd) {
                // A. On récupère les infos de l'utilisateur actuel
                $currentUser = $userModel->findById($userId);

                // B. Vérifications
                if (!password_verify($oldPwd, $currentUser['password_hash'])) {
                    $error = "L'ancien mot de passe est incorrect.";
                } elseif ($newPwd !== $confPwd) {
                    $error = "Les nouveaux mots de passe ne correspondent pas.";
                } elseif (!PasswordValidator::validate($newPwd)) {
                    $error = "Le nouveau mot de passe est trop faible (min 8 car. + 1 chiffre).";
                } else {
                    // C. Tout est bon : On hache et on sauvegarde
                    $newHash = password_hash($newPwd, PASSWORD_DEFAULT);

                    if ($userModel->updatePassword($userId, $newHash)) {
                        $success = "Mot de passe modifié avec succès !";
                    } else {
                        $error = "Erreur technique lors de la mise à jour.";
                    }
                }
            } else {
                $error = "Veuillez remplir tous les champs.";
            }
        }

        // 3. Affichage de la vue
        require_once __DIR__ . '/../../templates/profile.php';
    }
}
