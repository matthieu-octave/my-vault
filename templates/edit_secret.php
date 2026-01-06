<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un secret</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        ✏️ Modifier : <?= htmlspecialchars($secret['title']) ?>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?= \App\Utils\Security::getCsrfToken() ?>">

                            <div class="mb-3">
                                <label>Titre</label>
                                <input type="text" name="title" class="form-control" 
                                       value="<?= htmlspecialchars($secret['title']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label>Login</label>
                                <input type="text" name="login" class="form-control" 
                                       value="<?= htmlspecialchars($secret['login']) ?>">
                            </div>
                            <div class="mb-3">
                                <label>Mot de passe</label>
                                <input type="text" name="password" class="form-control" 
                                       value="<?= htmlspecialchars($decryptedPassword) ?>" required>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="dashboard" class="btn btn-secondary">Annuler</a>
                                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>