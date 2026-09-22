<?php
// ============================================
//  PAGE DE CONNEXION - index.php
// ============================================
session_start();

// Déjà connecté ?
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/accueil.php');
    } else {
        header('Location: user/accueil.php');
    }
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $erreur = 'Veuillez remplir tous les champs.';
    } elseif ($username === 'admin' && $password === 'admin') {
        // Connexion admin
        $_SESSION['role']     = 'admin';
        $_SESSION['username'] = 'admin';
        header('Location: admin/accueil.php');
        exit;
    } else {
        // Connexion utilisateur
        $_SESSION['role']     = 'user';
        $_SESSION['username'] = $username;
        header('Location: user/accueil.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion – Concours PIGIER CI</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container-sm">
    <div class="card">
        <div class="login-header">
            <div class="school">🎓 PIGIER CI – Abidjan Plateau</div>
            <h1>Espace Concours</h1>
            <p>Connectez-vous pour accéder à votre espace</p>
        </div>

        <?php if ($erreur): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username"
                       placeholder="Entrez votre identifiant"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       autocomplete="username" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password"
                       placeholder="Entrez votre mot de passe"
                       autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-full">
                🔐 Se connecter
            </button>
        </form>
    </div>

    <div class="footer">
        Année académique 2025-2026 &bull; DEV WEB Dynamique &bull; RGL2E &bull; Prof. M. WADJA
    </div>
</div> 


</body>
</html>
