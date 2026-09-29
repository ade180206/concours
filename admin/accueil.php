<?php
// ============================================
//  ADMIN - Page d'accueil
// ============================================
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil Admin – Concours  CI</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="header">
    <div class="logo">🎓  Administration</div>
    <nav>
        <a href="candidats.php">👤 Candidats</a>
        <a href="notes.php">📝 Saisie de notes</a>
        <a href="../logout.php">🚪 Déconnexion</a>
    </nav>
</div>

<div class="container">
    <div class="card" style="margin-bottom:28px;">
        <div class="card-title">🏠 Tableau de bord Administrateur</div>
        <p style="color:#64748b;margin-bottom:8px;">
            Bienvenue, <strong>Admin</strong>. Utilisez le menu ci-dessous pour gérer les candidats et leurs notes.
        </p>
    </div>

    <div class="menu-grid">
        <a href="candidats.php" class="menu-card">
            <div class="icon">👤</div>
            <h3>Candidats</h3>
            <p>Enregistrer les informations des candidats (matricule, nom, filière…)</p>
        </a>
        <a href="notes.php" class="menu-card">
            <div class="icon">📝</div>
            <h3>Saisie de Notes</h3>
            <p>Saisir les notes d'écrites et d'orales en Informatique et en Anglais</p>
        </a>
    </div>
</div>
</body>
</html>
