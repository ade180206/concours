<?php
// ============================================
//  UTILISATEUR - Détail des notes (refusés)
// ============================================
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../index.php');
    exit;
}

require_once '../config/db.php';
$conn = getConnexion();

$matricule = strtoupper(trim($_GET['matricule'] ?? ''));

if (!$matricule) {
    header('Location: accueil.php');
    exit;
}

// Récupérer les données
$stmt = $conn->prepare("
    SELECT c.matricule, c.nom, c.prenoms, c.filiere,
           n.info_ecrit, n.info_oral, n.anglais_ecrit, n.anglais_oral
    FROM candidats c
    JOIN notes n ON c.matricule = n.matricule
    WHERE c.matricule = ?
");
$stmt->bind_param('s', $matricule);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    header('Location: accueil.php');
    exit;
}

$row = $res->fetch_assoc();

$moy_info     = ($row['info_ecrit'] + $row['info_oral']) / 2;
$moy_anglais  = ($row['anglais_ecrit'] + $row['anglais_oral']) / 2;
$moy_generale = ($moy_info * 2 + $moy_anglais) / 3;

// Sécurité : si admis, pas de détail visible
if ($moy_generale >= 10) {
    header('Location: accueil.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Notes – Concours  CI</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="header">
    <div class="logo">🎓 Administration</div>
    <nav>
        <a href="accueil.php">🔍 Retour</a>
        <a href="../logout.php">🚪 Déconnexion</a>
    </nav>
</div>

<div class="container-sm">
    <div class="card">
        <div class="card-title">📋 Détail de mes Notes</div>

        <!-- Infos candidat -->
        <div style="background:#f0f4f8;border-radius:8px;padding:16px 20px;margin-bottom:24px;">
            <div style="font-size:0.8rem;color:#64748b;text-transform:uppercase;letter-spacing:0.08em;">Candidat</div>
            <div style="font-size:1.1rem;font-weight:700;color:#1a3a5c;margin-top:4px;">
                <?= htmlspecialchars($row['nom'] . ' ' . $row['prenoms']) ?>
            </div>
            <div style="font-size:0.85rem;color:#64748b;margin-top:2px;">
                Matricule : <strong><?= htmlspecialchars($row['matricule']) ?></strong> &bull;
                Filière : <strong><?= htmlspecialchars($row['filiere']) ?></strong>
            </div>
        </div>

        <!-- Notes informatique -->
        <div class="section-label">💻 Informatique</div>
        <table style="margin-bottom:20px;">
            <thead>
                <tr>
                    <th>Épreuve</th>
                    <th>Note</th>
                    <th>/ 20</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Écrit</td>
                    <td><strong><?= $row['info_ecrit'] ?></strong></td>
                    <td style="color:#94a3b8;">20</td>
                </tr>
                <tr>
                    <td>Oral</td>
                    <td><strong><?= $row['info_oral'] ?></strong></td>
                    <td style="color:#94a3b8;">20</td>
                </tr>
                <tr style="background:#eff6ff;">
                    <td><strong>Moyenne Informatique</strong></td>
                    <td><strong style="color:#2563eb;"><?= round($moy_info, 2) ?></strong></td>
                    <td style="color:#94a3b8;">20</td>
                </tr>
            </tbody>
        </table>

        <!-- Notes anglais -->
        <div class="section-label">🌍 Anglais</div>
        <table style="margin-bottom:20px;">
            <thead>
                <tr>
                    <th>Épreuve</th>
                    <th>Note</th>
                    <th>/ 20</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Écrit</td>
                    <td><strong><?= $row['anglais_ecrit'] ?></strong></td>
                    <td style="color:#94a3b8;">20</td>
                </tr>
                <tr>
                    <td>Oral</td>
                    <td><strong><?= $row['anglais_oral'] ?></strong></td>
                    <td style="color:#94a3b8;">20</td>
                </tr>
                <tr style="background:#eff6ff;">
                    <td><strong>Moyenne Anglais</strong></td>
                    <td><strong style="color:#2563eb;"><?= round($moy_anglais, 2) ?></strong></td>
                    <td style="color:#94a3b8;">20</td>
                </tr>
            </tbody>
        </table>

        <!-- Moyenne générale -->
        <div style="background:#fef2f2;border-radius:8px;padding:16px 20px;text-align:center;border:2px solid #dc2626;">
            <div style="font-size:0.85rem;color:#64748b;text-transform:uppercase;letter-spacing:0.08em;">Moyenne Générale</div>
            <div style="font-size:2rem;font-weight:700;color:#dc2626;margin:6px 0;">
                <?= round($moy_generale, 2) ?> <span style="font-size:1rem;font-weight:400;">/ 20</span>
            </div>
            <div style="font-size:0.8rem;color:#64748b;">
                Formule : (Moy. Info × 2 + Moy. Anglais) ÷ 3
            </div>
        </div>

        <div style="margin-top:24px;text-align:center;">
            <a href="accueil.php" class="btn btn-outline">← Retour à la recherche</a>
        </div>
    </div>
</div>


</body>
</html>
<?php $conn->close(); ?>
