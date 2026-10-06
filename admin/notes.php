<?php
// ============================================
//  ADMIN - Saisie des notes
// ============================================
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

require_once '../config/db.php';
$conn = getConnexion();

$message  = '';
$type_msg = '';

// ─── Traitement du formulaire ───
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricule      = trim($_POST['matricule'] ?? '');
    $info_ecrit     = floatval($_POST['info_ecrit'] ?? 0);
    $info_oral      = floatval($_POST['info_oral'] ?? 0);
    $anglais_ecrit  = floatval($_POST['anglais_ecrit'] ?? 0);
    $anglais_oral   = floatval($_POST['anglais_oral'] ?? 0);

    if (!$matricule) {
        $message = 'Veuillez sélectionner un candidat.';
        $type_msg = 'error';
    } elseif ($info_ecrit < 0 || $info_ecrit > 20 || $info_oral < 0 || $info_oral > 20
           || $anglais_ecrit < 0 || $anglais_ecrit > 20 || $anglais_oral < 0 || $anglais_oral > 20) {
        $message = 'Les notes doivent être comprises entre 0 et 20.';
        $type_msg = 'error';
    } else {
        // INSERT ou UPDATE (REPLACE INTO)
        $stmt = $conn->prepare("
            INSERT INTO notes (matricule, info_ecrit, info_oral, anglais_ecrit, anglais_oral)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                info_ecrit = VALUES(info_ecrit),
                info_oral  = VALUES(info_oral),
                anglais_ecrit = VALUES(anglais_ecrit),
                anglais_oral  = VALUES(anglais_oral)
        ");
        $stmt->bind_param('sdddd', $matricule, $info_ecrit, $info_oral, $anglais_ecrit, $anglais_oral);

        if ($stmt->execute()) {
            $message  = "Notes du candidat <strong>$matricule</strong> enregistrées avec succès !";
            $type_msg = 'success';
        } else {
            $message  = 'Erreur : ' . $conn->error;
            $type_msg = 'error';
        }
    }
}

// ─── Liste des candidats pour le select ───
$candidats = $conn->query("SELECT matricule, nom, prenoms FROM candidats ORDER BY matricule ASC");

// ─── Récapitulatif des notes saisies ───
$recapitulatif = $conn->query("
    SELECT c.matricule, c.nom, c.prenoms,
           n.info_ecrit, n.info_oral, n.anglais_ecrit, n.anglais_oral,
           ROUND((n.info_ecrit + n.info_oral) / 2, 2) AS moy_info,
           ROUND((n.anglais_ecrit + n.anglais_oral) / 2, 2) AS moy_anglais
    FROM candidats c
    JOIN notes n ON c.matricule = n.matricule
    ORDER BY c.nom ASC
");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisie des Notes – Concours  CI</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="header">
    <div class="logo">🎓 Administration</div>
    <nav>
        <a href="accueil.php">🏠 Accueil</a>
        <a href="candidats.php">👤 Candidats</a>
        <a href="notes.php" class="active">📝 Notes</a>
        <a href="../logout.php">🚪 Déconnexion</a>
    </nav>
</div>

<div class="container">

    <!-- Formulaire de saisie -->
    <div class="card" style="margin-bottom:28px;">
        <div class="card-title">📝 Saisie des Notes</div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $type_msg === 'success' ? 'success' : 'error' ?>">
                <?= $type_msg === 'success' ? '✅' : '⚠️' ?> <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if ($candidats->num_rows === 0): ?>
            <div class="alert alert-info">
                ℹ️ Aucun candidat enregistré. <a href="candidats.php">Enregistrez d'abord des candidats.</a>
            </div>
        <?php else: ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="matricule">Sélectionner le candidat *</label>
                <select id="matricule" name="matricule" required>
                    <option value="">-- Choisir un candidat --</option>
                    <?php while ($c = $candidats->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($c['matricule']) ?>"
                            <?= (($_POST['matricule'] ?? '') === $c['matricule']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['matricule'] . ' — ' . $c['nom'] . ' ' . $c['prenoms']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Notes Informatique -->
            <div class="section-label">💻 Épreuve d'Informatique</div>
            <div class="notes-grid">
                <div class="form-group">
                    <label for="info_ecrit">Note d'écrit (0 – 20)</label>
                    <input type="number" id="info_ecrit" name="info_ecrit"
                           min="0" max="20" step="0.25" placeholder="Ex: 14"
                           value="<?= htmlspecialchars($_POST['info_ecrit'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="info_oral">Note d'oral (0 – 20)</label>
                    <input type="number" id="info_oral" name="info_oral"
                           min="0" max="20" step="0.25" placeholder="Ex: 12"
                           value="<?= htmlspecialchars($_POST['info_oral'] ?? '') ?>" required>
                </div>
            </div>

            <!-- Notes Anglais -->
            <div class="section-label">🌍 Épreuve d'Anglais</div>
            <div class="notes-grid">
                <div class="form-group">
                    <label for="anglais_ecrit">Note d'écrit (0 – 20)</label>
                    <input type="number" id="anglais_ecrit" name="anglais_ecrit"
                           min="0" max="20" step="0.25" placeholder="Ex: 11"
                           value="<?= htmlspecialchars($_POST['anglais_ecrit'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="anglais_oral">Note d'oral (0 – 20)</label>
                    <input type="number" id="anglais_oral" name="anglais_oral"
                           min="0" max="20" step="0.25" placeholder="Ex: 13"
                           value="<?= htmlspecialchars($_POST['anglais_oral'] ?? '') ?>" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">💾 Enregistrer les notes</button>
        </form>
        <?php endif; ?>
    </div>

    <!-- Récapitulatif -->
    <?php if ($recapitulatif && $recapitulatif->num_rows > 0): ?>
    <div class="card">
        <div class="card-title">📊 Récapitulatif des notes saisies</div>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Candidat</th>
                        <th>Info Écrit</th>
                        <th>Info Oral</th>
                        <th>Moy. Info</th>
                        <th>Ang. Écrit</th>
                        <th>Ang. Oral</th>
                        <th>Moy. Anglais</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($r = $recapitulatif->fetch_assoc()): ?>
                    <tr>
                        <td><code><?= htmlspecialchars($r['matricule']) ?></code></td>
                        <td><?= htmlspecialchars($r['nom'] . ' ' . $r['prenoms']) ?></td>
                        <td><?= $r['info_ecrit'] ?></td>
                        <td><?= $r['info_oral'] ?></td>
                        <td><strong><?= $r['moy_info'] ?></strong></td>
                        <td><?= $r['anglais_ecrit'] ?></td>
                        <td><?= $r['anglais_oral'] ?></td>
                        <td><strong><?= $r['moy_anglais'] ?></strong></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
<?php $conn->close(); ?>
