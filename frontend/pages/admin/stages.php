<?php
require_once __DIR__ . '/../../components/admin.php';

require_once __DIR__ . '/../../components/activites.php';

// Activités proposées (sports de base + activités ajoutées) et image associée à chacune
$activites = liste_activites();

$pdo = db();
$animateurs = $pdo->query('SELECT id, username, prenom, nom FROM users ORDER BY prenom, nom, username')->fetchAll();
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_csrf();

    if (isset($_POST['supprimer'])) {
        $pdo->prepare('DELETE FROM stage WHERE id = ?')->execute([(int) $_POST['supprimer']]);
        $_SESSION['flash'] = 'Le stage a été supprimé.';
        header('Location: stages.php');
        exit();
    }

    $debut = $_POST['debut'] ?? '';
    $fin = $_POST['fin'] ?? '';
    $places = (int) ($_POST['places'] ?? 0);
    $choixActivites = array_values(array_intersect(array_keys($activites), $_POST['activites'] ?? []));
    $idsAnimateurs = array_map('intval', $_POST['animateurs'] ?? []);
    $nomsAnimateurs = [];
    foreach ($animateurs as $a) {
        if (in_array((int) $a['id'], $idsAnimateurs, true)) {
            $nomsAnimateurs[] = nom_animateur($a);
        }
    }

    if (!strtotime($debut) || !strtotime($fin)) {
        $erreurs[] = 'Veuillez indiquer les dates de début et de fin.';
    } elseif ($fin < $debut) {
        $erreurs[] = 'La date de fin doit être après la date de début.';
    }
    if ($places < 1) {
        $erreurs[] = 'Le nombre de places doit être d\'au moins 1.';
    }
    if (!$choixActivites) {
        $erreurs[] = 'Veuillez choisir au moins une activité.';
    }
    if (!$nomsAnimateurs) {
        $erreurs[] = 'Veuillez choisir au moins un animateur.';
    }

    if (!$erreurs) {
        $pdo->beginTransaction();
        $idStage = prochain_id($pdo, 'stage');
        $stmt = $pdo->prepare('INSERT INTO stage (id, debut, fin, intitule, description, places_diponibles, image_path, niveau_scolaire, id_user)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $idStage,
            $debut,
            $fin,
            implode(', ', $choixActivites),
            'Animateurs : ' . implode(', ', $nomsAnimateurs),
            $places,
            $activites[$choixActivites[0]]['image'],
            '',
            $_SESSION['user_id'],
        ]);
        $pdo->commit();
        $_SESSION['flash'] = 'Le stage a bien été créé.';
        header('Location: ../stages.php#stage-' . $idStage);
        exit();
    }
}

$stages = $pdo->query('SELECT id, debut, fin, intitule FROM stage ORDER BY debut DESC')->fetchAll();

admin_entete('Stages');
?>
<main class="admin-colonnes">
  <section class="admin-liste">
    <h2>Gérer les stages</h2>
    <?php if (!$stages): ?>
      <p class="admin-vide">Aucun stage pour le moment.</p>
    <?php endif; ?>
    <ul class="list-unstyled">
      <?php foreach ($stages as $stage): ?>
        <li title="<?= htmlspecialchars($stage['intitule']) ?>">
          <span>Stage du <?= date_fr($stage['debut']) ?> au <?= date_fr($stage['fin']) ?></span>
          <form method="POST" onsubmit="return confirm('Supprimer ce stage ?');">
            <?= champ_csrf() ?>
            <button type="submit" name="supprimer" value="<?= $stage['id'] ?>" class="admin-supprimer" aria-label="Supprimer ce stage">×</button>
          </form>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>

  <section class="admin-formulaire">
    <h2>Ajouter un stage</h2>
    <?php admin_erreurs($erreurs); ?>
    <form method="POST">
      <?= champ_csrf() ?>
      <div class="admin-ligne">
        <label>Date de début
          <input type="date" name="debut" value="<?= htmlspecialchars($_POST['debut'] ?? '') ?>" required>
        </label>
        <label>Date de fin
          <input type="date" name="fin" value="<?= htmlspecialchars($_POST['fin'] ?? '') ?>" required>
        </label>
      </div>
      <div class="admin-ligne">
        <label>Maximum de place
          <input type="number" name="places" min="1" placeholder="0" value="<?= htmlspecialchars($_POST['places'] ?? '') ?>" required>
        </label>
        <div class="admin-champ">
          <span>Activités</span>
          <details class="choix">
            <summary>Veuillez choisir les activités</summary>
            <div class="choix-options">
              <?php foreach (array_keys($activites) as $nom): ?>
                <label><input type="checkbox" name="activites[]" value="<?= htmlspecialchars($nom) ?>"
                    <?= in_array($nom, $_POST['activites'] ?? [], true) ? 'checked' : '' ?>> <?= htmlspecialchars($nom) ?></label>
              <?php endforeach; ?>
            </div>
          </details>
        </div>
      </div>
      <div class="admin-champ">
        <span>Nom des animateurs <a href="animateurs.php" class="small text-reset">(gérer)</a></span>
        <details class="choix">
          <summary>Veuillez choisir les animateurs</summary>
          <div class="choix-options">
            <?php foreach ($animateurs as $a): ?>
              <label><input type="checkbox" name="animateurs[]" value="<?= $a['id'] ?>"
                  <?= in_array((string) $a['id'], $_POST['animateurs'] ?? [], true) ? 'checked' : '' ?>> <?= htmlspecialchars(nom_animateur($a)) ?></label>
            <?php endforeach; ?>
          </div>
        </details>
      </div>
      <button type="submit" class="admin-bouton">Créer le stage</button>
    </form>
  </section>
</main>
<?php admin_pied(); ?>
