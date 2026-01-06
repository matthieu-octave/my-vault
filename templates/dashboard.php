<?php
$key = hex2bin(APP_KEY_HEX);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mon Coffre - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <span class="navbar-brand mb-0 h1">🔐 My Secure Vault</span>
            <a href="profile" class="btn btn-outline-light btn-sm ms-auto me-2">Profil</a>
            <a href="logout" class="btn btn-outline-danger btn-sm">Déconnexion</a>
        </div>
    </nav>

    <div class="container">

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        ➕ Ajouter un secret
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?= \App\Utils\Security::getCsrfToken() ?>">
                            <div class="mb-3">
                                <label>Titre (ex: Netflix)</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Identifiant / Login</label>
                                <input type="text" name="login" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label>Mot de passe à chiffrer</label>
                                <input type="text" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Chiffrer & Sauvegarder</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        📜 Mes secrets
                    </div>
                    <div class="card-body">
                        <?php if (empty($secrets)): ?>
                            <p class="text-muted text-center">Aucun secret pour l'instant.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <tr>
                                        <th>Titre</th>
                                        <th>Login</th>
                                        <th>Mot de passe</th>
                                        <th class="text-end">Actions</th>
                                    </tr>

                                    <?php foreach ($secrets as $s): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($s['title']) ?></strong></td>
                                            <td><?= htmlspecialchars($s['login']) ?></td>
                                            <td>
                                                <?php
                                                $clear = \App\Utils\Crypto::decrypt($s['encrypted_password'], $key);
                                                ?>
                                                <code class="text-danger bg-light px-2 py-1 rounded">
                                                    <?= htmlspecialchars($clear) ?>
                                                </code>
                                            </td>
                                            <td class="text-end">
                                                <a href="edit?id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary">✏️</a>

                                                <a href="delete?id=<?= $s['id'] ?>"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Voulez-vous vraiment supprimer ce secret ?');">🗑️</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>