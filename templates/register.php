<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - My Secure Vault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-md-6 col-lg-5">

                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0">🔐 My Secure Vault</h4>
                        <small>Création de compte</small>
                    </div>

                    <div class="card-body p-4">

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Erreur :</strong> <?= htmlspecialchars($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($success)): ?>
                            <div class="alert alert-success" role="alert">
                                <strong>Succès !</strong> <?= htmlspecialchars($success) ?>
                                <hr>
                                <a href="/login" class="btn btn-success w-100">Se connecter maintenant</a>
                            </div>
                        <?php else: ?>
                            <form method="POST" action="">
                                <input type="hidden" name="csrf_token" value="<?= \App\Utils\Security::getCsrfToken() ?>">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Adresse Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="nom@exemple.com" required>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Mot de passe Maître</label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Choisissez un mot de passe fort" required>
                                    <div class="form-text text-muted">
                                        Ce mot de passe chiffrera vos données. Ne l'oubliez pas !
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="progress" style="height: 5px;">
                                        <div id="password-strength" class="progress-bar" role="progressbar" style="width: 0%; transition: width 0.3s;"></div>
                                    </div>
                                    <small id="strength-text" class="text-muted d-block text-end mt-1" style="font-size: 0.8em;">Force : Vide</small>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">S'inscrire</button>
                                </div>
                            </form>

                        <?php endif; ?>
                    </div>

                    <div class="card-footer text-center py-3 bg-white">
                        <p class="mb-0">Déjà un compte ? <a href="/login" class="text-primary fw-bold text-decoration-none">Se connecter</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('password-strength');
    const strengthText = document.getElementById('strength-text');

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;

        // 1. Calcul du score (0 à 4)
        if (password.length >= 8) strength++;           // Critère longueur
        if (password.match(/[A-Z]/)) strength++;        // Critère majuscule
        if (password.match(/[0-9]/)) strength++;        // Critère chiffre
        if (password.match(/[^a-zA-Z0-9]/)) strength++; // Critère caractère spécial

        // 2. Gestion de l'affichage (Couleur & Largeur & Texte)
        let width = '0%';
        let colorClass = '';
        let message = '';

        switch (strength) {
            case 0:
            case 1:
                width = '25%';
                colorClass = 'bg-danger'; // Rouge
                message = 'Très faible';
                break;
            case 2:
                width = '50%';
                colorClass = 'bg-warning'; // Orange
                message = 'Moyen';
                break;
            case 3:
                width = '75%';
                colorClass = 'bg-info'; // Bleu clair
                message = 'Bon';
                break;
            case 4:
                width = '100%';
                colorClass = 'bg-success'; // Vert
                message = 'Excellent !';
                break;
        }

        // Cas particulier : champ vide
        if (password.length === 0) {
            width = '0%';
            message = 'Vide';
            colorClass = '';
        }

        // 3. Application des modifications au DOM
        strengthBar.style.width = width;
        
        // On retire toutes les classes de couleur possibles pour mettre la bonne
        strengthBar.className = 'progress-bar ' + colorClass;
        
        strengthText.textContent = 'Force : ' + message;
        
        // Bonus : changer la couleur du texte aussi
        strengthText.className = 'd-block text-end mt-1 ' + (strength >= 3 ? 'text-success' : 'text-muted');
    });
});
</script>
</body>

</html>