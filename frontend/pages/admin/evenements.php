<?php
require_once __DIR__ . '/../../components/admin.php';

$pdo = db();
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_csrf();

    if (isset($_POST['supprimer'])) {
        $stmt = $pdo->prepare('SELECT image_path FROM actualite WHERE id = ?');
        $stmt->execute([(int) $_POST['supprimer']]);
        supprimer_image($stmt->fetchColumn() ?: null);
        $pdo->prepare('DELETE FROM actualite WHERE id = ?')->execute([(int) $_POST['supprimer']]);
        $_SESSION['flash'] = 'L\'événement a été supprimé.';
        header('Location: evenements.php');
        exit();
    }

    $debut = $_POST['debut'] ?? '';
    $fin = $_POST['fin'] ?? '';
    $places = (int) ($_POST['places'] ?? 0);

    if (!strtotime($debut) || !strtotime($fin)) {
        $erreurs[] = 'Veuillez indiquer les dates de début et de fin.';
    } elseif ($fin < $debut) {
        $erreurs[] = 'La date de fin doit être après la date de début.';
    }
    if ($places < 1) {
        $erreurs[] = 'Le nombre de places doit être d\'au moins 1.';
    }
    if (empty($_FILES['affiche']['name'])) {
        $erreurs[] = 'Veuillez importer une affiche.';
    }

    if (!$erreurs) {
        $affiche = enregistrer_image($_FILES['affiche'], 'affiches');
        if ($affiche === null) {
            $erreurs[] = 'L\'affiche doit être une image (JPG, PNG, WebP ou GIF) de moins de 8 Mo.';
        } else {
            // La table actualite n'a pas de colonnes pour les dates et les places :
            // elles sont enregistrées dans le titre et la description.
            $pdo->beginTransaction();
            $stmt = $pdo->prepare('INSERT INTO actualite (id, titre, description, image_path, date_publication, id_user)
                                   VALUES (?, ?, ?, ?, NOW(), ?)');
            $stmt->execute([
                prochain_id($pdo, 'actualite'),
                'Événement du ' . date_fr($debut) . ' au ' . date_fr($fin),
                'Places : ' . $places,
                $affiche,
                $_SESSION['user_id'],
            ]);
            $pdo->commit();
            $_SESSION['flash'] = 'L\'événement a bien été créé.';
            header('Location: evenements.php');
            exit();
        }
    }
}

$evenements = $pdo->query('SELECT id, titre FROM actualite ORDER BY date_publication DESC')->fetchAll();

admin_entete('Événements');
?>
<main class="admin-colonnes">
  <section class="admin-liste">
    <h2>Gérer les événements</h2>
    <?php if (!$evenements): ?>
      <p class="admin-vide">Aucun événement pour le moment.</p>
    <?php endif; ?>
    <ul class="list-unstyled">
      <?php foreach ($evenements as $evenement): ?>
        <li>
          <span><?= htmlspecialchars($evenement['titre']) ?></span>
          <form method="POST" onsubmit="return confirm('Supprimer cet événement ?');">
            <?= champ_csrf() ?>
            <button type="submit" name="supprimer" value="<?= $evenement['id'] ?>" class="admin-supprimer" aria-label="Supprimer cet événement">×</button>
          </form>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>

  <section class="admin-formulaire">
    <h2>Ajouter un événement</h2>
    <?php admin_erreurs($erreurs); ?>
    <form method="POST" enctype="multipart/form-data">
      <?= champ_csrf() ?>
      <div class="admin-ligne">
        <label>Date de début
          <input type="date" name="debut" value="<?= htmlspecialchars($_POST['debut'] ?? '') ?>" required>
        </label>
        <label>Date de fin
          <input type="date" name="fin" value="<?= htmlspecialchars($_POST['fin'] ?? '') ?>" required>
        </label>
      </div>
      <label>Maximum de place
        <input type="number" name="places" min="1" placeholder="0" value="<?= htmlspecialchars($_POST['places'] ?? '') ?>" required>
      </label>
      <div class="admin-champ">
        <span>Choisir une affiche</span>
        <label class="admin-fichier">
          <input type="file" name="affiche" accept="image/*" required
            onchange="this.nextElementSibling.textContent = this.files[0] ? this.files[0].name : 'Importer une affiche';">
          <span>Importer une affiche</span>
        </label>
      </div>
      <button type="submit" class="admin-bouton">Créer l’événement</button>
    </form>
  </section>
</main>
<?php admin_pied(); ?>
