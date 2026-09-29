<?php
// ============================================
//  UTILISATEUR - Consultation du résultat
// ============================================
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../index.php');
    exit;
}

require_once '../config/db.php';
$conn = getConnexion();

$resultat   = null;
$erreur     = '';
$matricule  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricule = strtoupper(trim($_POST['matricule'] ?? ''));

    if (!$matricule) {
        $erreur = 'Veuillez entrer votre matricule.';
    } else {
        // Récupérer le candidat et ses notes
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
            $erreur = "Aucun résultat trouvé pour le matricule <strong>$matricule</strong>. Vérifiez votre saisie.";
        } else {
            $row = $res->fetch_assoc();

            // Calcul des moyennes
            $moy_info    = ($row['info_ecrit'] + $row['info_oral']) / 2;
            $moy_anglais = ($row['anglais_ecrit'] + $row['anglais_oral']) / 2;

            // Formule : (Moy_Info × 2 + Moy_Anglais) / 3
            $moy_generale = ($moy_info * 2 + $moy_anglais) / 3;

            $resultat = [
                'matricule'    => $row['matricule'],
                'nom'          => $row['nom'],
                'prenoms'      => $row['prenoms'],
                'filiere'      => $row['filiere'],
                'info_ecrit'   => $row['info_ecrit'],
                'info_oral'    => $row['info_oral'],
                'anglais_ecrit'=> $row['anglais_ecrit'],
                'anglais_oral' => $row['anglais_oral'],
                'moy_info'     => round($moy_info, 2),
                'moy_anglais'  => round($moy_anglais, 2),
                'moy_generale' => round($moy_generale, 2),
                'admis'        => ($moy_generale >= 10),
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Résultat – Concours  CI</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="header">
    <div class="logo">🎓 Administration</div>
    <nav>
        <a href="../logout.php">🚪 Déconnexion</a>
    </nav>
</div>

<div class="container-sm">

    <!-- Formulaire de recherche -->
    <div class="card" style="margin-bottom:24px;">
        <div class="card-title">🔍 Consulter mon résultat</div>
        <p style="color:#64748b;font-size:0.9rem;margin-bottom:20px;">
            Entrez votre numéro de matricule pour voir votre résultat au concours.
        </p>

        <?php if ($erreur): ?>
            <div class="alert alert-error">⚠️ <?= $erreur ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="matricule">Votre Matricule</label>
                <input type="text" id="matricule" name="matricule"
                       placeholder="Ex: C001"
                       value="<?= htmlspecialchars($matricule) ?>"
                       style="text-transform:uppercase;" required>
            </div>
            <button type="submit" class="btn btn-primary btn-full">
                👁️ Voir mon résultat
            </button>
        </form>
    </div>

    <!-- Résultat -->
    <?php if ($resultat): ?>
    <div class="card">
        <div class="result-box">
            <div style="font-size:0.85rem;color:#64748b;text-transform:uppercase;letter-spacing:0.08em;">
                <?= htmlspecialchars($resultat['matricule']) ?> — <?= htmlspecialchars($resultat['filiere']) ?>
            </div>
            <div style="font-size:1.3rem;font-weight:700;color:#1a3a5c;margin:8px 0;">
                <?= htmlspecialchars($resultat['nom'] . ' ' . $resultat['prenoms']) ?>
            </div>

            <div class="result-badge <?= $resultat['admis'] ? 'admis' : 'refuse' ?>">
                <?= $resultat['admis'] ? '🎉 ADMIS(E)' : '❌ REFUSÉ(E)' ?>
            </div>

            <div class="result-moyenne">
                Moyenne générale : <strong><?= $resultat['moy_generale'] ?> / 20</strong>
            </div>

            <?php if ($resultat['admis']): ?>
                <p style="margin-top:20px;color:#16a34a;font-size:0.9rem;">
                    Félicitations ! Vous avez été admis(e) au concours.
                </p>
            <?php else: ?>
                <p style="margin-top:20px;color:#64748b;font-size:0.9rem;">
                    Votre moyenne est inférieure à 10. Vous n'êtes pas admis(e) cette année.
                </p>
                <!-- Lien visible uniquement en cas de refus -->
                <div style="margin-top:20px;">
                    <a href="notes.php?matricule=<?= urlencode($resultat['matricule']) ?>"
                       class="btn btn-outline">
                        📋 Voir le détail de mes notes
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

</div>


</body>
</html>
<?php $conn->close(); ?>
