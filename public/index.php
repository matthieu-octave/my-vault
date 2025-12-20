<?php
require_once __DIR__ . '/../config/db.php';
echo "<h1>Mon Coffre-Fort Sécurisé</h1>";
echo "<p>Base de données connectée.</p>";
?>
<!DOCTYPE html>
<h1>Test de Sniffing HTTP</h1>

<form method="POST" action="">
    <input type="text" name="login" placeholder="Login" value="admin"><br>
    <input type="password" name="password" placeholder="Mot de passe" value="SuperSecret123"><br>
    <button type="submit">Se Connecter (Test)</button>
</form>
