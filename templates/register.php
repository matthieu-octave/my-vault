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

</body>
</html>