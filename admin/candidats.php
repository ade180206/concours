<?php
// ============================================
//  ADMIN - Enregistrement des candidats
// ============================================
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

require_once '../config/db.php';
$conn = getConnexion();
$message = '';
$type_msg = '';

// ─── Traitement du formulaire ───
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricule       = trim($_POST['matricule'] ?? '');
    $nom             = trim($_POST['nom'] ?? '');
    $prenoms         = trim($_POST['prenoms'] ?? '');
    $date_naissance  = trim($_POST['date_naissance'] ?? '');
    $filiere         = trim($_POST['filiere'] ?? '');

    if (!$matricule || !$nom || !$prenoms || !$date_naissance || !$filiere) {
        $message  = 'Veuillez remplir tous les champs.';
        $type_msg = 'error';
    } else {
        // Vérifier doublon
        $check = $conn->prepare("SELECT matricule FROM candidats WHERE matricule = ?");
        $check->bind_param('s', $matricule);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message  = "Le matricule <strong>$matricule</strong> existe déjà.";
            $type_msg = 'error';
        } else {
            $stmt = $conn->prepare("INSERT INTO candidats (matricule, nom, prenoms, date_naissance, filiere) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sssss', $matricule, $nom, $prenoms, $date_naissance, $filiere);
            if ($stmt->execute()) {
                $message  = "Candidat <strong>$nom $prenoms</strong> enregistré avec  succès !";
                $type_msg = 'success';
            } else { 
                $message  = 'Erreur lors de l\'enregistrement : ' . $conn->error;
                $type_msg = 'error';
            }
        }
    }
}

// ─── Liste des candidats ───
$liste = $conn->query("SELECT * FROM candidats ORDER BY matricule ASC");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidats – Concours  CI</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="header">
    <div class="logo">🎓 Administration</div>
    <nav>
        <a href="accueil.php">🏠 Accueil</a>
        <a href="candidats.php" class="active">👤 Candidats</a>
        <a href="notes.php">📝 Notes</a>
        <a href="../logout.php">🚪 Déconnexion</a>
    </nav>
</div>

<div class="container">

    <!-- Formulaire d'enregistrement -->
    <div class="card" style="margin-bottom:28px;">
        <div class="card-title">👤 Enregistrer un Candidat</div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $type_msg === 'success' ? 'success' : 'error' ?>">
                <?= $type_msg === 'success' ? '✅' : '⚠️' ?> <?= $message ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-grid">
                <div class="form-group">
                    <label for="matricule">Matricule *</label>
                    <input type="text" id="matricule" name="matricule"
                           placeholder="Ex: C001" required
                           value="<?= htmlspecialchars($_POST['matricule'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="filiere">Filière *</label>
                    <input type="text" id="filiere" name="filiere"
                           placeholder="Ex: Informatique"
                           value="<?= htmlspecialchars($_POST['filiere'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="nom">Nom *</label>
                    <input type="text" id="nom" name="nom"
                           placeholder="Nom de famille"
                           value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="prenoms">Prénoms *</label>
                    <input type="text" id="prenoms" name="prenoms"
                           placeholder="Prénoms"
                           value="<?= htmlspecialchars($_POST['prenoms'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="date_naissance">Date de naissance *</label>
                    <input type="date" id="date_naissance" name="date_naissance"
                           value="<?= htmlspecialchars($_POST['date_naissance'] ?? '') ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">➕ Enregistrer le candidat</button>
        </form>
    </div>

    <!-- Liste des candidats -->
    <div class="card">
        <div class="card-title">📋 Liste des Candidats enregistrés</div>
        <?php if ($liste->num_rows === 0): ?>
            <div class="alert alert-info">ℹ️ Aucun candidat enregistré pour l'instant.</div>
        <?php else: ?>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Matricule</th>
                            <th>Nom</th>
                            <th>Prénoms</th>
                            <th>Date de naissance</th>
                            <th>Filière</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $liste->fetch_assoc()): ?>
                        <tr>
                            <td><code><?= htmlspecialchars($row['matricule']) ?></code></td>
                            <td><?= htmlspecialchars($row['nom']) ?></td>
                            <td><?= htmlspecialchars($row['prenoms']) ?></td>
                            <td><?= date('d/m/Y', strtotime($row['date_naissance'])) ?></td>
                            <td><?= htmlspecialchars($row['filiere']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            
            </div>
        <?php endif; ?>
    </div>

</div>
</body>
</html>
<?php $conn->close(); ?>
