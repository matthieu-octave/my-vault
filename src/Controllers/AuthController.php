<?php

namespace App\Controllers;

use App\Models\User;

class AuthController {
    
    public function register() {
        $error = null;
        $success = null;

        // Si le formulaire est soumis (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if ($email && !empty($password)) {
                $userModel = new User();

                // 1. Vérifier si l'email existe déjà
                if ($userModel->findByEmail($email)) {
                    $error = "Cet email est déjà utilisé.";
                } else {
                    // 2. Hachage du mot de passe
                    $hash = password_hash($password, PASSWORD_DEFAULT);

                    // 3. Création via le Modèle
                    if ($userModel->create($email, $hash)) {
                        $success = "Compte créé avec succès !";
                        // Optionnel : Redirection vers le login
                        // header('Location: /login'); exit;
                    } else {
                        $error = "Erreur lors de la création du compte.";
                    }
                }
            } else {
                $error = "Veuillez remplir tous les champs.";
            }
        }

        // Chargement de la Vue (en passant les variables $error et $success)
        require_once __DIR__ . '/../../templates/register.php';
    }

    public function login() {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
}