<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - My Secure Vault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-md-5">
                
                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-header bg-success text-white text-center py-3">
                        <h4 class="mb-0">🔐 Accès au Coffre</h4>
                    </div>

                    <div class="card-body p-4">
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?= \App\Utils\Security::getCsrfToken() ?>">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required autofocus>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg">Ouvrir le coffre</button>
                            </div>
                        </form>
                    </div>

                    <div class="card-footer text-center py-3 bg-white">
                        <p class="mb-0">Pas encore de compte ? <a href="/register" class="text-success fw-bold text-decoration-none">S'inscrire</a></p>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>