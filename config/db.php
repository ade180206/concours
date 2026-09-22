<?php
// ============================================
//  CONFIGURATION BASE DE DONNÉES
//  Modifier si nécessaire selon votre XAMPP
// ============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Utilisateur XAMPP par défaut
define('DB_PASS', '');           // Mot de passe vide par défaut sur XAMPP
define('DB_NAME', 'concours');

function getConnexion() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("
        <div style='font-family:sans-serif;padding:30px;background:#fff0f0;border-left:5px solid #e53e3e;margin:20px;border-radius:8px;'>
            <h2>❌ Erreur de connexion à la base de données</h2>
            <p>" . $conn->connect_error . "</p>
            <p><strong>Vérifiez que :</strong><br>
            ✔ XAMPP est lancé (Apache + MySQL)<br>
            ✔ La base <em>concours</em> est créée via phpMyAdmin<br>
            ✔ Le fichier <em>concours.sql</em> a été importé</p>
        </div>");
    }

    $conn->set_charset('utf8mb4');
    return $conn;
}
?>
