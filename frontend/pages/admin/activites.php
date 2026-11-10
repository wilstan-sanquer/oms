<?php
require_once __DIR__ . '/../../components/admin.php';
require_once __DIR__ . '/../../components/activites.php';

$pdo = db();
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_csrf();

    if (isset($_POST['supprimer'])) {
        $stmt = $pdo->prepare('SELECT image_path FROM activite WHERE id = ?');
        $stmt->execute([(int) $_POST['supprimer']]);
        supprimer_image($stmt->fetchColumn() ?: null);
        $pdo->prepare('DELETE FROM activite WHERE id = ?')->execute([(int) $_POST['supprimer']]);
        $_SESSION['flash'] = 'L\'activité a été supprimée.';
        header('Location: activites.php');
        exit();
    }

    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($nom === '') {
        $erreurs[] = 'Veuillez donner un nom à l\'activité.';
    } elseif (array_key_exists(mb_strtolower($nom), array_change_key_case(liste_activites()))) {
        $erreurs[] = 'Une activité porte déjà ce nom.';
    }
    if ($description === '') {
        $erreurs[] = 'Veuillez décrire l\'activité.';
    }
    if (empty($_FILES['image']['name'])) {
        $erreurs[] = 'Veuillez importer une photo.';
    }

    if (!$erreurs) {
        $image = enregistrer_image($_FILES['image'], 'activites');
        if ($image === null) {
            $erreurs[] = 'La photo doit être une image (JPG, PNG, WebP ou GIF) de moins de 8 Mo.';
        } else {
            $pdo->prepare('INSERT INTO activite (nom, description, image_path, date_publication, id_user) VALUES (?, ?, ?, NOW(), ?)')
                ->execute([$nom, $description, $image, $_SESSION['user_id']]);
            $_SESSION['flash'] = 'L\'activité « ' . $nom . ' » a bien été ajoutée.';
            header('Location: ../activity/activite.php?id=' . $pdo->lastInsertId());
            exit();
        }
    }
}

$activites = $pdo->query('SELECT id, nom FROM activite ORDER BY date_publication DESC, id DESC')->fetchAll();

admin_entete('Activités');
?>
<main class="admin-colonnes">
  <section class="admin-liste">
    <h2>Gérer les activités</h2>
    <?php if (!$activites): ?>
      <p class="admin-vide">Aucune activité ajoutée pour le moment.</p>
    <?php endif; ?>
    <ul class="list-unstyled">
      <?php foreach ($activites as $activite): ?>
        <li>
          <a href="../activity/activite.php?id=<?= $activite['id'] ?>" class="text-reset text-decoration-none"><?= htmlspecialchars($activite['nom']) ?></a>
          <form method="POST" onsubmit="return confirm('Supprimer cette activité ?');">
            <?= champ_csrf() ?>
            <button type="submit" name="supprimer" value="<?= $activite['id'] ?>" class="admin-supprimer" aria-label="Supprimer cette activité">×</button>
          </form>
        </li>
      <?php endforeach; ?>
    </ul>
    <p class="admin-vide mt-4">Les 8 sports de base (football, basketball…) ont leur propre page et ne sont pas listés ici.</p>
  </section>

  <section class="admin-formulaire">
    <h2>Ajouter une activité</h2>
    <?php admin_erreurs($erreurs); ?>
    <form method="POST" enctype="multipart/form-data">
      <?= champ_csrf() ?>
      <label>Nom de l’activité
        <input type="text" name="nom" maxlength="100" placeholder="Ex. : Escalade" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
      </label>
      <label>Description
        <textarea name="description" rows="6" placeholder="Présentez l’activité aux familles…" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
      </label>
      <div class="admin-champ">
        <span>Photo de l’activité</span>
        <label class="admin-fichier">
          <input type="file" name="image" accept="image/*" required
            onchange="this.nextElementSibling.textContent = this.files[0] ? this.files[0].name : 'Importer une photo';">
          <span>Importer une photo</span>
        </label>
      </div>
      <button type="submit" class="admin-bouton">Ajouter l’activité</button>
    </form>
  </section>
</main>
<?php admin_pied(); ?>
